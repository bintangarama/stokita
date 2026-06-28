<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class SyncToSupabase extends Command
{
    protected $signature = 'db:sync-supabase';
    protected $description = 'Sinkronisasi data dari MySQL lokal ke Supabase PostgreSQL';

    public function handle()
    {
        $this->info('Memulai persiapan sinkronisasi database...');

        // 1. Validasi Environment Variables untuk Supabase
        $host = env('SUPABASE_DB_HOST');
        $database = env('SUPABASE_DB_DATABASE');
        $username = env('SUPABASE_DB_USERNAME');
        $password = env('SUPABASE_DB_PASSWORD');

        if (!$host || !$database || !$username || !$password) {
            $this->error('Kredensial SUPABASE_DB_* tidak lengkap di file .env Anda!');
            $this->warn('Pastikan Anda telah menambahkan konfigurasi berikut ke file .env:');
            $this->line('SUPABASE_DB_HOST=your-supabase-db-host.supabase.co');
            $this->line('SUPABASE_DB_PORT=5432');
            $this->line('SUPABASE_DB_DATABASE=postgres');
            $this->line('SUPABASE_DB_USERNAME=postgres');
            $this->line('SUPABASE_DB_PASSWORD=your-supabase-db-password');
            return 1;
        }

        // 2. Daftarkan koneksi supabase secara dinamis
        config(['database.connections.supabase' => [
            'driver' => 'pgsql',
            'host' => $host,
            'port' => env('SUPABASE_DB_PORT', '5432'),
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'require',
        ]]);

        // 3. Jalankan migrasi bersih di Supabase
        $this->info('Menjalankan migrasi bersih di Supabase (migrate:fresh)...');
        Artisan::call('migrate:fresh', [
            '--database' => 'supabase',
            '--force' => true
        ]);
        $this->info(Artisan::output());

        // 4. Ambil semua tabel dari koneksi mysql lokal
        $tables = Schema::connection('mysql')->getTables();
        $tableNames = collect($tables)->pluck('name')->all();

        $this->info('Menyalin data untuk setiap tabel...');

        foreach ($tableNames as $tableName) {
            // Cek apakah tabel tersebut ada di Supabase
            if (!Schema::connection('supabase')->hasTable($tableName)) {
                $this->warn("Tabel {$tableName} dilewati (tidak ada di skema migrasi Supabase).");
                continue;
            }

            $this->info("Menyalin tabel: {$tableName}");

            $columns = Schema::connection('mysql')->getColumnListing($tableName);
            $firstColumn = !empty($columns) ? $columns[0] : null;

            $query = DB::connection('mysql')->table($tableName);
            if ($firstColumn) {
                $query->orderBy($firstColumn);
            }

            $rowCount = 0;

            try {
                DB::connection('supabase')->transaction(function () use ($query, $tableName, &$rowCount) {
                    // Nonaktifkan foreign key constraints & triggers
                    DB::connection('supabase')->statement("SET session_replication_role = 'replica';");

                    // Hapus data bawaan migrasi jika ada
                    DB::connection('supabase')->table($tableName)->delete();

                    // Salin data dalam chunks
                    $query->chunk(250, function ($rows) use ($tableName, &$rowCount) {
                        $data = [];
                        foreach ($rows as $row) {
                            $data[] = (array) $row;
                        }
                        if (!empty($data)) {
                            DB::connection('supabase')->table($tableName)->insert($data);
                            $rowCount += count($data);
                        }
                    });

                    // Aktifkan kembali constraints & triggers
                    DB::connection('supabase')->statement("SET session_replication_role = 'origin';");
                });

                $this->info("Berhasil menyalin {$rowCount} baris ke tabel {$tableName}");

                // Reset PostgreSQL Sequence agar auto-increment ID berlanjut dengan benar
                if (Schema::connection('supabase')->hasColumn($tableName, 'id')) {
                    try {
                        $maxId = DB::connection('supabase')->table($tableName)->max('id');
                        if ($maxId && is_numeric($maxId)) {
                            DB::connection('supabase')->statement("SELECT setval(pg_get_serial_sequence('{$tableName}', 'id'), {$maxId})");
                        }
                    } catch (\Exception $e) {
                        // Jika kolom id bukan angka (misal UUID) atau sequence tidak ada, abaikan
                    }
                }
            } catch (\Exception $e) {
                $this->error("Gagal menyalin tabel {$tableName}: " . $e->getMessage());
                // Pastikan role dikembalikan ke origin jika terjadi error
                try {
                    DB::connection('supabase')->statement("SET session_replication_role = 'origin';");
                } catch (\Exception $ex) {}
            }
        }

        $this->info('Sinkronisasi database selesai dengan sukses!');
        return 0;
    }
}

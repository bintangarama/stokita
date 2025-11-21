<?php

namespace App\Services;

use App\Models\UnitConversion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UnitConversionService
{
    /**
     * Konversi jumlah antar satuan.
     * 
     * @param float $qty Jumlah yang akan dikonversi
     * @param int $fromUnitId ID satuan asal
     * @param int $toUnitId ID satuan tujuan
     * @return float hasil konversi
     */
    public static function convert(float $qty, int $fromUnitId, int $toUnitId): float
    {
        // Jika sama, tidak perlu konversi
        if ($fromUnitId === $toUnitId) {
            return $qty;
        }

        // 🔹 Gunakan cache untuk mempercepat lookup
        $cacheKey = "unit_conv_{$fromUnitId}_{$toUnitId}";
        $cachedFactor = Cache::get($cacheKey);
        if ($cachedFactor !== null) {
            return $qty * $cachedFactor;
        }

        // 🔸 Cari konversi langsung di tabel
        $conversion = UnitConversion::where('unit_from_id', $fromUnitId)
            ->where('unit_to_id', $toUnitId)
            ->first();

        if ($conversion) {
            Cache::put($cacheKey, $conversion->factor, now()->addDay());
            return $qty * $conversion->factor;
        }

        // 🔸 Coba cari arah sebaliknya (reverse lookup)
        $reverse = UnitConversion::where('unit_from_id', $toUnitId)
            ->where('unit_to_id', $fromUnitId)
            ->first();

        if ($reverse) {
            $reverseFactor = 1 / $reverse->factor;
            Cache::put($cacheKey, $reverseFactor, now()->addDay());
            return $qty * $reverseFactor;
        }

        // 🔸 Jika tetap tidak ditemukan
        Log::warning("[UnitConversionService] Konversi tidak ditemukan: {$fromUnitId} → {$toUnitId}. Nilai asli dipakai.");

        // Cache hasil fallback supaya tidak log terus-menerus
        Cache::put($cacheKey, 1, now()->addHour());

        // 🔸 Return nilai tanpa konversi (fallback aman)
        return $qty;
    }

    /**
     * Hapus cache konversi tertentu (misal setelah update data)
     */
    public static function clearCache(int $fromUnitId, int $toUnitId): void
    {
        Cache::forget("unit_conv_{$fromUnitId}_{$toUnitId}");
    }

    /**
     * Hapus semua cache konversi (misal setelah import konversi massal)
     */
    public static function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Dapatkan faktor konversi antar satuan (tanpa mengalikan qty)
     */
    public static function getFactor(int $fromUnitId, int $toUnitId): float
    {
        return self::convert(1, $fromUnitId, $toUnitId);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['code', 'name'];

    public function conversionsFrom()
    {
        return $this->hasMany(UnitConversion::class, 'unit_from_id');
    }

    public function conversionsTo()
    {
        return $this->hasMany(UnitConversion::class, 'unit_to_id');
    }
}

class UnitConversion extends Model
{
    use HasFactory;

    protected $fillable = ['unit_from_id', 'unit_to_id', 'factor'];

    public function unitFrom()
    {
        return $this->belongsTo(Unit::class, 'unit_from_id');
    }

    public function unitTo()
    {
        return $this->belongsTo(Unit::class, 'unit_to_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL EVENTS
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        // 🔸 Cegah duplikasi
        static::creating(function (UnitConversion $conversion) {
            $exists = self::where('unit_from_id', $conversion->unit_from_id)
                ->where('unit_to_id', $conversion->unit_to_id)
                ->exists();

            if ($exists) {
                throw new \Exception('Konversi dari satuan ini sudah ada.');
            }

            if ($conversion->unit_from_id === $conversion->unit_to_id) {
                throw new \Exception('Satuan asal dan tujuan tidak boleh sama.');
            }
        });

        // 🔸 Buat reverse conversion otomatis
        static::created(function (UnitConversion $conversion) {
            $reverseExists = self::where('unit_from_id', $conversion->unit_to_id)
                ->where('unit_to_id', $conversion->unit_from_id)
                ->exists();

            if (! $reverseExists && $conversion->factor > 0) {
                self::create([
                    'unit_from_id' => $conversion->unit_to_id,
                    'unit_to_id'   => $conversion->unit_from_id,
                    'factor'       => 1 / $conversion->factor,
                ]);
            }
        });
    }
}

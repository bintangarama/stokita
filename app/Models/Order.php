<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_no',
        'order_date',
        'total_amount',
        'discount',
        'grand_total',
        'status',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
    ];


    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($order) {
    //         if (!$order->order_no) {
    //             $order->order_no = 'ORD-' . now()->format('Ymd-His');
    //         }
    //     });
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {

            // Jika sudah ada order_no dari sistem, jangan diganti
            if ($order->order_no) return;

            // Ambil nomor terakhir
            $last = self::orderBy('id', 'desc')->first();
            $next = $last ? ((int) substr($last->order_no, 4)) + 1 : 1;

            // Format ORD-0001
            $order->order_no = 'ORD-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}

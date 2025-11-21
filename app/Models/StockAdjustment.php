<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    //
    protected $fillable = ['adjustment_date', 'reference_no', 'notes', 'created_by'];

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

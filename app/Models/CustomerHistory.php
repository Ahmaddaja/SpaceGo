<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHistory extends Model
{
    use HasFactory;

    protected $table = 'customer_histories';

    protected $fillable = [
        'customer_id',
        'activity_type',
        'description',
        'additional_data',
        'created_by'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship dengan Customer (User)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Method untuk log activity
    public static function logActivity($customer_id, $activity_type, $description, $additional_data = null, $created_by = 'system')
    {
        return self::create([
            'customer_id' => $customer_id,
            'activity_type' => $activity_type,
            'description' => $description,
            'additional_data' => $additional_data,
            'created_by' => $created_by
        ]);
    }

    // Scope untuk history pembayaran
    public function scopePaymentHistory($query)
    {
        return $query->whereIn('activity_type', ['PAYMENT_SUCCESS', 'PAYMENT_FAILED', 'RENTAL_PAYMENT']);
    }
}
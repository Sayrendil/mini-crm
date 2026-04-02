<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customer;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'subject',
        'description',
        'status',
        'replied_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

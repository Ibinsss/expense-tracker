<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'amount',
        'date',
        'category',
        'notes',
        'user_id',
        'receipt_path',
    ];

    // Relationship: an expense belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

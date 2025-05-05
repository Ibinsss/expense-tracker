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
        'receipt_data',
        'receipt_mime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getReceiptSrcAttribute(): ?string
    {
        if (empty($this->receipt_data) || empty($this->receipt_mime)) {
            return null;
        }

        $b64 = base64_encode($this->receipt_data);
        return "data:{$this->receipt_mime};base64,{$b64}";
    }
}

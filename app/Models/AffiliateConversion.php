<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_link_id',
        'amount',
        'ip_address',
        'user_agent',
        'referrer',
        'converted_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'converted_at' => 'datetime',
    ];

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }
}

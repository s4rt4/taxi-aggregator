<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'user_id',
        'message',
        'attachments',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_internal' => 'boolean',
        ];
    }

    // Relationships

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

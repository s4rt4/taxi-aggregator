<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'corporate_id',
        'period_start',
        'period_end',
        'total_bookings',
        'subtotal',
        'vat_amount',
        'total_amount',
        'currency',
        'due_date',
        'status',
        'sent_at',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'total_bookings' => 'integer',
            'subtotal' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CorporateInvoice $invoice) {
            if (empty($invoice->reference)) {
                $invoice->reference = static::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $reference = "INV-{$date}-{$random}";

        while (static::where('reference', $reference)->exists()) {
            $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $reference = "INV-{$date}-{$random}";
        }

        return $reference;
    }

    // Relationships

    public function corporate(): BelongsTo
    {
        return $this->belongsTo(Corporate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'corporate_invoice_id');
    }

    // Scopes

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
            ->whereDate('due_date', '<', now()->toDateString());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'overdue']);
    }

    // Helpers

    public function isOverdue(): bool
    {
        if ($this->status !== 'sent' && $this->status !== 'overdue') {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsPaid(?string $reference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $reference,
        ]);
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => 'overdue']);
    }
}

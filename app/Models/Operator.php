<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_id',
        'operator_name',
        'legal_company_name',
        'trading_name',
        'registration_number',
        'vat_number',
        'email',
        'phone',
        'website',
        'postcode',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'base_lat',
        'base_lng',
        'dispatch_system',
        'licence_number',
        'licence_authority',
        'licence_expiry',
        'fleet_size',
        'operator_licence_file',
        'public_liability_insurance_file',
        'public_liability_expiry',
        'accepts_prepaid',
        'accepts_cash',
        'stripe_account_id',
        'stripe_status',
        'tier',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'commission_rate',
        'rating_avg',
        'rating_count',
        'total_bookings',
        'is_featured',
        'dead_leg_approved',
        'airport_approved',
        'icabbi_enabled',
        'icabbi_api_url',
        'icabbi_app_key',
        'icabbi_secret_key',
        'icabbi_integration_name',
    ];

    protected function casts(): array
    {
        return [
            'licence_expiry' => 'date',
            'public_liability_expiry' => 'date',
            'approved_at' => 'datetime',
            'accepts_prepaid' => 'boolean',
            'accepts_cash' => 'boolean',
            'is_featured' => 'boolean',
            'dead_leg_approved' => 'boolean',
            'airport_approved' => 'boolean',
            'icabbi_enabled' => 'boolean',
            'base_lat' => 'decimal:7',
            'base_lng' => 'decimal:7',
            'commission_rate' => 'decimal:2',
            'rating_avg' => 'decimal:2',
        ];
    }

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function contacts()
    {
        return $this->hasMany(OperatorContact::class);
    }

    public function primaryContact()
    {
        return $this->hasOne(OperatorContact::class)->where('type', 'primary');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function perMilePrices()
    {
        return $this->hasMany(PerMilePrice::class);
    }

    public function perMileUplifts()
    {
        return $this->hasMany(PerMileUplift::class);
    }

    public function locationPrices()
    {
        return $this->hasMany(LocationPrice::class);
    }

    public function postcodeAreaPrices()
    {
        return $this->hasMany(PostcodeAreaPrice::class);
    }

    public function meetGreetCharges()
    {
        return $this->hasMany(MeetGreetCharge::class);
    }

    public function flashSales()
    {
        return $this->hasMany(FlashSale::class);
    }

    public function deadLegDiscounts()
    {
        return $this->hasMany(DeadLegDiscount::class);
    }

    public function freePickupPostcodes()
    {
        return $this->hasMany(FreePickupPostcode::class);
    }

    public function vehicleAvailability()
    {
        return $this->hasMany(VehicleAvailability::class);
    }

    public function noticePeriods()
    {
        return $this->hasMany(NoticePeriod::class);
    }

    public function postcodeLeadTimes()
    {
        return $this->hasMany(PostcodeLeadTime::class);
    }

    public function tripRange()
    {
        return $this->hasOne(TripRange::class);
    }

    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class);
    }

    public function availabilityPauses()
    {
        return $this->hasMany(AvailabilityPause::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tripIssues()
    {
        return $this->hasMany(TripIssue::class);
    }

    public function weeklyStats()
    {
        return $this->hasMany(OperatorWeeklyStat::class);
    }

    public function statements()
    {
        return $this->hasMany(Statement::class);
    }

    // Dispatch helpers

    public function usesIcabbi(): bool
    {
        return $this->icabbi_enabled && $this->icabbi_app_key && $this->icabbi_secret_key;
    }

    // Scopes

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->approved();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

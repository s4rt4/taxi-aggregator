<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'type',
        'name',
        'email',
        'phone',
    ];

    // Relationships

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}

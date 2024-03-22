<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationRequest extends Model
{
    use HasFactory;

    protected $table = 'medication_requests';
    protected $primaryKey = "medication_request_id";
    protected $keyType = "string";

    protected $fillable = [
        'medication_request_id',
        'medication_request_status'
    ];
}
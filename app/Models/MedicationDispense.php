<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationDispense extends Model
{
    use HasFactory;

    protected $table = 'medication_dispenses';
    protected $primaryKey = "medication_dispense_id";
    protected $keyType = "string";

    protected $fillable = [
        'medication_dispense_id',
        'medication_dispense_status'
    ];
}
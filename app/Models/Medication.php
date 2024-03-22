<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';
    protected $primaryKey = "medication_id";
    protected $keyType = "string";

    protected $fillable = [
        'medication_id',
        'medication_status'
    ];
}
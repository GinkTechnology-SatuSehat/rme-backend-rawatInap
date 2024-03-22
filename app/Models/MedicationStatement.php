<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationStatement extends Model
{
    use HasFactory;

    protected $table = 'medication_statements';
    protected $primaryKey = "medication_statement_id";
    protected $keyType = "string";

    protected $fillable = [
        'medication_statement_id',
        'medication_statement_status'
    ];
}

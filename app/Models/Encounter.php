<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasFactory;

    protected $table = 'encounters';
    protected $primaryKey = "encounter_id";
    protected $keyType = "string";

    protected $fillable = [
        'encounter_id',
        'encounter_status'
    ];
}

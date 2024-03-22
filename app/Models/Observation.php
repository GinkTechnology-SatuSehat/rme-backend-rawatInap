<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    protected $table = 'observations';
    protected $primaryKey = "observation_id";
    protected $keyType = "string";

    protected $fillable = [
        'observation_id',
        'observation_status'
    ];
}

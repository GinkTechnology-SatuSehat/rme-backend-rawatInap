<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practitioner extends Model
{
    use HasFactory;

    protected $table = 'practitioners';
    protected $primaryKey = "practitioner_id";
    protected $keyType = "string";

    protected $fillable = [
        'practitioner_id',
    ];
}

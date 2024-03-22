<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    use HasFactory;

    protected $table = 'allergys';
    protected $primaryKey = "allergy_id";
    protected $keyType = "string";

    protected $fillable = [
        'allergy_id',
    ];
}
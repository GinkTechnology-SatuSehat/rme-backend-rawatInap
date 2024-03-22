<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specimen extends Model
{
    use HasFactory;

    protected $table = 'specimens';
    protected $primaryKey = "specimen_id";
    protected $keyType = "string";

    protected $fillable = [
        'specimen_id',
        'specimen_status'
    ];
}
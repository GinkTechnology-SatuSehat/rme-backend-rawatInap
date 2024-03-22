<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    use HasFactory;

    protected $table = 'compositions';
    protected $primaryKey = "composition_id";
    protected $keyType = "string";

    protected $fillable = [
        'composition_id',
        'composition_status'
    ];
}
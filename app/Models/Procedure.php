<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $table = 'procedures';
    protected $primaryKey = "procedure_id";
    protected $keyType = "string";

    protected $fillable = [
        'procedure_id',
        'procedure_status'
    ];
}
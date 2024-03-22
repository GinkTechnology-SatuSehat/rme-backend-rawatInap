<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuSehatToken extends Model
{
    use HasFactory;

    protected $table = 'satusehat_tokens';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;

    protected $fillable = [
        'token',
        'token_type'
    ];

}
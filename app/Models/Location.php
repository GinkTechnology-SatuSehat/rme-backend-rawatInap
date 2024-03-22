<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $primaryKey = "location_id";
    protected $keyType = "string";

    protected $fillable = [
        'location_id',
    ];
}

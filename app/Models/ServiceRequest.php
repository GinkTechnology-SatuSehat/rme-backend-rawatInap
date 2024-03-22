<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_requests';
    protected $primaryKey = "service_request_id";
    protected $keyType = "string";

    protected $fillable = [
        'service_request_id',
        'service_request_status',
    ];
}

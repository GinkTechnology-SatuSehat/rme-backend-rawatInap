<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarePlan extends Model
{
    use HasFactory;

    protected $table = 'care_plans';
    protected $primaryKey = "care_plan_id";
    protected $keyType = "string";

    protected $fillable = [
        'care_plan_id',
        'care_plan_status'
    ];
}

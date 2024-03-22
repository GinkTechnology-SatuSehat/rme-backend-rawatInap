<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalImpression extends Model
{
    use HasFactory;

    protected $table = 'clinical_impressions';
    protected $primaryKey = "clinical_impression_id";
    protected $keyType = "string";

    protected $fillable = [
        'clinical_impression_id',
        'clinical_impression_status'
    ];
}
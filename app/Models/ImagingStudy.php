<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagingStudy extends Model
{
    use HasFactory;

    protected $table = 'imaging_studies';
    protected $primaryKey = "imaging_study_id";
    protected $keyType = "string";

    protected $fillable = [
        'imaging_study_id',
        'imaging_study_status'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticReport extends Model
{
    use HasFactory;

    protected $table = 'diagnostic_reports';
    protected $primaryKey = "diagnostic_report_id";
    protected $keyType = "string";

    protected $fillable = [
        'diagnostic_report_id',
        'diagnostic_report_status'
    ];
}
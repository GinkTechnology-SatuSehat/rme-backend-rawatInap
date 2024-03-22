<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_responses';
    protected $primaryKey = "questionnaire_response_id";
    protected $keyType = "string";

    protected $fillable = [
        'questionnaire_response_id',
        'questionnaire_response_status'
    ];
}

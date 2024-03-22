<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrganization extends Model
{
    use HasFactory;

    protected $table = 'sub_organizations';
    protected $primaryKey = "sub_organization_id";
    protected $keyType = "string";

    protected $fillable = [
        'sub_organization_id',
    ];
}

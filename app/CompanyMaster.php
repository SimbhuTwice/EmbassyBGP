<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class companyMaster extends Model
{
    protected $table = 'company_master';
	protected $fillable = [ 
        'company_name',
        'company_short_name',
        'website',
        'created_by',
        'updated_by',
        'is_active',
    ];
}

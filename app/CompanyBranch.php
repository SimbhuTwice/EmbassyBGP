<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $table = 'company_branch';
	protected $fillable = [ 
        'company_id',
        'branch_name',
        'address',
        'city',
        'pincode',
        'email',
        'gstnumber',
        'distech_deviceip',
        'distech_username',
        'distech_password',
        'created_by',
        'updated_by',
        'is_active',
    ];
}

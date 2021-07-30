<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistechDevice extends Model
{
    protected $table = 'distech_device';
	protected $fillable = [ 
        'company_id',
        'branch_id',
        'distech_deviceip',
        'object_type',
        'object_name',
        'asn_value',
        'present_value',
        'status_flag',
        'status_date',
        'status_time',
    ];
}

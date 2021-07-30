<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchDeviceLocation extends Model
{
    protected $table = 'branch_device_location';
	protected $fillable = [ 
        'company_id',
        'branch_id',
        'device_type',
        'device_name',
        'device_location',
        'chart_type',
	'show_in_header',
        'plot_min',
        'plot_max',
        'plot_bands',
	'img_src',
	'device_uom',
        'device_description',
	'device_id',
        'created_by',
        'updated_by',
        'is_active',
    ];
}

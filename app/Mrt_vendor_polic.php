<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_vendor_polic extends Model
{
    protected $primaryKey = 'venpol_id';
    public $timestamps    = false;
    protected $fillable   = [
        'venpol_type',
        'venpol_title',
        'venpol_content',
        'venpol_status',
        'venpol_createdby',
        'venpol_createddate',
        'venpol_updatedby',
        'venpol_lastupdate',
        'venpol_ip'
    ];
}

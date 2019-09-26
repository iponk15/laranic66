<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_roles extends Model
{
    protected $primaryKey  = 'role_id';
    public $timestamps     = false;
    protected $fillable    = [
        'role_name',
        'role_description',
        'role_status',
        'role_createdby',
        'role_createddate',
        'role_updatedby',
        'role_lastupdate'
    ];
}

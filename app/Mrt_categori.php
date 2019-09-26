<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_categori extends Model
{
    protected $primaryKey  = 'category_id';
    public $timestamps     = false;
    protected $fillable    = [
        'category_code',
        'category_name',
        'category_status',
        'category_createdby',
        'category_createddate',
        'category_updatedby',
        'category_lastupdate',
        'category_ip'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_sub_categori extends Model
{
    protected $primaryKey  = 'subcat_id';
    public $timestamps     = false;
    protected $fillable    = [
        'subcat_categori_id',
        'subcat_code',
        'subcat_name',
        'subcat_status',
        'subcat_createdby',
        'subcat_createddate',
        'subcat_updatedby',
        'subcat_lastupdate'
    ];
}

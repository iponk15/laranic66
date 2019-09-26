<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_menu extends Model
{
    protected $primaryKey  = 'menu_id';
    public $timestamps     = false;
    protected $fillable    = [
        'menu_nama',
        'menu_parent',
        'menu_link',
        'menu_icon',
        'menu_order',
        'menu_status',
        'menu_createdby',
        'menu_updatedby',
        'menu_ip'
    ];
}

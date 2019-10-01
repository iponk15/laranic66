<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MrtModule extends Model
{
    protected $primaryKey  = 'module_id';
    protected $fillable    = [
        'module_menu_id',
        'module_status',
        'module_createdby',
        'module_updatedby',
        'module_ip'
    ];
}

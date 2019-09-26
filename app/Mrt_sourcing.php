<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mrt_sourcing extends Model
{
    protected $primaryKey  = 'sourcing_id';
    public $timestamps     = false;
    protected $fillable    = [
        'sourcing_no_inv',
        'sourcing_title',
        'sourcing_startdate',
        'sourcing_enddate',
        'sourcing_category',
        'sourcing_subcategori',
        'sourcing_type',
        'sourcing_createdby',
        'sourcing_createddate',
        'sourcing_ip'
    ];
}

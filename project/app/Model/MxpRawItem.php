<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MxpRawItem extends Model
{
    protected $table = 'mxp_raw_item';
    protected $primaryKey = 'id_raw_item';

    protected $fillable = [
        'user_id',
        'item_code',
        'item_name',
        'price',
        'sort_description',
        'opening_quantity'
    ];
}
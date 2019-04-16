<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpIp extends Model
{
    protected $table = 'mxp_ip_check';
    protected $fillable = ['ip', 'countryCode', 'cityName', 'zipCode', 'latitude', 'longitude'];
}

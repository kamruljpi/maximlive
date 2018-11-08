<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpSupplierPrice extends Model
{
    protected $primaryKey = 'supplier_price_id';
	protected $table = 'mxp_supplier_prices';
    public function supplier(){
        return $this->hasOne(Supplier::class, 'supplier_id', 'supplier_id');
    }
}

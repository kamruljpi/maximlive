<?php

namespace App\Http\Controllers\Purchase;

class PurchaseFlugs
{
	const PURCHASE_ORDER = 'purchase_order';

	const PURCHASE = 'purchase';

	//this flug use when purchase order genarate to purchase
	const PURCHASE_FROM_PURCHASE_ORDER = 'purchase_form_purchase_order';


	//this flug use when purchase store in mxp_store table
	const PURCHASE_STORE = 'purchase_store';

}
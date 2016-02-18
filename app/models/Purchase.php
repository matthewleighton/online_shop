<?php
	class Purchase extends Model {
		public $properties = array(
			'user_id' => '',
			'payment_id' => '',
			'address_id' => '',
			'products_price' => '',
			'delivery_price' => ''
		);

		public function createPurchase() {

		}
	}
?>
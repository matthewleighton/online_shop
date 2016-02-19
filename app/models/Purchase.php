<?php
	class Purchase extends Model {
		public $properties = array(
			'userId' => '',
			'paymentMethodId' => '',
			'addressId' => '',
			'productsPrice' => '',
			'deliveryPrice' => ''
		);

		public function createPurchase() {

		}
	}
?>
<?php
	class Payment_Method extends Model {

		public $properties = array(
			'fk_payment_method_user' => '',
			'fk_payment_method_address' => '',
			'card_type' => '',
			'card_number' => '',
			'cardholder_name' => '',
			'exp_month' => '',
			'exp_year' => ''
		);

		public $table = "payment_method";

		public $errors = array();

		protected $sqlOptions = ['join' => ['address' => ['address.address_id', 'payment_method.fk_payment_method_address']]];

		public function __construct() {
			$this->properties['fk_payment_method_user'] = $_SESSION['user_id'];

			$this->validates('card_type', 'presence');
	
			$this->validates('card_number', 'presence');

			$this->validates('fk_payment_method_address', 'presence');
		}
	}
?>
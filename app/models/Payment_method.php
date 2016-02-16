<?php
	class Payment_Method extends Model {

		public $properties = array(
			'user_id' => '',
			'address_id' => '',
			'card_type' => '',
			'card_number' => '',
			'cardholder_name' => '',
			'exp_month' => '',
			'exp_year' => ''
		);

		public $table = "payment_method";

		public $errors = array();

		protected $sqlOptions = ['join' => ['address' => ['address.address_id', 'payment_method.address_id']]];

		public function __construct() {
			$this->properties['user_id'] = $_SESSION['user_id'];

			$this->validates('card_type', 'presence');
	
			$this->validates('card_number', 'presence');

			$this->validates('address_id', 'presence');
		}
	}
?>
<?php
	class Address extends Model {

		public $properties = array(
			'fk_address_user' => '',
			'full_name' => '',
			'address_line_1' => '',
			'address_line_2' => '',
			'city' => '',
			'county' => '',
			'postcode' => '',
			'country' => '',
			'phone_number' => ''
			);

		public $errors = array();

		public $table = "address";

		public function __construct() {
			$this->properties['fk_address_user'] = $_SESSION['user_id'];

			$this->validates('full_name', 'presence');

			$this->validates('address_line_1', 'presence');

			$this->validates('city', 'presence');

			$this->validates('postcode', 'presence');

			$this->validates('country', 'presence');

			$this->validates('phone_number', 'presence');
		}

		

	}
?>
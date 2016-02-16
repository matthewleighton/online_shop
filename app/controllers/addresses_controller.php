<?php
	Class Addresses extends Controller {
		
		public function __construct() {
			require_once('../app/models/Address.php');
		}

		//Lists all your addresses
		public function index() {
			
		}

		// Add a new address
		public function add() {
			session_start();
			$address = new Address;
			if(isset($_POST['full_name'])) {
				$address->assignProperties();
				$addressId = $address->saveToDb('INSERT INTO', 'address', $address->properties);
				
				$_SESSION['addressId'] = $addressId;
				$_SESSION['address'] = $address;

				$this->redirect_to($_POST['redirect']);
			}
		}
	}
?>
<?php
	class Payment_Methods extends Controller {
		
		public function __construct() {
			session_start();
			require_once('../app/models/Payment_Method.php');
		}

		public function index() {
			echo "Payment Methods - INDEX";
		}

		public function add() {
			if(!isset($_POST['card_number'])) {
				$this->redirect_to();
			}

			$paymentMethod = new Payment_Method;
			$paymentMethod->assignProperties($_POST);
			$paymentMethod->runValidations();

			if($_POST['include_new_address'] == "1") {
				require_once('../app/models/Address.php');
				$address = new Address;
				$address->assignProperties($_POST);
				$address->runValidations();

				// A valid payment method will still have one error - the address_id will be missing.
				if(count($paymentMethod->errorsList) == 1 && isset($paymentMethod->errorsList['address_id'])) {
					$addressId = $address->saveToDb('INSERT INTO', 'address', $address->properties);
				}
			} else {
				$addressId = $_POST['addressId'];
			}

			
			$paymentMethod->properties['address_id'] = $addressId;
			$paymentMethodId = $paymentMethod->saveToDb('INSERT INTO', 'payment_method', $paymentMethod->properties);

			$_SESSION['paymentMethodId'] = $paymentMethodId;
			$_SESSION['payment_method'] = $paymentMethod;
			if(isset($_SESSION['address'])) {
				$_SESSION['address'] = $address;	
			}

			$this->redirect_to($_POST['redirect']);
		}
	}
?>
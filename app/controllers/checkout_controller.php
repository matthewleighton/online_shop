<?php
	Class checkout extends Controller {
		public function index() {
			if(!Sessions_helper::logged_in()) {
				$this->redirect_to('sessions/login?redirect=checkout');
				break;
			}
		
			if(!array_key_exists('checkout', $_SESSION)) {
				echo "creating checkout key";
				$_SESSION['checkout'] = [];
			}

			if(isset($_POST['addressId'])) {
				$_SESSION['checkout']['address'] = $_POST['addressId'];
			}

			if(isset($_POST['paymentMethodId'])) {
				$_SESSION['checkout']['paymentMethod'] = $_POST['paymentMethodId'];
			}

			if(!isset($_SESSION['checkout']['address'])) {
				$this->redirect_to('checkout/address');
			} elseif(!isset($_SESSION['checkout']['deliveryMethod'])) {
				$this->redirect_to('checkout/deliverymethod');
			} elseif(!isset($_SESSION['checkout']['paymentMethod'])) {
				$this->redirect_to('checkout/paymentmethod');
			} else {
				
				$this->redirect_to('checkout/confirm');
			}
		}

		public function address() {
			require_once('../app/models/Address.php');
			session_start();
			if(isset($_SESSION['address'])) {
				$address = $_SESSION['address'];
				if(count($_SESSION['address']->errorsList) == 0) {
					$_SESSION['checkout']['address'] = $_SESSION['addressId'];
					unset($_SESSION['addressId']);
					unset($_SESSION['address']);
					$this->redirect_to('checkout/index');
					break;
				}
			} else {
				$address = new Address;
			}
			
			$addressList = $address->findByUserId($_SESSION['user_id']);

			$view = new View('checkout/address', ['header' => false, 'footer' => false]);
			$view->set_title('Delivery Address');
			
			$view->pass_data('addressList', $addressList);
			$view->pass_data('address', $address);
			$view->pass_data('redirect', 'checkout/address');

			$view->load_page();
			unset($_SESSION['address']);
		}

		public function deliveryMethod() {
			if(isset($_POST['deliveryMethod'])) {
				session_start();
				$_SESSION['checkout']['deliveryMethod'] = $_POST['deliveryMethod'];
				$this->redirect_to('checkout');
			}

			$view = new View('checkout/delivery_method', ['header' => false, 'footer' => false]);
			$view->set_title('Delivery Method');

			$view->load_page();
		}

		public function paymentMethod() {
			require_once('../app/models/Payment_Method.php');
			require_once('../app/models/Address.php');
			session_start();
			
			if(isset($_SESSION['payment_method'])) {
				$paymentMethod = $_SESSION['payment_method'];

				if(count($paymentMethod->errorsList) == 0) {
					$_SESSION['checkout']['paymentMethod'] = $_SESSION['paymentMethodId'];
					unset($_SESSION['paymentMethodId']);
					unset($_SESSION['paymentMethod']);
					$this->redirect_to('checkout/index');
					break;
				}

			} else {
				$paymentMethod = new Payment_Method;	
			}

			if(isset($_SESSION['address'])) {
				$address = $_SESSION['address'];
			} else {
				$address = new Address;	
			}

			$addressList = $address->findByUserId($_SESSION['user_id']);

			$paymentList = $paymentMethod->findByUserId($_SESSION['user_id']);

			$addressAttributes = ['full_name', 'address_line_1', 'address_line_2', 'city',
								  'county', 'postcode', 'country', 'phone_number'];

			$view = new View('checkout/payment_method', ['header' => false, 'footer' => false]);
			$view->set_title('Payment Method');

			$view->pass_data('payment_method', $paymentMethod);
			$view->pass_data('paymentList', $paymentList);
			$view->pass_data('redirect', 'checkout/paymentmethod');
			$view->pass_data('addressAttributes', $addressAttributes);
			$view->pass_data('address', $address);
			$view->pass_data('addressList', $addressList);

			$view->load_page();

			unset($_SESSION['address']);
			unset($_SESSION['payment_method']);
		}

		public function confirm() {
			session_start();
			require_once('../app/models/Payment_Method.php');
			require_once('../app/models/Address.php');
			require_once('../app/models/Cart.php');

			$address = new Address;
			$address = $address->findById($_SESSION['checkout']['address']);

			$paymentMethod = new Payment_Method;
			$paymentMethod = $paymentMethod->findById($_SESSION['checkout']['paymentMethod']);

			if($address['address_id'] != $paymentMethod['address_id']) {
				$billingAddress = new Address;
				$billingAddress = $billingAddress->findById($paymentMethod['address_id']);
			}

			$cart = new Cart;
			$cart = $cart->generateCart();

			switch ($_SESSION['checkout']['deliveryMethod']) {
				case 'same-day':
					$deliveryTime = 'today';
					$deliveryPrice = 4.99;
					break;
				case 'one-day':
					$deliveryTime = '+1 day';
					$deliveryPrice = 2.99;
					break;
				default:
					$deliveryTime = '+3 days';
					$deliveryPrice = 0;
					break;
			}

			$deliveryDate = date('l d M. Y', strtotime($deliveryTime, time()));

			$view = new View('checkout/confirm', ['header' => false, 'footer' => false]);
			$view->set_title('Confirm Purchase Details');

			if($address['address_id'] != $paymentMethod['address_id']) {
				$billingAddress = new Address;
				$billingAddress = $billingAddress->findById($paymentMethod['address_id']);
				$view->pass_data('billingAddress', $billingAddress);
			}

			$view->pass_data('deliveryDate', $deliveryDate);
			$view->pass_data('deliveryPrice', $deliveryPrice);
			$view->pass_data('cart', $cart);
			$view->pass_data('address', $address);
			$view->pass_data('paymentMethod', $paymentMethod);

			$view->load_page();
		}

		public function submit() {
			echo "Thanks for placing your order";
		}
	}
?>
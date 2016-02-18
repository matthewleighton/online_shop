<?php
	Class checkout extends Controller {
		
		public function __construct() {
			session_start();
			if(!Sessions_helper::logged_in()) {
				$this->redirect_to('sessions/login?redirect=checkout');
				break;
			} elseif (!isset($_SESSION['checkout'])) {
				$_SESSION['checkout'] = [];
				$this->redirect_to('checkout/index');
				break;
 			}

 			if ($_SESSION['cart'] == null) {
 				$this->redirect_to('carts');
 			}

 			//Redirect to earlier page if previous information is missing
			if(!isset($_SESSION['redirecting'])) {
				$stages = ['address', 'deliveryMethod', 'paymentMethod'];
				foreach ($stages as $stage) {
					if ($_SESSION['checkout'][$stage] == null) {
						$_SESSION['redirecting'] = true;
						$this->redirect_to('checkout/' . $stage);
						break;
					}
				}
				
			} else {
				unset($_SESSION['redirecting']);
			}
		}

		public function index() {
			$_SESSION['checkout']['cart'] = $_SESSION['cart'];
			$_SESSION['checkout']['address'] = null;
			$_SESSION['checkout']['deliveryMethod'] = null;
			$_SESSION['checkout']['paymentMethod'] = null;
			$this->redirect_to('checkout/address');
		}

		public function address() {
			require_once('../app/models/Address.php');
			//session_start();
			if(isset($_POST['addressId'])) {
				$_SESSION['checkout']['address'] = $_POST['addressId'];
				$this->redirect_to('checkout/deliveryMethod');
				break;
			}
			if(isset($_SESSION['address'])) {
				$address = $_SESSION['address'];
				if(count($_SESSION['address']->errorsList) == 0) {
					$_SESSION['checkout']['address'] = $_SESSION['addressId'];
					unset($_SESSION['addressId']);
					unset($_SESSION['address']);
					
					$this->redirect_to('checkout/deliveryMethod');
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
				$validDeliveryMethods = ['free' => ['price' => 0, 'deliveryTime' => '+3 days'], 
										 'first-class' => ['price' => 1.50, 'deliveryTime' => '+1 day'],
										 'one-day' => ['price' => 3.49, 'deliveryTime' => 'today']];

				if(in_array($_POST['deliveryMethod'], array_keys($validDeliveryMethods))) {
					// TODO - change DeliveryTime to DeliveryDue
					$delivery = $validDeliveryMethods[$_POST['deliveryMethod']];
					$_SESSION['checkout']['deliveryMethod'] = [];
					$_SESSION['checkout']['deliveryMethod']['deliveryMethodName'] = $_POST['deliveryMethod'];
					$_SESSION['checkout']['deliveryMethod']['deliveryPrice'] = $delivery['price'];
					$_SESSION['checkout']['deliveryMethod']['deliveryTime'] = $delivery['deliveryTime'];

					$deliveryDue = date('l d M. Y', strtotime($delivery['deliveryTime'], time()));
					$_SESSION['checkout']['deliveryMethod']['deliveryDue'] = $deliveryDue;






					$this->redirect_to('checkout/paymentMethod');
				}
			}

			$view = new View('checkout/delivery_method', ['header' => false, 'footer' => false]);
			$view->set_title('Delivery Method');

			$view->load_page();
		}

		public function paymentMethod() {
			require_once('../app/models/Payment_Method.php');
			require_once('../app/models/Address.php');
			if(isset($_POST['paymentMethodId'])) {
				$_SESSION['checkout']['paymentMethod'] = $_POST['paymentMethodId'];
				$this->redirect_to('checkout/confirm');
				break;
			}

			if(isset($_SESSION['payment_method'])) {
				$paymentMethod = $_SESSION['payment_method'];

				if(count($paymentMethod->errorsList) == 0) {
					$_SESSION['checkout']['paymentMethod'] = $_SESSION['paymentMethodId'];
					unset($_SESSION['paymentMethodId']);
					unset($_SESSION['payment_method']);
					$this->redirect_to('checkout/confirm');
					break;
				}
			} else {
				$paymentMethod = new Payment_Method;
				unset($_SESSION['payment_method']);
				unset($_SESSION['paymentMethodId']);
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
			$cart = $cart->generateCartFromSession($_SESSION['checkout']['cart']);

			$delivery = $_SESSION['checkout']['deliveryMethod'];
			$deliveryDate = date('l d M. Y', strtotime($delivery['deliveryTime'], time()));
			$deliveryPrice = $delivery['deliveryPrice'];

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
			echo "Thanks for placing your order!<br><br>";
			var_dump($_SESSION['checkout']);
			echo "<br><br>";
			var_dump(array_keys($_SESSION['checkout']));
			/*require_once('../app/models/Purchase.php');
			$purchase = new Purchase;
			$purchase->assignProperties();
			if ($purchaseId = $purchase->saveToDb('INSERT INTO', 'purchase', $purchase->properties)) {
				$purchase->addToPurchase($_SESSION['checkout']);
			}*/
		}
	}
?>
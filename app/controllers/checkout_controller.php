<?php
	Class checkout extends Controller {
		
		public function __construct() {
			require_once('../app/helpers/Checkout_helper.php');
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
					if ($_SESSION['checkout']['properties'][$stage] == null) {
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
			$_SESSION['checkout'] = [];
			$_SESSION['checkout']['cart'] = $_SESSION['cart'];
			$_SESSION['checkout']['properties']['address'] = null;
			$_SESSION['checkout']['properties']['deliveryMethod'] = null;
			$_SESSION['checkout']['properties']['paymentMethod'] = null;
			$_SESSION['checkout']['properties']['fk_purchase_user'] = $_SESSION['user_id'];
			$this->redirect_to('checkout/address');
		}

		public function address() {
			require_once('../app/models/Address.php');
			if(isset($_POST['addressId']) && Checkout_helper::confirmAddressOwnership($_POST['addressId'])) {
					$_SESSION['checkout']['properties']['address'] = $_POST['addressId'];
					$this->redirect_to('checkout/deliveryMethod');
					break;
			}

			if(isset($_SESSION['address'])) {
				$address = $_SESSION['address'];
				if(count($_SESSION['address']->errorsList) == 0) {
					$_SESSION['checkout']['properties']['address'] = $_SESSION['addressId'];
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
				$validDeliveryMethods = ['free' => ['price' => 0, 'deliveryTime' => '+4 days'], 
										 'first-class' => ['price' => 1.50, 'deliveryTime' => '+2 day'],
										 'one-day' => ['price' => 3.49, 'deliveryTime' => '+ 1 day']];

				if(in_array($_POST['deliveryMethod'], array_keys($validDeliveryMethods))) {
					$delivery = $validDeliveryMethods[$_POST['deliveryMethod']];
					
					$deliveryDue = date('l d M. Y', strtotime($delivery['deliveryTime'], time()));
					$_SESSION['checkout']['properties']['deliveryDue'] = $deliveryDue;
					$_SESSION['checkout']['properties']['deliveryPrice'] = $delivery['price'];
					$_SESSION['checkout']['properties']['deliveryMethod'] = $_POST['deliveryMethod'];

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
			if(isset($_POST['paymentMethodId']) && Checkout_helper::confirmCardOwnership($_POST['paymentMethodId'])) {
				$_SESSION['checkout']['properties']['paymentMethod'] = $_POST['paymentMethodId'];
				$this->redirect_to('checkout/confirm');
				break;
			}

			if(isset($_SESSION['payment_method'])) {
				$paymentMethod = $_SESSION['payment_method'];

				if(count($paymentMethod->errorsList) == 0) {
					$_SESSION['checkout']['properties']['paymentMethod'] = $_SESSION['paymentMethodId'];
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
			$address = $address->findById($_SESSION['checkout']['properties']['address']);

			$paymentMethod = new Payment_Method;
			$paymentMethod = $paymentMethod->findById($_SESSION['checkout']['properties']['paymentMethod']);

			$cart = new Cart;
			$cart = $cart->generateCartFromSession($_SESSION['checkout']['cart']);

			$deliveryDate = $_SESSION['checkout']['properties']['deliveryDue'];
			$deliveryPrice = $_SESSION['checkout']['properties']['deliveryPrice'];

			$view = new View('checkout/confirm', ['header' => false, 'footer' => false]);
			$view->set_title('Confirm Purchase Details');

			if($address['address_id'] != $paymentMethod['fk_payment_method_address']) {
				$billingAddress = new Address;
				$billingAddress = $billingAddress->findById($paymentMethod['fk_payment_method_address']);
				$view->pass_data('billingAddress', $billingAddress);
			}

			$productsPrice = 0;
			foreach ($_SESSION['cart'] as $product) {
				$productsPrice += ((floatval($product['product_price'])) * $product['cart_quantity']);
			}
			$_SESSION['checkout']['properties']['productsPrice'] = $productsPrice;

			// TODO - Change original naming of properties to include "id", so these lines aren't needed.
			$_SESSION['checkout']['properties']['fk_purchase_payment_method'] = $_SESSION['checkout']['properties']['paymentMethod'];
			$_SESSION['checkout']['properties']['fk_purchase_address'] = $_SESSION['checkout']['properties']['address'];

			$view->pass_data('deliveryDate', $deliveryDate);
			$view->pass_data('deliveryPrice', $deliveryPrice);
			$view->pass_data('cart', $cart);
			$view->pass_data('address', $address);
			$view->pass_data('paymentMethod', $paymentMethod);

			$view->load_page();
		}

		public function submit() {
			require_once('../app/models/Purchase.php');
			$purchase = new Purchase;
			$purchase->assignProperties($_SESSION['checkout']['properties']);
			$purchaseId = $purchase->savePreparedStatementToDb('purchase', $purchase->properties);
			
			$products = [];
			foreach ($_SESSION['checkout']['cart'] as $key => $value) {
				if (is_array($value)) {
					array_push($products, ['fkProductPurchaseProduct' => $key,
										   'quantityInPurchase' => $value['cart_quantity'],
									   	   'priceAtPurchase' => $value['product_price']]);
				}
			}

			$purchase->addToJoinTable($products, 'product_purchase');

			require_once('../app/models/Cart.php');
			$cart = new Cart($_SESSION['checkout']['properties']['fk_purchase_user']);
			
			$cart->emptyCart();
			
			$view = new View('checkout/submit');
			$view->set_title('Order Complete');
			$view->pass_data('deliveryDue', $_SESSION['checkout']['properties']['deliveryDue']);
			$view->load_page();
			
			unset($_SESSION['checkout']);
		}
	}
?>
<?php
	Class Carts extends Controller {

		public function __construct() {

		}

		public function index() {			
			require_once('../app/models/Cart.php');
			$model = new Cart;
			if (Sessions_helper::logged_in()) {
				$cart = $model->generateCartFromDb();
			} else {
				if (isset($_SESSION['cart'])) {
					$cart = $model->generateCartFromSession($_SESSION['cart']);	
				} else {
					$cart = [];
				}
			}

			// Commented since I've added this in the Product model - don't think it's needed here anymore.
			// Will remove later
			#$amountInCart = count($cart);
			#for ($i=0; $i < $amountInCart; $i++) { 
			#	$catagory = ucfirst($cart[$i]['product_catagory']);
			#	require_once('../app/models/' . $catagory . '.php');
			#	$model = new $catagory;
			#	$cart[$i] = $model->splitListsToArray($cart[$i]);
			#}
			
			$view = new View('cart/index');
			$view->pass_data('cart', $cart);
			$view->set_title('Basket');
			$view->load_page();
		}

		public function addItem() {
			if(Sessions_helper::logged_in()) {
				// Add item to cart table in DB
				require_once('../app/models/Cart.php');
				$cart = new Cart;
				$cart->addProductToCart($_POST['productVersionId'], $_POST['quantity']);
			} else {
				// Add item to cart in session variable
				if(session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				
				if(!isset($_SESSION['cart'])) {
					$_SESSION['cart'] = [];
				}

				if(!isset($_SESSION['cart'][$_POST['productVersionId']])) {
					$_SESSION['cart'][$_POST['productVersionId']]['cart_quantity'] = intval($_POST['quantity']);
				} else {
					$_SESSION['cart'][$_POST['productVersionId']]['cart_quantity'] += intval($_POST['quantity']);
				}

				$_SESSION['cart'][$_POST['productVersionId']]['product_price'] = $_POST['price'];
			}

			$this->redirect_to('carts');
		}

		public function removeItem() {
			if(Sessions_helper::logged_in()) {
				require_once('../app/models/Cart.php');
				$cart = new Cart;
				$cart->removeItem($_POST['productVersionId']);
			} else {
				if(array_key_exists($_POST['productVersionId'], $_SESSION['cart'])) {
					unset($_SESSION['cart'][$_POST['productVersionId']]);
					if (count($_SESSION['cart']) == 0) {
						unset($_SESSION['cart']);
					}
				}
			}

			$this->redirect_to('carts');
		}
	}

?>
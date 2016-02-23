<?php
	Class Carts extends Controller {

		public function __construct() {

		}

		public function index() {			
			require_once('../app/models/cart.php');
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
			
			$view = new View('cart/index');
			$view->pass_data('cart', $cart);
			$view->set_title('Basket');
			$view->load_page();
		}

		public function addItem() {
			if(Sessions_helper::logged_in()) {
				// Add item to cart table in DB
				require_once('../app/models/product.php');
				$model = new Product;
				$model->addToCart($_POST['product_id'], $_POST['quantity']);
			} else {
				// Add item to cart in session variable
				if(session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				
				if(!isset($_SESSION['cart'])) {
					$_SESSION['cart'] = [];
				}

				if(!isset($_SESSION['cart'][$_POST['product_id']])) {
					$_SESSION['cart'][$_POST['product_id']]['cart_quantity'] = intval($_POST['quantity']);
				} else {
					$_SESSION['cart'][$_POST['product_id']]['cart_quantity'] += intval($_POST['quantity']);
				}

				$_SESSION['cart'][$_POST['product_id']]['price'] = $_POST['price'];
/*
				if(array_key_exists($_POST['product_id'], $_SESSION['cart'])) {
					$_SESSION['cart'][$_POST['product_id']] += intval($_POST['quantity']);
				} else {
					$_SESSION['cart'][$_POST['product_id']] = intval($_POST['quantity']);
				}
*/
			}
			$this->redirect_to('carts');
		}

		public function removeItem() {
			if(Sessions_helper::logged_in()) {
				require_once('../app/models/cart.php');
				$cart = new Cart;
				$cart->removeItem($_POST['product_id']);
			} else {
				if(array_key_exists($_POST['product_id'], $_SESSION['cart'])) {
					unset($_SESSION['cart'][$_POST['product_id']]);
					if (count($_SESSION['cart']) == 0) {
						unset($_SESSION['cart']);
					}
				}
			}

			$this->redirect_to('carts');
		}
	}

?>
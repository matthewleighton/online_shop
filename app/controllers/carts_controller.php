<?php
	Class Carts extends Controller {

		public function __construct() {

		}

		public function index() {
			if(Sessions_helper::logged_in()) {
				require_once('../app/models/cart.php');
				$model = new Cart;
				$cart = $model->generateCart();
			} else {
				if(isset($_SESSION['cart']) && $_SESSION['cart'] != []) {
					require_once('../app/models/product.php');
					$model = new Product;
					$cart = $model->generateCart();
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

				if(array_key_exists($_POST['product_id'], $_SESSION['cart'])) {
					$_SESSION['cart'][$_POST['product_id']] += intval($_POST['quantity']);
				} else {
					$_SESSION['cart'][$_POST['product_id']] = intval($_POST['quantity']);
				}
			}
			$this->redirect_to('carts');
		}

		public function removeItem() {
			if(Sessions_helper::logged_in()) {
				foreach ($_SESSION['cart'] as $product) {
					if($product['product_id'] == $_POST['product_id']) {
						require_once('../app/models/cart.php');
						$cart = new Cart;
						$cart->removeItem($_POST['product_id']);
						break;
					}
				}
			} else {
				if(array_key_exists($_POST['product_id'], $_SESSION['cart'])) {
					unset($_SESSION['cart'][$_POST['product_id']]);
				}
			}

			$this->redirect_to('carts');
		}
	}

?>
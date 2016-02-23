<?php
	class sessions_helper {
		public function login() {
			$conn = Db::connect();
			$sql = "SELECT user_id FROM users WHERE email = '" . $_POST['email'] . "' AND password = '" . md5($_POST['password']) . "'";
			$result = $conn->query($sql)->fetch_assoc();
			
			if($result == NULL) {
				return false;
			} else {
				session_start();
				$_SESSION['user_id'] = $result['user_id'];
				
				if(isset($_SESSION['cart']) && $_SESSION['cart'] != []) {
					require_once('../app/models/Product.php');
					$Product = new Product;
					foreach ($_SESSION['cart'] as $product_id => $value) {
						//var_dump($product_id);
						//die();
						$Product->addToCart($product_id, $_SESSION['cart'][$product_id]['cart_quantity']);
					}
				}

				return true;
			}
		}

		public function logged_in() {
			if(session_id() == "") {
				session_start();
			}
			if(isset($_SESSION['user_id'])) {
				return true;
			} else {
				return false;
			}
		}

		public function currentUser() {
			
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}

			if(isset($_SESSION['user_id'])) {
				return User::findBy('user_id', $_SESSION['user_id']);
			}


		}

		public function userIsAdmin() {
			#var_dump(Sessions_helper::currentUser());

			if (Sessions_helper::currentUser()['admin'] == '1') {
				return true;
			} else {
				return false;
			}
			
		}

	}
?>
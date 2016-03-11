<?php
	class sessions_helper {
		public function login() {
			$conn = Db::connect();
			$sql = 'SELECT user_id FROM users ' . 
				   ' WHERE email = ? AND password = ?';

			$email = $conn->real_escape_string($_POST['email']);
			$password = md5($_POST['password']);


			$statement = $conn->prepare($sql);
			$statement->bind_param('ss', $email, $password);
			$statement->execute();

			$statement->bind_result($userId);

			$statement->fetch();

			if($userId == NULL) {
				return false;
			} else {
				session_start();
				$_SESSION['user_id'] = $userId;

				if(isset($_SESSION['cart']) && $_SESSION['cart'] != []) {
					require_once('../app/models/Cart.php');
					$cart = new Cart;
					foreach ($_SESSION['cart'] as $product_id => $value) {
						$cart->addProductToCart($product_id, $_SESSION['cart'][$product_id]['cart_quantity']);
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
			if (Sessions_helper::currentUser()['admin'] == '1') {
				return true;
			} else {
				return false;
			}
		}

	}
?>
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
					foreach ($_SESSION['cart'] as $product_id => $quantity) {
						$Product->addToCart($product_id, $quantity);
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
			if(isset($_SESSION['user_id'])) {
				if(isset($this->currentUser)) {
					return $this->currentUser;
				} else {
					$this->currentUser = User::findBy('user_id', $_SESSION['user_id']);
					return $this->currentUser;
				}
			}
		}

	}
?>
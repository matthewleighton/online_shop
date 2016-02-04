<?php
	class sessions_helper {
		public function login() {
			
			$conn = Db::connect();
			$sql = "SELECT id FROM users WHERE email = '" . $_POST['email'] . "' AND password = '" . md5($_POST['password']) . "'";
			$result = $conn->query($sql)->fetch_assoc();
			if($result == NULL) {
				return false;
			} else {
				session_start();
				$_SESSION['id'] = $result['id'];
				return true;
			}
		}


		public function logged_in() {
			if(session_id() == "") {
				session_start();
			}
			if(isset($_SESSION['id'])) {
				return true;
			} else {
				return false;
			}
		}

		public function currentUser() {
			if(isset($_SESSION['id'])) {
				if(isset($this->currentUser)) {
					return $this->currentUser;
				} else {
					$this->currentUser = User::findBy('id', $_SESSION['id']);
					return $this->currentUser;
				}
			}
		}

	}
?>
<?php
	class Main_helper {
		public function login() {
			
			$conn = Db::connect();
			$sql = "SELECT id FROM users WHERE email = '" . $_POST['email'] . "' AND password = '" . md5($_POST['password']) . "'";
			if($result = $conn->query($sql)) {
				$row = $result->fetch_assoc();
				session_start();
				$_SESSION['id'] = $row['id'];
				print_r($_SESSION['id']);
				session_destroy();
			}

/*

			$user = $conn->query($sql);
			$row = fetch_assoc($user);

			session_start();
			if(isset($user)) {
				$_SESSION['user'] = mysql_fetch_assoc($user);
			}
			print_r($_SESSION['user']);
*/

/*
			if(isset($_SESSION['id'])) {
				echo $_SESSION['id'];
			} else {
				echo "Not logged in";
			}
*/
			/*session_start();
			echo 'Started session<br>';
			if(!isset($_SESSION['color'])) {
				$_SESSION['color']	= 'pink';
			}
			echo $_SESSION['color'];*/
		}
	}
?>
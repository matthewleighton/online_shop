<?php
	class Sessions extends Controller {
		public function __construct() {
			require_once('../app/models/User.php');
		}

		public function index() {
			echo 'The page you were looking for could not be found.';
		}

		public function login() {
			if(array_key_exists('email', $_POST)) {
				Main_helper::login();
			}

			$view = new View('layouts/register_login', ['header' => false, 'footer' => false]);
			$view->set_title('Login');
			$view->load_page();
		}
	}
?>
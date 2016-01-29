<?php
	class Sessions extends Controller {
		public function index() {
			echo 'The page you were looking for could not be found.';
		}

		public function login() {
			$view = new View('sessions/login', ['header' => false, 'footer' => false]);
			$view->set_title('Login');
			/*$view->pass_data('user', $user);*/
			$view->load_page();
		}
	}
?>
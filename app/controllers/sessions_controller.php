<?php
	class Sessions extends Controller {
		public function __construct() {
			require_once('../app/models/User.php');
		}

		public function index() {
			$this->redirect_to('');
		}

		public function login($error = false) {
			if(isset($_POST['email']) &&
			   isset($_POST['password']) &&
			   $_POST['email'] != '' &&
			   $_POST['password'] != '') {
				
				if(Sessions_helper::login()) {
					if(array_key_exists('redirect', $_POST)) {
						$this->redirect_to($_POST['redirect']);
					} else {
						$this->redirect_to('home/index');	
					}
					
				} else {
					$_POST['email'] = '';
					$_POST['password'] = '';
					$this->login(true);
				}
			}

			$view = new View('layouts/register_login', ['header' => false, 'footer' => false]);
			$view->set_title('Login');
			$view->pass_data('loginError', $error);
			$view->load_page();
		}

		public function logout() {
			session_start();
			session_destroy();
			$this->redirect_to('home/index');
		}
	}
?>
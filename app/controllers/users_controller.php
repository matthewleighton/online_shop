<?php
	class Users extends Controller {
		protected $user;

		public function __construct() {
			require_once('../app/helpers/users_helper.php');
			require_once('../app/models/User.php');
		}

		public function index() {

		}

		public function newUser() {
			$user = new User;
			if(isset($_POST['first_name'])){
				$user->assignProperties();
				if($user->createUser()) {
					Sessions_helper::login();
					$this->redirect_to();
				}
			}

			$view = new View('layouts/register_login', ['header' => false, 'footer' => false]);
			$view->set_title('Create account');
			$view->pass_data('user', $user);
			$view->load_page();
		}

	}
?>
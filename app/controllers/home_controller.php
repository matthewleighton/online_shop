<?php
	class Home extends Controller {
		
		protected $user;

		public function __construct() {
			$this->user = $this->load_model('User');
		}

		public function index($name = '') {
			$view = new View('home/index');
			$view->load_page();
			//unset($_SESSION['checkout']);
		}

		public function test() {
			$this->load_view('home/test');
		}

	}
?>
<?php
	class Home extends Controller {
		
		protected $user;

		public function __construct() {
			$this->user = $this->load_model('User');
		}

		public function index($name = '') {
			require_once('../app/models/Product.php');
			$featuredProducts = Product::findRandomProducts('5');

			$view = new View('home/index');
			$view->pass_data('featuredProducts', $featuredProducts);
			$view->load_page();
		}

		public function test() {
			$this->load_view('home/test');
		}

	}
?>
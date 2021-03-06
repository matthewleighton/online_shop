<?php
	class Home extends Controller {
		
		protected $user;

		public function __construct() {
			$this->user = $this->load_model('User');
		}

		public function index($name = '') {
			require_once('../app/models/Product.php');
			$featuredProductsIds = Product::findRandomProductIds('18');

			$view = new View('home/index');
			$view->pass_data('featuredProductsIds', $featuredProductsIds);
			$view->load_page();
		}

		public function test() {
			$this->load_view('home/test');
		}

	}
?>
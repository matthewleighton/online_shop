<?php
	class Products extends Controller {
		
		public $product;

		public function __construct() {
			require_once('../app/models/Product.php');
		}

		public function index($catagory) {
			$view = new View('products/index');
			$view->set_title('Products');
			$view->load_page();
		}

		public function books() {
			require_once('../app/models/Book.php');
			$this->books = new Book;
			$books = $this->books->findAll();

			$view = new View('products/books');
			$view->set_title('Books');
			$view->pass_data('products', $books);
			$view->load_page();
		}

		public function search() {
			
		}

	}
?>
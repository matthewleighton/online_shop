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

		public function search() {
			
		}

		public function catagory($catagory) {
			if(substr($catagory, -1) == "s") {
				$catagory = substr($catagory, 0, strlen($catagory) - 1);
			}

			if(file_exists('../app/models/' . $catagory . '.php')) {
				require_once('../app/models/' . $catagory . '.php');
				$this->model = new $catagory;
				$list = $this->model->findAll();

				$view = new View('products/results');
				$view->set_title(ucfirst($catagory) . 's');
				$view->pass_data('products', $list);	
			} else {
				$this->redirect_to('home/index');
			}
			
			$view->load_page();
		}

		public function item($id = '') {
				require_once('../app/models/product.php');
				$model = new Product;
				$product = $model->findById($id);
				//print_r($product);

				$view = new View('products/item');
				$view->set_title($product['product_name']);
				$view->pass_data('product', $product);
				$view->load_page();
		}

	}
?>
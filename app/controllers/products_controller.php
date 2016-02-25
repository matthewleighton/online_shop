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

		public function create() {
			if (!Sessions_helper::userIsAdmin()) {
				$this->redirect_to();
			}

			if (count($_POST) > 0) {
				if ($_POST['product_catagory'] == "book") {
					// Split authors into array
					if ($_POST['authors'] != '') {
						$authorsList = explode(',', $_POST['authors']);
						foreach ($authorsList as $key => $author) {
							$authorsList[$key] = trim($author);
						}
						$_POST['authors'] = $authorsList;
					}

					require_once('../app/models/book.php');
					$product = new Book;
					$product->build();
				}
			} else {
				$product = new Product();
			}

			$view = new View('products/create', ['header' => false, 'footer' =>false]);
			$view->set_title('Add product');

			$view->pass_data('product', $product);

			$view->load_page();
		}

		public function search() {
			echo "This is the product search function<br><br>";
			var_dump($_POST);
		}

		public function catagory($catagory) {
			if (substr($catagory, -1) == "s") {
				$catagory = substr($catagory, 0, strlen($catagory) - 1);
			}

			if(file_exists('../app/models/' . $catagory . '.php')) {
				require_once('../app/models/' . $catagory . '.php');
				$this->model = new $catagory;
				$list = $this->model->findAll($catagory . "." . $catagory . "_id");

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

				$view = new View('products/item');
				$view->set_title($product['product_name']);
				$view->pass_data('product', $product);
				$view->load_page();
		}
	}
?>
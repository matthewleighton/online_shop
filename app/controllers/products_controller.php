<?php
	class Products extends Controller {
		
		public $product;

		public function __construct() {
			require_once('../app/models/Product.php');
		}

		public function index($catagory = "book") {
			$view = new View('products/index');
			$view->set_title('Products');
			$view->load_page();
		}

		public function create() {
			if (!Sessions_helper::userIsAdmin()) {
				$this->redirect_to();
			}

			if (count($_POST) > 0) {
				switch ($_POST['product_catagory']) {
					case 'book':
						$this->explodeCreatorList('author');
						break;
					case 'film':
						$this->explodeCreatorList('director');
						break;
				}

				require_once('../app/models/' . $_POST['product_catagory'] . '.php');
				$product = new $_POST['product_catagory'];
				$productId = $product->build($_POST['product_catagory']);
				if ($productId != 0) {
					$this->redirect_to('products/item/' . $productId);
					break;	
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
			if (!isset($_GET['search'])) {
				$this->redirect_to('');
				break;
			}

			#$where = 'WHERE ((product_name LIKE %?%) OR (product_description LIKE %?%) OR person_name LIKE %?%)) ';
			




			$where = [];
			$where['sql'] = 'WHERE ((product_name LIKE ?) OR (product_description LIKE ?) OR (person_name LIKE ?)) ';
			$where['datatypes'] = 'sss';
			$where['values'] = [];
			for ($i=0; $i < 3; $i++) { 
				array_push($where['values'], '%' . $_GET['search'] . '%');
			}

			// Uncomment this, and comment out the above $where in order to turn this back to original functionality.
			/*$where = "WHERE ((product_name LIKE '%" . $_GET['search'] . "%') " .
					 "OR (product_description LIKE '%" . $_GET['search'] . "%') " .
					 "OR (person_name LIKE '%" . $_GET['search'] . "%')) ";*/
			

			$validCatagories = ['book', 'film'];
			if ($_GET['catagory'] != 'all' && in_array($_GET['catagory'], $validCatagories)) {
					$where['sql'] .= "AND product_catagory='" . $_GET['catagory'] . "' ";
			}

			$join = ['madeby' => ['product_id', 'fk_madeby_product'],
					 'person' => ['fk_madeby_person', 'person_id']];

			require_once('../app/models/Product.php');
			$searchResults = Product::findProducts($where, $join);
			
			$view = new View('products/results');
			$view->set_title("Search results - '" . $_GET['search'] . "'");
			$view->pass_data('products', $searchResults);
			$view->load_page();
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

			#var_dump($list);
			
			$view->load_page();
		}

		public function item($id = '') {
			if ($id == '464532') {
				$this->redirect_to('');
			} else {
				require_once('../app/models/product.php');
				//$model = new Product;
				//$product = $model->findById($id);
				$product = Product::findByProductId($id);

				$view = new View('products/item');
				$view->set_title($product['product_name']);
				$view->pass_data('product', $product);
				$view->load_page();	
			}
		}
	}
?>
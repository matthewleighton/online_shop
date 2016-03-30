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

				require_once('../app/models/' . ucfirst($_POST['product_catagory']) . '.php');
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

			$join = ['madeby' => ['fk_madeby_base_product', 'base_product_id'],
					 'person' => ['person_id', 'fk_madeby_person']];

			require_once('../app/models/Product.php');
			$searchResults = Product::findProducts($where, $join);
			$orderedResults = Product::groupByBaseProduct($searchResults);
			
			$view = new View('products/results');
			$view->set_title("Search results - '" . $_GET['search'] . "'");
			$view->pass_data('products', $orderedResults);
			$view->load_page();
		}

		# TODO - This should search for base products, not versions.
		public function catagory($catagory) {
			if (substr($catagory, -1) == "s") {
				$catagory = substr($catagory, 0, strlen($catagory) - 1);
			}


			if(file_exists('../app/models/' . ucfirst($catagory) . '.php')) {
				require_once('../app/models/' . ucfirst($catagory) . '.php');
				$this->model = new $catagory;
				$products = $this->model->findAll($catagory);
				$baseProducts = $this->model->groupByBaseProduct($products);

				$view = new View('products/results');
				$view->set_title(ucfirst($catagory) . 's');
				$view->pass_data('products', $baseProducts);
			} else {
				$this->redirect_to('home/index');
			}
			
			$view->load_page();
		}

		public function item($id = '') {
			require_once('../app/models/Product.php');
			$product = Product::findByProductVersionId($id);

			if ($product) {
				$catagory = ucfirst($product['product_catagory']);
				require_once('../app/models/' . $catagory . '.php');
				$model = new $catagory;
				$product = $model->splitListsToArray($product);

				$productVersions = Product::findProductVersions($product['base_product_id']);

				require_once('../app/models/Wish_list.php');

				$view = new View('products/item');
				$view->set_title($product['product_name']);

				session_start();
				if (isset($_SESSION['user_id'])) {
					$wishLists = Wish_list::findByUserId($_SESSION['user_id']);	
					$view->pass_data('wishLists', $wishLists);
				}


				$view->pass_data('product', $product);
				$view->pass_data('productVersions', $productVersions);

			} else {
				$view = new View('products/not_found');
				$view->set_title('Product not found');
			}
			
			$view->load_page();
		}
	}
?>
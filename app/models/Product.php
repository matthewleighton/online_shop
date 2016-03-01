<?php
	class Product extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product";

		public $productColumns = ['product_name', 'price', 'product_description',
								  'release_date', 'product_catagory'];

		public $properties = array(
			"product_name" => "",
			"price" => "",
			"product_description" => "",
			"release_date" => "",
			"product_catagory" => ""
			);

		// Specifies how to correctly use joins, etc for this table in queries.
		/*protected $sqlOptions = ['join' => ['book' => ['book.product_id', 'product.product_id'],
							 	 			'author_book' => ['author_book.book_id', 'book.book_id'],
								 			'author' => ['author.author_id', 'author_book.author_id']],
								 'concat' => [['author.author_name', 'authors']],
								 'groupby' => 'product.product_id'];
		*/

		public function __construct() {
			$this->validates('product_name', 'presence');
			$this->validates('product_name', 'length', ['minimum' => 2]);
		}

		public function addToCart($product_id, $quantity) {
			$sql = "SELECT * FROM shopping_cart WHERE fk_shopping_cart_user='" . $_SESSION['user_id'] .
				   "' AND fk_shopping_cart_product='" . $product_id . "'";
			$conn = Db::connect();
			$results = $conn->query($sql);

			if($results->num_rows > 0) {
				// Increment quantity
				$sql = "UPDATE shopping_cart SET cart_quantity = cart_quantity + " . intval($quantity) . 
						" WHERE fk_shopping_cart_user='" . $_SESSION['user_id'] . 
						"' AND fk_shopping_cart_product='" . $product_id . "'";
				$conn->query($sql);
			} else {
				// Add new entry to cart
				$sql = "INSERT INTO shopping_cart (fk_shopping_cart_user, fk_shopping_cart_product, cart_quantity)" . 
					   "VALUES ('" . $_SESSION['user_id'] . "', '" . $product_id . "', '" . $quantity . "')";
				$conn->query($sql);
			}

			$conn->close();
 		}

 		public function build() {
 			$this->assignProperties($_POST);
 			$this->runValidations();
 			if (count($this->errorsList) == 0) {
 				
 			}
 		}

 		public static function findByProductId($id) {
			/*$sql = "SELECT *";
			$where = " WHERE " . get_class($this) . "." . get_class($this) . "_id = '" . $id . "' ";
			$sql = $this->generateSearchSql($sql, $where);
			$results = $this->runSql($sql);
			
			return $results->fetch_assoc();*/

			$productCatagory = Product::findProductCatagory($id);

			switch ($productCatagory) {
				case 'book':
					require_once('../app/models/Book.php');
					$product = new Book;
					break;
				case 'film':
					require_once('../app/models/Film.php');
					$product = new Film;
					break;
			}

			$sql = "SELECT *";
			$where = " WHERE product.product_id='" . $id . "' ";
			$sql = $product->generateSearchSql($sql, $where);
			
			$results = $product->runSql($sql);
			
			return $results->fetch_assoc();			
		}

		private static function findProductCatagory($id) {
			$sql = "SELECT product_catagory FROM product WHERE product_id='" . $id . "'";
			$results = Product::runSql($sql);
			return $results->fetch_assoc()['product_catagory'];
		}

		// Generates an array of products.
		// For use when the products' types are not known
		public static function findProducts($where, $join = '') {
			$returnArray = [];
			$productsSortedByType = [];

			// Create array of product IDs/product catagories
			$sql = "SELECT product.product_id, product_catagory FROM product ";
			$sql .= $join . " " . $where;
			$results = Model::runSql($sql);
			$productTypes = Model::createResultsArray($results);
			
			// Arrange the product IDs by product catagory
			foreach ($productTypes as $product) {
				if (!array_key_exists($product['product_catagory'], $productsSortedByType)) {
					$productsSortedByType[$product['product_catagory']] = [];
				}

				array_push($productsSortedByType[$product['product_catagory']], $product['product_id']);
			}

			// Query the database, creating an array of products and their attributes
			// Then adding this array onto the final return array
			foreach ($productsSortedByType as $catagory => $productList) {
				require_once('../app/models/' . $catagory . '.php');
				$model = new $catagory;
				$catagoryWhere = $where;
				$catagoryWhere .= " AND product_catagory='" . $catagory . "'";
				$sql = $model->generateSearchSql('SELECT *', $catagoryWhere, $join);
				echo $sql;
				$resultsPDO = $model->runSql($sql);
				$resultsArray = $model->createResultsArray($resultsPDO);
				
				$returnArray = array_merge($returnArray, $resultsArray);
			}
			
			#var_dump($returnArray);
			#die();
			return $returnArray;
		}

	}

?>
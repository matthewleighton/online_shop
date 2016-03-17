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
			"product_catagory" => "",
			"creators" => []
			);

		public function __construct() {
			require_once('../app/helpers/products_helper.php');
			$this->validates('product_name', 'presence');
			$this->validates('product_name', 'length', ['minimum' => 2]);

			$this->sqlOptions = ['join' => ['madeby' => ['madeby.fk_madeby_product', 'product.product_id'],
									  		'person' => ['person.person_id', 'madeby.fk_madeby_person']],
						   		 'groupby' => 'product.product_id',
						   		 'concat' => []];
		}

 		public static function findByProductId($id) {
			$productCatagory = Product::findProductCatagoryById($id);
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

		private static function findProductCatagoryById($id) {
			$sql = "SELECT product_catagory FROM product WHERE product_id='" . $id . "'";
			$results = Product::runSql($sql);
			return $results->fetch_assoc()['product_catagory'];
		}

		// Generates an array of products.
		// For use when the products' types are not known
		public static function findProducts($where, $join = []) {
			$productList = Product::findProductCatagories($where, $join);
			#var_dump($productList);
			#die();

			$productIdsByCatagory = Product::sortByCatagory($productList);
			return Product::findProductDetailsByCatagory($productIdsByCatagory, $where, $join);
		}


		// Create array of product IDs/product catagories
		public static function findProductCatagories($where, $join) {
			$sql = "SELECT product.product_id, product_catagory FROM product ";
			foreach ($join as $key => $value) {
				$sql .= "JOIN " . $key . " ON " . $value[0] . "=" . $value[1] . " ";
			}

			if (is_array($where)) {
				$sql .= $where['sql'];
				$results = Model::buildAndRunPreparedStatement($sql, $where['datatypes'], $where['values']);
				return Model::createResultsArray($results);
			} else {
				$sql .= $where;
				$results = Model::runSql($sql);
				#var_dump(Model::createResultsArray($results));
				#die();
				return Model::createResultsArray($results);
			}
			
			
		}

		// Arrange product IDs by product catagory
		// $productList is an array of product IDs and catagories.
		public static function sortByCatagory($productList) {
			$returnArray = [];
			foreach ($productList as $product) {
				if (!array_key_exists($product['product_catagory'], $returnArray)) {
					$returnArray[$product['product_catagory']] = [];
				}

				array_push($returnArray[$product['product_catagory']], $product['product_id']);
			}

			return $returnArray;
		}

		// Query the database, creating an array of products and their attributes
		// Then adding this array onto the final return array
		public static function findProductDetailsByCatagory($productIds, $where, $join = []) {
			$returnArray = [];
			foreach ($productIds as $catagory => $productList) {
				require_once('../app/models/' . $catagory . '.php');
				$model = new $catagory;
				if (is_array($where)) {
					$catagoryWhere = $where['sql'];
				} else {
					$catagoryWhere = $where;
				}
				$catagoryWhere .= " AND product_catagory='" . $catagory . "'";

				// Only join to tables if the catagory sql isn't already going to join it
				$additionalJoins = '';
				foreach ($join as $table => $value) {
					if (!array_key_exists($table, $model->sqlOptions['join'])) {
						$additionalJoins .= " JOIN " . $table . " ON " . $value[0] . "=" . $value[1] . " ";
					}
				}

				$sql = $model->generateSearchSql('SELECT *', $catagoryWhere, ['join' => $additionalJoins]);

				if (is_array($where)) {
					$results = Model::buildAndRunPreparedStatement($sql, $where['datatypes'], $where['values']);
				} else {
					$results = $model->runSql($sql);
				}
				$resultsArray = $model->createResultsArray($results);
				$returnArray = array_merge($returnArray, $resultsArray);
			}

			return $returnArray;
		}

		public static function findRandomProducts($quantity) {
			$sql = "SELECT product_id, product_catagory FROM product ORDER BY RAND() LIMIT " . $quantity;
			$productIds = Product::createResultsArray(Product::runSql($sql));
			$productsByCatagory = Product::sortByCatagory($productIds);
			
			$where = " WHERE product_id IN (";
			foreach ($productIds as $product) {
				$where .= "'" . $product['product_id'] . "', ";
			}
			$where = substr($where, 0, -2) . ")";
			
			$productList = Product::findProductDetailsByCatagory($productsByCatagory, $where);
			shuffle($productList);
			return($productList);
		}

		public function build($catagory) {
			$this->assignProperties($_POST);

			if ($this->runValidations()) {

				// Saving the base product to the database
				$productTableProperties = [];		
				foreach ($this->properties as $key => $value) {
					if (in_array($key, $this->productColumns)) {
						$productTableProperties[$key] = $value;
					}
				}
				$productId = $this->saveToDb('INSERT', 'product', $productTableProperties);

				// Saving product to book table
				$catagoryTableProperties = ['fk_' . $catagory . '_product' => $productId];
				foreach ($this->properties as $key => $value) {
					if (in_array($key, $this->catagoryColumns)) {
						$catagoryTableProperties[$key] = $value;
					}
				}
				$this->saveToDb('INSERT', $catagory, $catagoryTableProperties);

				// Adding creators - authors, directors, etc
				foreach (array_keys($this->properties['creators']) as $jobTitle) {
					$creatorsArray = [];
					foreach ($_POST[$jobTitle] as $person) {
						$sql = "SELECT * FROM person WHERE person_name='" . $person . "'";
						$results = $this->runSql($sql);
						$resultsArray = $this->createResultsArray($results);

						if (isset($resultsArray[0]['person_id'])) {
							echo "That person already exists!<br><br>";
							array_push($creatorsArray, $resultsArray[0]['person_id']);
						} else {
							echo "That is a new person!<br><br>";
							$personId = $this->saveToDb('INSERT', 'person', ['person_name' => $person]);
							array_push($creatorsArray, $personId);
						}
					}
					foreach ($creatorsArray as $personId) {
						$columnValues = ['fk_madeby_person' => $personId,
										 'fk_madeby_product' => $productId,
										 'person_role' => $jobTitle];
						$this->saveToDb('INSERT', 'madeby', $columnValues);
					}
				}
				return $productId;
			}
			
		}

	}

?>
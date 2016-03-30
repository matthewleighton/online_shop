<?php
	class Product extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product_version";

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

			$this->sqlOptions = ['join' => ['base_product' => ['base_product_id', ' fk_product_version_base_product'],
											'madeby' => ['madeby.fk_madeby_base_product', 'base_product_id'],
									  		'person' => ['person.person_id', 'madeby.fk_madeby_person']],
						   		 'groupby' => 'product_version_id',
						   		 'concat' => []];
		}

		# Returns a single product, based on its version_id
 		public static function findByProductVersionId($id) {
			$productCatagory = ucfirst(Product::findProductCatagoryById($id));

			if (!$productCatagory) {
				return false;
			}
			
			require_once('../app/models/' . $productCatagory . '.php');
			$product = new $productCatagory;

			$sql = "SELECT *";
			$where = " WHERE product_version_id=? ";
			$sql = $product->generateSearchSql($sql, $where);

			$datatypes = 'i';
			$params = [$id];

			$results = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);
			
			return $results->fetch_assoc();			
		}

		# Returns a product's catagory
		private static function findProductCatagoryById($id) {
			$sql = "SELECT product_catagory FROM product_version
					LEFT JOIN base_product ON base_product_id = fk_product_version_base_product
					WHERE product_version_id=?";
			$datatypes = 'i';
			$params = [$id];
			$results = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);
			$catagory = $results->fetch_assoc()['product_catagory'];

			return ucfirst(str_replace(' ', '_', $catagory));
		}

		// Generates an array of products.
		// For use when the products' types are not known
		public static function findProducts($where, $join = []) {
			$productList = Product::findProductCatagories($where, $join);
			$productIdsByCatagory = Product::sortByCatagory($productList);
			return Product::findProductDetailsByCatagory($productIdsByCatagory, $where, $join);
		}

		// Create array of product IDs/product catagories
		public static function findProductCatagories($where, $join = []) {
			$sql = "SELECT base_product_id, product_catagory FROM base_product ";

			foreach ($join as $key => $value) {
				$sql .= "LEFT JOIN " . $key . " ON " . $value[0] . "=" . $value[1] . " ";
			}

			if (is_array($where)) {
				$sql .= $where['sql'];
				#echo "<br>" . $sql;die;
				$results = Model::buildAndRunPreparedStatement($sql, $where['datatypes'], $where['values']);
				return Model::createResultsArray($results);
			} else {
				$sql .= $where;
				$results = Model::runSql($sql);
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

				array_push($returnArray[$product['product_catagory']], $product['base_product_id']);
			}

			return $returnArray;
		}

		// Query the database, creating an array of products and their attributes
		// Then adding this array onto the final return array
		public static function findProductDetailsByCatagory($productIds, $where, $join = []) {
			$returnArray = [];
			foreach ($productIds as $catagory => $productList) {
				require_once('../app/models/' . ucfirst($catagory) . '.php');
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
					if (!array_key_exists($table, $model->sqlOptions['join']) && $table != 'product_version') {
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

				$productCount = count($resultsArray);
				for ($i=0; $i < $productCount; $i++) { 
					$resultsArray[$i] = $model->splitListsToArray($resultsArray[$i]);
				}

				$returnArray = array_merge($returnArray, $resultsArray);
			}

			return $returnArray;
		}

		public static function findRandomProductIds($quantity) {
			$sql = 'SELECT base_product_id, product_version_id FROM product_version ' . 
				   'JOIN base_product ON fk_product_version_base_product = base_product_id ORDER BY RAND() LIMIT ?';
			$datatypes = 'i';
			$params = [$quantity];

			$results = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);

			$resultsArray = Model::createResultsArray($results);

			#var_dump($resultsArray);die;

			return $resultsArray;
		}


/*
		public static function findRandomProductIds($quantity) {
			#TODO - Each product_version id must be from a different base_product
			$sql = "SELECT product_version_id FROM product_version ORDER BY RAND() LIMIT " . $quantity;
			$productIds = Product::createResultsArray(Product::runSql($sql));
			#var_dump($productIds);die;
			return $productIds;
			
			
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
*/
		/*
		public static function findRandomProductIds($quantity) {
			$sql = 'SELECT base_product_id FROM base_product ORDER BY RAND() LIMIT ?';
			$datatypes = 'i';
			$params = [$quantity];

			$baseIds = Product::createResultsArray(Product::buildAndRunPreparedStatement($sql, $datatypes, $params));

			$returnArray = [];
			$sql = 'SELECT product_version_id FROM product_version WHERE product_version_id = ?';
			foreach ($baseIds as $array => $baseId) {
				$params = [$baseId['base_product_id']];
				$versionId = Product::createResultsArray(Product::buildAndRunPreparedStatement($sql, $datatypes, $params));
				if (count($versionId > 0)) {
					echo "ergszg";
					var_dump($versionId);die;
				}
			}

			#var_dump($returnArray);die;


		}
*/


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

		# Groups product search results together by base product.
		public function groupByBaseProduct($searchResults) {
			$returnArray = [];

			foreach ($searchResults as $productVersion) {
				if (!array_key_exists($productVersion['base_product_id'], $returnArray)) {
					$returnArray[$productVersion['base_product_id']] = [];
				}
				$returnArray[$productVersion['base_product_id']][] = $productVersion;
			}

			return $returnArray;
		}

		public function findProductVersions($baseProductId) {
			$sql = "SELECT product_version_id, platform, product_price FROM product_version " .
				   "JOIN base_product on fk_product_version_base_product = base_product_id " . 
				   "WHERE base_product_id = ?";
			$datatypes = 'i';
			$params = [$baseProductId];
			$results = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);
			return Model::createResultsArray($results);
		}

		public function splitListsToArray($product) {
			if (isset($this->sqlOptions['concat'])) {
				foreach ($this->sqlOptions['concat'] as $concat) {
					#$product[$concat[1]] = 
					$attributes = preg_split('/(?<=\S),(?=\S)/', $product[$concat[1]]);
					if ($attributes[0] != '') {
						$product[$concat[1]] = $attributes;
					} else {
						unset($product[$concat[1]]);
					}
				}
			}

			return $product;
		}

	}

?>
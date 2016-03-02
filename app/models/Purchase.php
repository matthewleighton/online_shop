<?php
	class Purchase extends Model {
		public $properties = array(
			'fk_purchase_user' => '',
			'fk_purchase_payment_method' => '',
			'fk_purchase_address' => '',
			'productsPrice' => '',
			'deliveryPrice' => ''
		);

		protected $table = "purchase";

		//protected $sqlOptions = ['join' => ['product_purchase' => ['purchase.purchase_id', 'product_purchase.purchase_id']]];

		public function generatePurchaseArray() {
			$purchaseList = $this->findByUserId($_SESSION['user_id']);
			$completePurchaseArray = [];
			$purchaseIdList = [];

			foreach ($purchaseList as $purchase) {
				$completePurchaseArray[$purchase['purchase_id']] = ['details' => $purchase];
				$completePurchaseArray[$purchase['purchase_id']]['products'] = [];
				array_push($purchaseIdList, $purchase['purchase_id']);
			}

			// Find all items belonging to purchases linked to current user
			$sql = "SELECT product_id, product_catagory, fk_product_purchase_purchase " .
				   "FROM product_purchase " .
				   "JOIN product ON product_id=fk_product_purchase_product " . 
				   "WHERE fk_product_purchase_purchase IN (";
			foreach ($purchaseIdList as $purchaseId) {
				$sql .= "'" . $purchaseId . "', ";
			}
			$sql = substr($sql, 0, -2) . ") ";
			
			$products = $this->createResultsArray($this->runSql($sql));

			// Sorting purchased products by caragory
			$productCatagories = [];
			foreach ($products as $product) {
				if (!isset($productCatagories[$product['product_catagory']])) {
					$productCatagories[$product['product_catagory']] = [];
				}
				array_push($productCatagories[$product['product_catagory']], $product);
			}

			// Search for the details of each purchased product, dealing with each catagory individually
			require_once('../app/models/Product.php');
			foreach ($productCatagories as $catagory => $productList) {
				require_once('../app/models/' . $catagory . '.php');
				$model = new $catagory;
				$sql = 'SELECT * ';
				
				$where = "WHERE fk_product_purchase_product IN (";
				foreach ($productList as $product) {
					$where .= "'" . $product['product_id'] . "', ";
				}
				$where = substr($where, 0, -2) . ") AND fk_product_purchase_purchase IN (";
				foreach ($productList as $product) {
					$where .= "'" . $product['fk_product_purchase_purchase'] . "', ";
				}
				$where = substr($where, 0, -2) . ")";

				$join = "JOIN product_purchase ON fk_product_purchase_product=product_id ";
				$groupby = "GROUP BY product_purchase_id";

				
				$sql = $model->generateSearchSql($sql, $where, ['join' => $join, 'groupby' => $groupby]);
				$productsOfCatagory = $this->createResultsArray($this->runSql($sql));

				foreach ($productsOfCatagory as $product) {
					array_push($completePurchaseArray[$product['fk_product_purchase_purchase']]['products'], $product);
				}
			}

			return array_reverse($completePurchaseArray);
			

			/*






			$sql = "SELECT *, GROUP_CONCAT(person.person_name) authors FROM product_purchase ";
			$sql .= "JOIN product ON product_purchase.fk_product_purchase_product=product.product_id ";
			$sql .= "JOIN book ON book.fk_book_product=product.product_id ";
			$sql .= "JOIN author ON author.fk_author_product=product.product_id ";
			$sql .= "JOIN person ON person.person_id=author.fk_author_person ";
			$sql .= "WHERE fk_product_purchase_purchase IN (";
			foreach ($purchaseIdList as $purchaseId) {
				$sql .= "'" . $purchaseId . "', ";
			}

			$sql = substr($sql, 0, -2) . ") GROUP BY product_purchase.product_purchase_id";

			
			echo $sql;
			die();

			$products = $this->createResultsArray($this->runSql($sql));

			var_dump($products);
			#die();
			
			foreach ($products as $product) {
				if (!isset($completePurchaseArray[$product['fk_product_purchase_purchase']]['products'])) {
					$completePurchaseArray[$product['fk_product_purchase_purchase']]['products'] = [];
				}
				array_push($completePurchaseArray[$product['fk_product_purchase_purchase']]['products'], $product);
			}
			
			#var_dump(array_reverse($completePurchaseArray));
			#die();
			return array_reverse($completePurchaseArray);
			*/
		}
	}
?>
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
			$sql = "SELECT product_version_id, product_catagory, fk_purchase_product_version_purchase " .
				   "FROM purchase_product_version " .
				   "JOIN product_version ON product_version_id=fk_purchase_product_version_product_version " .
				   "JOIN base_product ON base_product_id=fk_product_version_base_product " . # Do I need this line?
				   "WHERE fk_purchase_product_version_purchase IN (";
			foreach ($purchaseIdList as $purchaseId) {
				$sql .= "'" . intval($purchaseId) . "', ";
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
				require_once('../app/models/' . ucfirst($catagory) . '.php');
				$model = new $catagory;
				$sql = 'SELECT * ';
				
				$where = "WHERE fk_purchase_product_version_product_version IN (";
				foreach ($productList as $product) {
					$where .= "'" . $product['product_version_id'] . "', ";
				}
				$where = substr($where, 0, -2) . ") AND fk_purchase_product_version_purchase IN (";
				foreach ($productList as $product) {
					$where .= "'" . $product['fk_purchase_product_version_purchase'] . "', ";
				}
				$where = substr($where, 0, -2) . ")";

				$join = "JOIN purchase_product_version ON fk_purchase_product_version_product_version=product_version_id ";
				$groupby = "GROUP BY purchase_product_version_id";

				$sql = $model->generateSearchSql($sql, $where, ['join' => $join, 'groupby' => $groupby]);

				$productsOfCatagory = $this->createResultsArray($this->runSql($sql));

				foreach ($productsOfCatagory as $product) {
					array_push($completePurchaseArray[$product['fk_purchase_product_version_purchase']]['products'], $product);
				}
			}

			return array_reverse($completePurchaseArray);
		}
	}
?>
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
				array_push($purchaseIdList, $purchase['purchase_id']);
			}

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

			#echo $sql;
			#die();

			$products = $this->createResultsArray($this->runSql($sql));
			
			foreach ($products as $product) {
				if (!isset($completePurchaseArray[$product['fk_product_purchase_purchase']]['products'])) {
					$completePurchaseArray[$product['fk_product_purchase_purchase']]['products'] = [];
				}
				array_push($completePurchaseArray[$product['fk_product_purchase_purchase']]['products'], $product);
			}
			
			#var_dump(array_reverse($completePurchaseArray));
			#die();
			return array_reverse($completePurchaseArray);
			
		}
	}
?>
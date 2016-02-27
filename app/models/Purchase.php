<?php
	class Purchase extends Model {
		public $properties = array(
			'userId' => '',
			'paymentMethodId' => '',
			'addressId' => '',
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
			$sql .= "JOIN product ON product_purchase.product_id=product.product_id ";
			$sql .= "JOIN book ON book.product_id=product.product_id ";
			$sql .= "JOIN author ON author.FK_author_product=product.product_id ";
			$sql .= "JOIN person ON person.person_id=author.FK_author_person ";
			$sql .= "WHERE purchase_id IN (";
			foreach ($purchaseIdList as $purchaseId) {
				$sql .= "'" . $purchaseId . "', ";
			}

			$sql = substr($sql, 0, -2) . ") GROUP BY product_purchase.product_purchase_id";

			#echo $sql;
			#die();

			$products = $this->createResultsArray($this->runSql($sql));
			
			foreach ($products as $product) {
				if (!isset($completePurchaseArray[$product['purchase_id']]['products'])) {
					$completePurchaseArray[$product['purchase_id']]['products'] = [];
				}
				array_push($completePurchaseArray[$product['purchase_id']]['products'], $product);
			}
			
			#var_dump(array_reverse($completePurchaseArray));
			#die();
			return array_reverse($completePurchaseArray);
			
		}
	}
?>
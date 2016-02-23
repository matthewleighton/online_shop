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

			$sql = "SELECT *, GROUP_CONCAT(author.author_name) authors FROM product_purchase ";
			$sql .= "JOIN product ON product_purchase.product_id=product.product_id ";
			$sql .= "JOIN book ON book.product_id=product.product_id ";
			$sql .= "JOIN author_book ON book.book_id=author_book.book_id ";
			$sql .= "JOIN author ON author_book.author_id=author.author_id ";
			$sql .= "WHERE purchase_id IN (";
			foreach ($purchaseIdList as $purchaseId) {
				$sql .= "'" . $purchaseId . "', ";
			}

			$sql = substr($sql, 0, -2) . ") GROUP BY product_purchase.product_purchase_id";

			#echo "<br><br>" . $sql . "<br><br>";
			#die();

			$products = $this->createResultsArray($this->runSql($sql));
			
			foreach ($products as $product) {
				if (!isset($completePurchaseArray[$product['purchase_id']]['products'])) {
					$completePurchaseArray[$product['purchase_id']]['products'] = [];
				}
				array_push($completePurchaseArray[$product['purchase_id']]['products'], $product);
			}
			
			#var_dump(array_reverse($completePurchaseArray));
			return array_reverse($completePurchaseArray);
			
		}
	}
?>
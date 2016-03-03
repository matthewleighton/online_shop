<?php
	class Cart extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product";

		public function __construct($userId = '') {
			$this->userId = $userId;
		}



		// Specifies how to correctly use joins, etc for this table in queries.
		protected $sqlOptions = ['join' => ['book' => ['book.fk_book_product', 'product.product_id'],
							 	 			'author' => ['author.fk_author_product', 'product.product_id'],
								 			'person' => ['person.person_id', 'author.fk_author_person']],
								 'concat' => [['person.person_name', 'authors']],
								 'groupby' => 'product.product_id'];

		public function generateCartFromDb() {
			$where = "WHERE fk_shopping_cart_user='" . $_SESSION['user_id'] . "'";
			$join = ['shopping_cart' => ['fk_shopping_cart_product', 'product_id']];
			#$join = "JOIN shopping_cart ON fk_shopping_cart_product=product.product_id";
			require_once('../app/models/Product.php');
			$cart = Product::findProducts($where, $join);
			unset($_SESSION['cart']);

			// Also create a session cart variable, containing only IDs and quantities
			foreach($cart as $product) {
				$_SESSION['cart'][$product['product_id']] = ['cart_quantity' => $product['cart_quantity'],
															 'product_price' => $product['price']];
			}

			return $cart;
		}

		public function generateCartFromSession($cart) {
			$sql = "SELECT *";
			$where = " WHERE ";
			foreach ($cart as $product_id => $quality) {
				$where .= "(product.product_id='" . $product_id . "') OR ";
			}

			$where = rtrim($where, " OR ");
			$sql = $this->generateSearchSql($sql, $where);

			$results = $this->runSql($sql);
			$array = $this->createResultsArray($results);

			$finishedCart = [];
			foreach ($array as $product) {
				$product['cart_quantity'] = $cart[$product['product_id']]['cart_quantity'];
				array_push($finishedCart, $product);
			}

			return $finishedCart;
		}

		public function removeItem($product_id) {
			$sql = "DELETE FROM shopping_cart WHERE fk_shopping_cart_user='";
			$sql .= $_SESSION['user_id'] . "' AND fk_shopping_cart_product='" . $product_id . "'";
			$this->runSql($sql);
		}

		public function emptyCart() {
			$sql = "DELETE FROM shopping_cart WHERE fk_shopping_cart_user='" . $this->userId . "'";
			$this->runSql($sql, true);

			if (isset($_SESSION['cart'])) {
				unset($_SESSION['cart']);
			}
		}
	}
?>
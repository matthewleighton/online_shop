<?php
	class Cart extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product";

		// Specifies how to correctly use joins, etc for this table in queries.
		protected $sqlOptions = ['join' => ['book' => ['book.product_id', 'product.product_id'],
							 	 			'author_book' => ['author_book.book_id', 'book.book_id'],
								 			'author' => ['author.author_id', 'author_book.author_id']],
								 'concat' => [['author.author_name', 'authors']],
								 'groupby' => 'product.product_id'];

		public function generateCartFromDb() {
			// Add shopping_cart to the list of tables to be joined
			$this->sqlOptions['join']['shopping_cart'] = ['shopping_cart.product_id', 'product.product_id'];

			$sql = "SELECT *";
			$where = " WHERE shopping_cart.user_id = '" . $_SESSION['user_id'] . "'";			
			$sql = $this->generateSearchSql($sql, $where);

			$results = $this->runSql($sql);
			
			$cart = $this->createResultsArray($results);

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
			$sql = "DELETE FROM shopping_cart WHERE user_id='";
			$sql .= $_SESSION['user_id'] . "' AND product_id='" . $product_id . "'";
			$this->runSql($sql);
		}
	}
?>
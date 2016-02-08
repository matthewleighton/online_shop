<?php
	class Cart extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product";

		// Specifies how to correctly use joins, etc for this table in queries.
		protected $sqlOptions = ['join' => ['book' => ['book.product_id', 'product.product_id'],
							 	 			'author_book' => ['author_book.book_id', 'book.book_id'],
								 			'author' => ['author.author_id', 'author_book.author_id'],
								 			'shopping_cart' => ['shopping_cart.product_id', 'product.product_id']],
								 'concat' => [['author.author_name', 'authors']],
								 'groupby' => 'product.product_id'];

		public function generateCart() {
			$sql = "SELECT *";
			$where = " WHERE shopping_cart.user_id = '" . $_SESSION['user_id'] . "'";

			$sql = $this->generateSearchSql($sql, $where);
			$results = $this->searchDb($sql);
			$cart = $this->createResultsArray($results);
			$_SESSION['cart'] = $cart;

			return $cart;
		}

		public function removeItem($product_id) {
			$sql = "DELETE FROM shopping_cart WHERE user_id='";
			$sql .= $_SESSION['user_id'] . "' AND product_id='" . $product_id . "'";
			$this->searchDb($sql);
		}
	}
?>
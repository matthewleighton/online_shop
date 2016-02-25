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
			"product_catagory" => ""
			);

		// Specifies how to correctly use joins, etc for this table in queries.
		/*protected $sqlOptions = ['join' => ['book' => ['book.product_id', 'product.product_id'],
							 	 			'author_book' => ['author_book.book_id', 'book.book_id'],
								 			'author' => ['author.author_id', 'author_book.author_id']],
								 'concat' => [['author.author_name', 'authors']],
								 'groupby' => 'product.product_id'];
		*/

		public function __construct() {
			$this->validates('product_name', 'presence');
			$this->validates('product_name', 'length', ['minimum' => 2]);
		}

		public function addToCart($product_id, $quantity) {
			$sql = "SELECT * FROM shopping_cart WHERE user_id='" . $_SESSION['user_id'] .
				   "' AND product_id='" . $product_id . "'";
			$conn = Db::connect();
			$results = $conn->query($sql);

			if($results->num_rows > 0) {
				// Increment quantity
				$sql = "UPDATE shopping_cart SET cart_quantity = cart_quantity + " . intval($quantity) . 
						" WHERE user_id='" . $_SESSION['user_id'] . "' AND product_id='" . $product_id . "'";
				$conn->query($sql);
			} else {
				// Add new entry to cart
				$sql = "INSERT INTO shopping_cart (user_id, product_id, cart_quantity) VALUES ('";
				$sql .= $_SESSION['user_id'] . "', '" . $product_id . "', '" . $quantity . "')";
				$conn->query($sql);
			}

			$conn->close();
 		}

 		public function build() {
 			$this->assignProperties($_POST);
 			$this->runValidations();
 			if (count($this->errorsList) == 0) {
 				
 			}
 		}

	}

?>
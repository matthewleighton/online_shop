<?php
	class Cart extends Model {
		// Identifies which table needs to be searched when this model is used.
		public $table = "product";

		public function __construct($userId = '') {
			require_once('../app/helpers/carts_helper.php');
			$this->userId = $userId;
		}

		// Specifies how to correctly use joins, etc for this table in queries.
		protected $sqlOptions = ['join' => ['book' => ['book.fk_book_product', 'product.product_id'],
							 	 			'author' => ['author.fk_author_product', 'product.product_id'],
								 			'person' => ['person.person_id', 'author.fk_author_person']],
								 'concat' => [['person.person_name', 'authors']],
								 'groupby' => 'product.product_id'];

		public function generateCartFromDb() {
			$where = [];
			$where['sql'] = 'WHERE fk_shopping_cart_user = ? ';
			$where['datatypes'] = 'i';
			$where['values'] = [$_SESSION['user_id']];
			
			$join = [];
			$join['product_version'] = ['fk_product_version_base_product', 'base_product_id'];
			$join['shopping_cart'] = ['fk_shopping_cart_product_version', 'product_version_id'];

			require_once('../app/models/Product.php');
			$cart = Product::findProducts($where, $join);
			unset($_SESSION['cart']);

			// Also create a session cart variable, containing only IDs and quantities - used in checkout.
			foreach($cart as $product) {
				$_SESSION['cart'][$product['product_version_id']] = ['cart_quantity' => $product['cart_quantity'],
																	 'product_price' => $product['product_price']];
			}

			return $cart;
		}

		public function generateCartFromSession($cart) {
			$where = [];
			$where['sql'] = 'WHERE product_version_id IN (';
			$where['datatypes'] = '';
			$where['values'] = [];

			foreach ($cart as $productVersionId => $quantity) {
				$where['sql'] .= ' ?, ';
				$where['datatypes'] .= 'i';
				array_push($where['values'], $productVersionId);
			}
			$where['sql'] = substr($where['sql'], 0, -2) . ') ';

			$join = [];
			$join['product_version'] = ['fk_product_version_base_product', 'base_product_id'];
			
			require_once('../app/models/Product.php');
			$returnCart = Product::findProducts($where, $join);

			foreach ($returnCart as $key => $product) {
				$returnCart[$key]['cart_quantity'] = $cart[$product['product_version_id']]['cart_quantity'];
				
			}

			return $returnCart;
		}

		public function addProductToCart($productVersionId, $quantity) {
			$conn = Db::connect();
			if(carts_helper::alreadyInCart($productVersionId)) {
				carts_helper::incrementProductQuantity($productVersionId, $quantity);
			} else {
				$sql = "INSERT INTO shopping_cart (fk_shopping_cart_user, fk_shopping_cart_product_version, cart_quantity) " . 
					   "VALUES (?, ?, ?)";
				$statement = $conn->prepare($sql);
				$statement->bind_param('iii', $_SESSION['user_id'], $productVersionId, $quantity);
				$statement->execute();
			}
		}

		public function removeItem($productVersionId) {
			$sql = 'DELETE FROM shopping_cart WHERE fk_shopping_cart_user = ? AND fk_shopping_cart_product_version = ?';
			
			$database = Db::connect();
			$statement = $database->prepare($sql);

			$statement->bind_param('ii', $_SESSION['user_id'], $productVersionId);
			$statement->execute();
		}

		public function emptyCart() {
			$sql = 'DELETE FROM shopping_cart WHERE fk_shopping_cart_user = ?';
			$database = Db::connect();
			$statement = $database->prepare($sql);
			$statement->bind_param('i',  $_SESSION['user_id']);
			$statement->execute();

			if (isset($_SESSION['cart'])) {
				unset($_SESSION['cart']);
			}
		}
	}
?>
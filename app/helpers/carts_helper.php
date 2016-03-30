<?php
	class carts_helper {
		// Returns true if the product already exists in the user's cart
		public static function alreadyInCart($productId) {
			$sql = 'SELECT cart_id FROM shopping_cart ' . 
				   'WHERE fk_shopping_cart_user = ? AND fk_shopping_cart_product_version = ?';
			
			$conn = Db::connect();
			
			$statement = $conn->prepare($sql);
			$statement->bind_param('ii', $_SESSION['user_id'], $productId);
			$statement->execute();
			$statement->bind_result($cardId);

			$result = $statement->fetch();

			$conn->close();

			return $result;
		}

		public static function incrementProductQuantity($productId, $quantity) {
			$sql = "UPDATE shopping_cart SET cart_quantity = cart_quantity + ? " . 
				   "WHERE fk_shopping_cart_user = ? AND fk_shopping_cart_product_version = ?";

			$conn = Db::connect();

			$statement = $conn->prepare($sql);
			$statement->bind_param('iii', $quantity, $_SESSION['user_id'], $productId);
			$statement->execute();
		}
	}
?>
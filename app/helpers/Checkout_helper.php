<?php
	class Checkout_helper {
		// Return true if the chosen address belongs to the user
		public static function confirmAddressOwnership($addressId) {
			$sql = 'SELECT address_id FROM address WHERE address_id = ? AND fk_address_user = ?';
			
			return Checkout_helper::runConfirmationQuery($sql, $addressId);
		}

		// Return true if the chosen card belongs to the user
		public static function confirmCardOwnership($paymentId) {
			$sql = 'SELECT payment_method_id from payment_method WHERE payment_method_id = ? AND fk_payment_method_user = ?';

			return Checkout_helper::runConfirmationQuery($sql, $paymentId);
		}

		private function runConfirmationQuery($sql, $id) {
			
			$conn = Db::connect();
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('ii', $id, $_SESSION['user_id']);
			$stmt->execute();
			$stmt->bind_result($id);
			
			$stmt->fetch();
			$conn->close();

			$_SESSION['test'] = $id;

			return $id;
		}
	}
?>
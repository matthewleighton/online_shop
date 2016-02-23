<?php
	class Account extends Controller {
		public function __construct() {
			$this->mustBeLoggedIn();
		}
		public function index() {
			$view = new View('account/index');
			$view->set_title('Your Account');
			$view->load_page();
		}

		public function orders() {
			require_once('../app/models/purchase.php');
			$model = new Purchase;
			$purchaseList = $model->generatePurchaseArray();

			$view = new View('purchases/index');
			$view->set_title('Your Orders');
			$view->pass_data('purchaseList', $purchaseList);

			$view->load_page();
		}

		public function paymentMethods() {
			echo "this is the payment methods page";
		}

		public function addresses() {
			echo "this is the addresses page";
		}
	}
?>
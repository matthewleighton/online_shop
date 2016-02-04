<?php
	Class Cart extends Controller {

		public function __construct() {

		}

		public function index() {
			if(!Sessions_helper::logged_in()) {
				$this->redirect_to('sessions/login');
			} else {
				$view = new View('cart/index');
				$view->load_page();
			}
		}
	}

?>
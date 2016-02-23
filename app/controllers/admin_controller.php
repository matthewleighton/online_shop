<?php
	class Admin extends Controller {
		public function __construct() {
			if (!Sessions_helper::userIsAdmin()) {
				$this->redirect_to();
			}
			#var_dump(Sessions_helper::userIsAdmin());
		}

		public function index() {
			echo "This is the admin controller";
		}
	}
?>
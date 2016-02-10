<?php
	Class checkout extends Controller {
		public function index() {
			if(!Sessions_helper::logged_in()) {
				$this->redirect_to('sessions/login?redirect=checkout');
			}
		}
	}
?>
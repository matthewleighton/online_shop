<?php
	class Controller {
		public function load_model($model) {
			require_once('../app/models/' . $model . '.php');
			return new $model;
		}

		public function load_view($view, $data = []) {
			require_once('../app/views/' . $view . '.php');
		}

		public function redirect_to($location = '') {
			header('location: http://localhost/online_shop/public/' . $location);
		}

		protected function currentMethod() {
			$url = $_SERVER['REQUEST_URI'];
			return substr($url, strrpos($url, '/') + 1);
		}

		protected function mustBeLoggedIn() {
			if (!Sessions_helper::logged_in()) {
				$this->redirect_to('sessions/login?redirect=' . $_GET['url']);
			}
		}

		protected function explodeCreatorList($role) {
			if ($_POST[$role] != '') {
				$personList = explode(',', $_POST[$role]);
				foreach ($personList as $key => $person) {
					$personList[$key] = trim($person);
				}
				$_POST[$role] = $personList;
			}
		}
	}
?>
<?php
	class Model {
		
		public $validationsList = array();
		public $errorsList = array();

/* ---------- Validation functions ---------- */

		// Add items to the validations list which will later be validated
		public function validates($attr, $quality, $value = '', $error = '') {
			
			if(!array_key_exists($attr, $this->validationsList)) {
				$this->validationsList[$attr] = [];
			}

			if(!array_key_exists($quality, $this->validationsList[$attr])) {
				$this->validationsList[$attr][$quality] = [];
			}

			if(is_array($value)){
				foreach($value as $v => $v2) {
					$this->validationsList[$attr][$quality][$v] = $v2;
				}	
			} else {
				$this->validationsList[$attr][$quality] = $value;
			}

			//echo '<br>';
			//print_r($this->validationsList);
			//echo '<br>';
		}

		// Loop through the validations held in the validations list
		public function runValidations() {
			$this->errorsList = array();
			foreach ($this->validationsList as $attr => $quality) {
				foreach ($quality as $quality => $value) {
					$this->validation($attr, $quality, $value);
				}
			}

			if(empty($this->errorsList)) {
				return true;
			} else {
				return false;
			}
		}

		// Run an individual validation
		private function validation($attr_name, $quality, $value) {
			if($attr_name == 'password') {
				$attr = $_POST['password'];
				if(array_key_exists('password_confirmation', $_POST) && !array_key_exists('password', $this->errorsList)) {
					if($_POST['password'] != $_POST['password_confirmation']) {
						$this->addError('password', ' and password confirmation must match.');
						$this->addError('password_confirmation');
					}
				}
			} else {
				$attr = $this->properties[$attr_name];
			}
		
			// Don't test for anything else if a presence test is failing.
			if($attr == null && array_key_exists('presence', $this->validationsList[$attr_name])) {
				if($quality == 'presence') {
					$this->addError($attr_name, ' cannot be empty.');
				}
			} else {
				switch($quality) {
					case 'length':
						if(array_key_exists('maximum', $value)) {
							if(strlen($attr) > $value['maximum']) {
								$this->addError($attr_name, ' is too long.');
							}
						}

						if(array_key_exists('minimum', $value)) {
							if(strlen($attr) < $value['minimum']) {
								$this->addError($attr_name, ' is too short.');
							}
						}
					break;
					case 'match':
					case 'no_match':
						$result = preg_match($value[0], $attr);
						if(($result == true && $quality == 'no_match') || ($result == false && $quality == 'match')) {
							$this->addError($attr_name, $this->validationsList[$attr_name][$quality][1]);
						}
					break;
					case 'unique':
						$conn = Db::connect();
						$sql = 'SELECT *' . ' FROM ' . get_class($this) . 's WHERE ' . $attr_name . "='" . $_POST[$attr_name] . "'";
						$results = $conn->query($sql);
				
						if($results->num_rows > 0) {
							$this->addError($attr_name, ' address is already in use.');
						}
					break;
				}
			}
		}

		// Add an error message to the errors list
		private function addError($attr, $error = '') {
			$attr_clean_name = ucfirst(str_replace('_', ' ', $attr));
			if(!array_key_exists($attr, $this->errorsList)) {
				$this->errorsList[$attr] = array();
			}
			
			if($error != '') {
				array_push($this->errorsList[$attr], $attr_clean_name . $error);
			}
		}

		// Generates the sql needed to add entries to the database
		protected function generateSql($method, $column, $entries) {
			$sql = $method . ' ' . $column . ' (';

			foreach(array_keys($this->properties) as $key) {
				$sql .= $key . ', ';
			}

			if(array_key_exists('password', $_POST)) {
				$sql .= 'password, ';
			}

			$sql = rtrim($sql, ', ') . ') VALUES (';

			foreach($this->properties as $value) {
				$sql .= "'" . $value . "', ";
			}

			if(array_key_exists('password', $_POST)) {
				$sql .= "'" . md5($_POST['password']) . "', ";
			}

			$sql = rtrim($sql, ', ') . ')';
			return $sql;
		}

		protected function saveToDatabase($sql) {
			$conn = Db::connect();
			$conn->query($sql);
			$conn->close();
			return true;
		}

/* ---------- Search functions ---------- */

		// Creates the sql required to search and properly join the required tables
		protected function generateSearchSql($sql, $where = '') {
			
			if(isset($this->sqlOptions['concat'])) {
				foreach ($this->sqlOptions['concat'] as $concat => $value) {
					$sql .= ", GROUP_CONCAT(" . $value[0] . ") " . $value[1];
				}
			}

			$sql .= " FROM " . $this->table;

			if(isset($this->sqlOptions['join'])) {
				foreach($this->sqlOptions['join'] as $join => $on) {
					$sql .= " JOIN " . $join . " ON " . $on[0] . " = " . $on[1];
				}
			}

			$sql .= $where;

			if(isset($this->sqlOptions['groupby'])) {
				$sql .= " GROUP BY " . $this->sqlOptions['groupby'];
			}

			return $sql;
		}

		protected function searchDb($sql) {
			$conn = Db::connect();
			$results = $conn->query($sql);
			$conn->close();
			return $results;
		}

		protected function createResultsArray($results) {
			$array = [];
			//var_dump($results);
			while($row = $results->fetch_assoc()) {
				array_push($array, $row);
			}

			return $array;
		}

		public function findAll() {
			$sql = "SELECT *";
			$sql = $this->generateSearchSql($sql);
			$results = $this->searchDb($sql);

			return $this->createResultsArray($results);
		}

		public function findById($id) {
			$sql = "SELECT *";
			$where = " WHERE " . get_class($this) . "." . get_class($this) . "_id = '" . $id . "' ";
			$sql = $this->generateSearchSql($sql, $where);
			$results = $this->searchDb($sql);
			
			return $results->fetch_assoc();
		}

		

	}
?>
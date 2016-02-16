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

		// Assign values to the object's properties array, based on info submitted via POST
		public function assignProperties() {
			foreach(array_keys($_POST) as $postKey) {
				
				if(array_key_exists($postKey, $this->properties)) {
					$this->properties[$postKey] = $_POST[$postKey];
				}

				if(isset($this->{$postKey})) {
					$this->{$postKey} = $_POST[$postKey];
				}
			}
		}

/* ---------- SQL functions ---------- */

		// Generates the sql needed to add entries to the database
		protected function generateSql($method, $column, $entries) {
			$sql = $method . ' ' . $column . ' (';

			foreach(array_keys($entries) as $key) {
				$sql .= $key . ', ';
			}

			if(array_key_exists('password', $_POST)) {
				$sql .= 'password, ';
			}

			$sql = rtrim($sql, ', ') . ') VALUES (';

			foreach($entries as $value) {
				$sql .= "'" . $value . "', ";
			}

			if(array_key_exists('password', $_POST)) {
				$sql .= "'" . md5($_POST['password']) . "', ";
			}

			$sql = rtrim($sql, ', ') . ')';

			return $sql;
		}
		
		// Run an SQL statement
		// $returnId can be set to true in order to return the id of the newly created entry.
		protected function runSql($sql, $returnId = false) {
			$conn = Db::connect();
			$results = $conn->query($sql);
			
			if($returnId == true) {
				$id = $conn->insert_id;
				$conn->close();
				return $id;
			}

			$conn->close();
			return $results;
		}
		
		// Run an object's validations and save it to the database.
		public function saveToDb($sqlMethod, $table, $data) {
			if($this->runValidations()) {
				$sql = $this->generateSql($sqlMethod, $table, $data);
				echo $sql;
				$results = $this->runSql($sql, true);
				return $results;
			} else {
				return false;
			}
		}

		// Creates the sql to search and join the required tables for the model this is called from.
		protected function generateSearchSql($sql, $where = '') {
			if(isset($this->sqlOptions['concat'])) {
				foreach ($this->sqlOptions['concat'] as $concat => $value) {
					$sql .= ", GROUP_CONCAT(" . $value[0] . ") " . $value[1];
				}
			}

			$sql .= " FROM " . $this->table;

			if(isset($this->sqlOptions['join'])) {
				foreach($this->sqlOptions['join'] as $join => $on) {
					$sql .= " LEFT JOIN " . $join . " ON " . $on[0] . " = " . $on[1];
				}
			}

			$sql .= $where;

			if(isset($this->sqlOptions['groupby'])) {
				$sql .= " GROUP BY " . $this->sqlOptions['groupby'];
			}

			return $sql;
		}

		// Turns the PDO object returned from a query into an associative array
		protected function createResultsArray($results) {
			$array = [];
			while($row = $results->fetch_assoc()) {
				array_push($array, $row);
			}

			return $array;
		}

		public function findAll() {
			$sql = "SELECT *";
			$sql = $this->generateSearchSql($sql);
			$results = $this->runSql($sql);

			return $this->createResultsArray($results);
		}

		public function findById($id) {
			$sql = "SELECT *";
			$where = " WHERE " . get_class($this) . "." . get_class($this) . "_id = '" . $id . "' ";
			$sql = $this->generateSearchSql($sql, $where);
			$results = $this->runSql($sql);
			
			return $results->fetch_assoc();
		}

		public function findByUserId($userId) {
			$sql = "SELECT *";
			$where = " WHERE " . get_class($this) . "." . "user_id = '" . $userId ."' ";
			$sql = $this->generateSearchSql($sql, $where);
			//echo $sql; // REMOVE LATER
			
			$results = $this->runSql($sql);

			return $this->createResultsArray($results);
		}

		

	}
?>
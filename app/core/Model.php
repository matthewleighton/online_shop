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
		public function assignProperties($list) {
			#var_dump($list);
			#die();
			foreach(array_keys($list) as $attr) {
				
				if(array_key_exists($attr, $this->properties)) {
					$this->properties[$attr] = $list[$attr];
				}

				if(isset($this->{$attr})) {
					$this->{$attr} = $list[$attr];
				}
			}
		}

/* ---------- SQL functions ---------- */

		// Generates the sql needed to add entries to the database
		protected function generateSql($method, $table, $entries) {
			$sql = $method . ' ' . $table . ' (';

			foreach(array_keys($entries) as $key) {
				$key = $this->toCamelCase($key);
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

			#echo $sql;
			#die();

			return $sql;
		}
		
		// Run an SQL statement
		// $returnId can be set to true in order to return the id of the newly created entry.
		public function runSql($sql, $returnId = false) {
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
				$entryId = $this->runSql($sql, true);
				
				$this->id = $entryId;
				return $entryId;
			} else {
				return false;
			}
		}

		// Creates the sql to search and join the required tables for the model this is called from.
		protected function generateSearchSql($sql, $where = '', $options = []) {
			if(isset($this->sqlOptions['concat'])) {
				foreach ($this->sqlOptions['concat'] as $concat => $value) {
					$sql .= ", GROUP_CONCAT(" . $value[0] . ") AS " . $value[1];
				}
			}

			$sql .= " FROM " . $this->table;

			if(isset($this->sqlOptions['join'])) {
				foreach($this->sqlOptions['join'] as $join => $on) {
					$sql .= " LEFT JOIN " . $join . " ON " . $on[0] . " = " . $on[1];
				}
			}

			if (isset($options['join'])) {
				$sql .= " " . $options['join'] . " ";
			}

			$sql .=" " . $where;


			if (isset($options['groupby'])) {
				$sql .= " " . $options['groupby'] . " ";
			} elseif (isset($this->sqlOptions['groupby'])) {
				$sql .= "GROUP BY " . $this->sqlOptions['groupby'];
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

		public function findAll($column) {
			$sql = "SELECT *";
			$where = " WHERE " . $column . " IS NOT NULL ";
			$sql = $this->generateSearchSql($sql, $where);
			#echo $sql;
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
			$where = " WHERE ";
			if (strtolower(get_class($this)) == "User") {
				$where .= "user_id";
			} else {
				$where .= "fk_" . get_class($this) . "_user";
			}
			$where .= "='" . $userId . "' ";
			
			#$where = "WHERE fk_" . get_class($this) . "_user='" . $userId . "' ";

			$sql = $this->generateSearchSql($sql, $where);

			$results = $this->runSql($sql);

			return $this->createResultsArray($results);
		}

		public function addToJoinTable($propertiesList, $table) {
			if (isset($this->id)) {
				$sql = "INSERT INTO " . $table . " (fk_" . $table . "_" . get_class($this) . ", ";

				foreach (array_keys($propertiesList[0]) as $colName) {
					$sql .= $this->toCamelCase($colName) . ", ";
				}
				$sql = substr($sql, 0, -2) . ") VALUES ";
				foreach ($propertiesList as $key => $value) {
					$sql .= "('" . $this->id . "', ";
					foreach ($value as $colValue) {
						$sql .= "'" . $colValue . "', ";
					}
					$sql = substr($sql, 0, -2) . "), ";
				}
				$sql = substr($sql, 0, -2);

				#echo $sql;
				#die();

				$this->runSql($sql);
			}
		}

		private function toCamelCase($str) {
			return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $str)), '_');
		}

		public static function buildAndRunPreparedStatement($sql, $datatypes, $params) {
			$conn = Db::connect();
			$stmt = $conn->prepare($sql);

			$stmtParams = array();
			$stmtParams[] = & $datatypes;

			foreach ($params as $param) {
				$stmtParams[] = & $param;
			}

			call_user_func_array(array($stmt, 'bind_param'), $stmtParams);
			$stmt->execute();
			$results = $stmt->get_result();

			$returnArray = [];
			while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
				array_push($returnArray, $row);
			}

			return $returnArray;
		}

		public static function runPreparedStatement($stmt) {
			$conn = Db::connect();
			$stmt->execute();
			var_dump($stmt);
			die();
			$results = $stmt->get_result();

			$returnArray = [];
			while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
				array_push($returnArray, $row);
			}

			return $returnArray;
		}

	}
?>
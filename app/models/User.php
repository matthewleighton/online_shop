<?php
	class User extends Model {
		
		public $properties = array(
			'first_name' => "",
			'last_name' => "",
			'email' => ""
		);

		public $errors = array();

		public function __construct() {
			$this->validates('first_name', 'presence');
			$this->validates('first_name', 'length', ['maximum' => 30]);
			$this->validates('first_name', 'no_match', ['/[0-9]/', ' must only contain letters.']);

			$this->validates('last_name', 'presence', 'presence');
			$this->validates('last_name', 'length', ['maximum' => 30]);
			$this->validates('last_name', 'no_match', ['/[0-9]/', ' must contain only letters.']);

			$this->validates('email', 'presence');
			$this->validates('email', 'match', ['/\A[\w+\-.]+@[a-z\d\-.]+\.[a-z]+\z/i', ' must be valid.']);
			$this->validates('email', 'unique');

			$this->validates('password', 'presence');
			$this->validates('password', 'length', ['minimum' => 6]);
		}

		public function assignProperties($list) {
			foreach(array_keys($list) as $attr) {
				
				if(array_key_exists($attr, $this->properties)) {
					$this->properties[$attr] = $list[$attr];
				}
			}
		}

		public function createUser() {
			if($this->runValidations()) {
				$sql = $this->generateSql('INSERT INTO', 'users', $this->properties);
				$this->runSql($sql);
				return true;
			} else {
				return false;
			}
		}

		public function findBy($columns, $val) {
			$conn = Db::connect();
			$sql = "SELECT  user_id, first_name, last_name, email, admin FROM users WHERE ";
			if(is_array($columns)) {
				foreach ($columns as $col) {
					$sql .= $col . "='" . $val[array_search($col, $col_list)] . "' AND  ";
				}
				$sql = rtrim($sql, " AND ");
			} else {
				$sql .= $columns . "='" . $val . "'";
			}
			
			$results = $conn->query($sql);
			return $results->fetch_assoc();
		}
	}
?>
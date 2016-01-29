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
			$this->validates('first_name', 'length', ['minimum' => 2]);
			$this->validates('first_name', 'length', ['maximum' => 9]);
			$this->validates('first_name', 'no_match', ['/[0-9]/', ' must only contain letters.']);

			$this->validates('last_name', 'presence', 'presence');
			$this->validates('last_name', 'length', ['minimum' => 4]);
			$this->validates('last_name', 'length', ['maximum' => 10]);
			$this->validates('last_name', 'no_match', ['/[0-9]/', ' must contain only letters.']);

			$this->validates('email', 'presence');
			$this->validates('email', 'match', ['/\A[\w+\-.]+@[a-z\d\-.]+\.[a-z]+\z/i', ' must be valid.']);

			$this->validates('password', 'presence');
			$this->validates('password', 'length', ['minimum' => 4]);

			//print_r($this->validationsList);
			//echo '<br>';
		}

		public function assignProperties() {
			foreach(array_keys($_POST) as $postKey) {
				
				if(array_key_exists($postKey, $this->properties)) {
					$this->properties[$postKey] = $_POST[$postKey];
				} else if ($postKey == 'password' && array_key_exists('password_confirmation', $_POST)) {
					
				}

				//print_r($this->properties);

				if(isset($this->{$postKey})) {
					$this->{$postKey} = $_POST[$postKey];
				}
			}
		}

		public function createUser() {
			
			//echo '<br><br>';
			//print_r($this->validationsList);

			if($this->runValidations()) {
				//$sql = "INSERT INTO Users (first_name, last_name, email) VALUES ('Test', 'One', 'email')";
				$sql = $this->generateSql('INSERT INTO', 'users', $this->properties);
				//echo '<br>SQL is ' . $sql . '<br>';
				$this->saveToDatabase($sql);
				return true;
			} else {
				return false;
			}
		}
	}
?>
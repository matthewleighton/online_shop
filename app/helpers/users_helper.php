<?php
	class Users_helper {
		
		public function createInput($type, $name, $placeholder, $classes = []) {
			echo "<input type='" . $type .
					  "' name='" . $name . 
					  "' placeholder='" . $placeholder . "'"; 
		
			if(array_key_exists($name, $_POST)) {
			  	echo "value='" . $_POST[$name]. "'";
			}

			echo "class='" . var_dump($this->data) . "' >";
		}
	}
?>
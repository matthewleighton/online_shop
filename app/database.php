<?php
	class Db {
		public function connect() {
			$connection = new mysqli('localhost', 'root', '', 'online_shop');
			if($connection->connect_error) {
				die("Connection failed " . $conn->connect_error);
			}
			return $connection;
		}
	}
		
?>
<?php
	require_once('TestInit.php');
	require_once('../app/models/User.php');
	class UserTest extends PHPUnit_Framework_TestCase {


		public $test;

		public function setUp() {
			$this->test = new User;
		}

		public function testcreateUser() {
			
		}

	}
?>
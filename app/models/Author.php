<?php
	class Author extends Model {
		protected $properties = array(
			'author_name' => ''
		);

		public function __construct() {
			$this->validates('author_name', 'presence');
			$this->validates('author_name', 'length', ['maximum' => 30]);
		}
	}
?>
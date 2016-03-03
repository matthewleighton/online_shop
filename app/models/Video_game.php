<?php
	class Video_game extends Product {
		protected $catagoryColumns = ['console', 'age_rating'];

		public function __construct() {
			$this->properties['console'] = '';
			$this->validates('page_count', 'presence');

			$this->properties['book_type'] = '';
			$this->validates('book_type', 'presence');
		}
	}
?>
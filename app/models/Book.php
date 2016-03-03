<?php
	class Book extends Product {

		protected $catagoryColumns = ['page_count', 'book_type', 'publisher', 'language'];

		public function __construct() {
			$this->properties['page_count'] = '';
			$this->validates('page_count', 'presence');

			$this->properties['book_type'] = '';
			$this->validates('book_type', 'presence');

			$this->properties['publisher'] = '';
			$this->validates('publisher', 'presence');

			$this->properties['language'] = '';
			$this->validates('language', 'presence');

			$this->properties['creators']['author'] = [];
			
			parent::__construct();

			$this->sqlOptions['join']['book'] = ['fk_book_product', 'product.product_id'];
			array_push($this->sqlOptions['concat'],
				["CASE WHEN person_role = 'author' THEN person.person_name ELSE NULL END", 'authors']);
		}
	}
?>
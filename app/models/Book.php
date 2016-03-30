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
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN person_role = 'author' THEN person.person_name ELSE NULL END", 'authors'];
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN fk_languages_base_product_languages = language_id " .
											 "THEN language_name END ORDER BY languages_base_product_id", 'languages'];

			$this->sqlOptions['join']['physical_book'] = ['fk_physical_book_product_version', 'product_version_id'];
			$this->sqlOptions['join']['ebook'] = ['fk_ebook_product_version', 'product_version_id'];
			$this->sqlOptions['join']['languages_base_product'] = ['fk_languages_base_product_base_product', 'base_product_id'];
			$this->sqlOptions['join']['languages'] = ['language_id', 'fk_languages_base_product_languages'];
		}
	}
?>
<?php
	class Film extends Product {
		
		/*protected $sqlOptions = ['join' => ['film' => ['film.fk_film_product', 'product.product_id'],
											'director' => ['director.fk_director_product', 'product.product_id'],
											'person' => ['person.person_id', 'director.fk_director_person']],
								 'concat' => [['person.person_name', 'director']],
								 'groupby' => 'product.product_id'];*/

		protected $catagoryColumns = ['running_time', 'age_rating'];

		public function __construct() {
			$this->properties['running_time'] = '';
			$this->validates('running_time', 'presence');

			$this->properties['age_rating'] = '';
			$this->validates('age_rating', 'presence');

			$this->properties['creators']['director'] = [];
			
			parent::__construct();

			$this->sqlOptions['join']['film'] = ['fk_film_base_product', 'base_product_id'];
			$this->sqlOptions['join']['languages_base_product'] = ['fk_languages_base_product_base_product', 'base_product_id'];
			$this->sqlOptions['join']['subtitles'] = ['fk_subtitles_base_product', 'base_product_id'];
			$this->sqlOptions['join']['languages'] = ['language_id', 'fk_languages_base_product_languages'];

			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN person_role = 'director' " .
											 "THEN person.person_name ELSE NULL END", 'directors'];
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN person_role = 'actor' " .
											 "THEN person.person_name ELSE NULL END", 'actors'];
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN fk_languages_base_product_languages = language_id " .
											 "THEN language_name END ORDER BY languages_base_product_id", 'languages'];
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN fk_subtitles_languages = language_id " .
											 "THEN language_name END ORDER BY languages_base_product_id", 'subtitles'];
		}
	}
?>
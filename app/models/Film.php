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

			$this->sqlOptions['join']['film'] = ['fk_film_product', 'product.product_id'];
			array_push($this->sqlOptions['concat'],
				["CASE WHEN person_role = 'director' THEN person.person_name ELSE NULL END", 'director']);
		}
	}
?>
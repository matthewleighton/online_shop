<?php
	class Film extends Product {
		
		protected $sqlOptions = ['join' => ['film' => ['film.product_id', 'product.product_id']],
								 'groupby' => 'product.product_id'];

		public function __construct() {
			$this->properties['running_time'] = '';
			$this->validates('running_time', 'presence');

			$this->properties['age_rating'] = '';
			$this->validates('age_rating', 'presence');
			
			parent::__construct();
		}
	}
?>
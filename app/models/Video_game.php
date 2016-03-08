<?php
	class Video_game extends Product {
		protected $catagoryColumns = ['console', 'age_rating'];

		public function __construct() {
			$this->properties['console'] = '';
			$this->validates('console', 'presence');

			$this->properties['book_type'] = '';
			$this->validates('book_type', 'presence');

			parent::__construct();

			$this->sqlOptions['join']['video_game'] = ['fk_video_game_product', 'product.product_id'];
		}
	}
?>
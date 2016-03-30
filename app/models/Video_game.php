<?php
	class Video_game extends Product {
		protected $catagoryColumns = ['console', 'age_rating'];

		public function __construct() {
			$this->properties['console'] = '';
			$this->validates('console', 'presence');

			$this->properties['book_type'] = '';
			$this->validates('book_type', 'presence');

			parent::__construct();
			$this->sqlOptions['join']['video_game'] = ['fk_video_game_product_version', 'product_version_id'];
			$this->sqlOptions['join']['pc_game'] = ['fk_pc_game_video_game', 'video_game_id'];
			$this->sqlOptions['join']['subtitles'] = ['fk_subtitles_base_product', 'base_product_id'];
			$this->sqlOptions['join']['languages_base_product'] = ['fk_languages_base_product_base_product',
																   'base_product_id'];
			$this->sqlOptions['join']['languages'] = ['language_id',
													  'fk_subtitles_languages OR fk_languages_base_product_languages'];
		}
	}
?>
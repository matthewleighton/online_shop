<?php
	class Music extends Product {

		public function __construct() {
			parent::__construct();
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN person_role = 'musician' THEN person_name END ORDER BY madeby_id",
											 'musicians'];
			$this->sqlOptions['concat'][] = ['song_title ORDER BY song_number', 'songs'];
			$this->sqlOptions['concat'][] = ['running_time ORDER BY song_number', 'running_times'];

			$this->sqlOptions['join']['song_base_product'] = ['fk_song_base_product_base_product', 'base_product_id'];
			$this->sqlOptions['join']['song'] = ['song_id', 'fk_song_base_product_song'];
		}


	}
?>
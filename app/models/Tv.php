<?php
	class Tv extends Product {

		public function __construct() {
			parent::__construct();
			$this->sqlOptions['concat'][] = ["DISTINCT CASE WHEN person_role = 'actor' " .
											 "THEN person_name END ORDER BY madeby_id", 'actors'];
			$this->sqlOptions['concat'][] = ['DISTINCT CASE WHEN fk_subtitles_languages = language_id ' .
											 'THEN language_name END ORDER BY subtitles_id', 'subtitles'];
			$this->sqlOptions['concat'][] = ['DISTINCT CASE WHEN fk_languages_base_product_languages = language_id ' .
											 'THEN language_name END ORDER BY languages_base_product_id', 'languages'];
			$this->sqlOptions['concat'][] = ['DISTINCT tv_episode_name ORDER BY tv_episode_number', 'episodes'];
			$this->sqlOptions['concat'][] = ['DISTINCT tv_episode_description ORDER BY tv_episode_number',
											 'episode_descriptions'];
			# TODO  - episode_air_date concat is only DINSTINCT to avoid an issue were it was being joined to multiple times.
			# Find a way to fix the joining issue. Else, the inclusion of DISTINCT will cause issues with episodes of the
			# same air date
			$this->sqlOptions['concat'][] = ['DISTINCT tv_episode_air_date ORDER BY tv_episode_number', 'episode_air_dates'];
			$this->sqlOptions['concat'][] = ['tv_episode_running_time ORDER BY tv_episode_number',
											 'episode_running_times'];


			$this->sqlOptions['join']['tv_season'] = ['fk_tv_season_base_product', 'base_product_id'];
			$this->sqlOptions['join']['tv_series'] = ['fk_tv_season_tv_series', 'tv_series_id'];
			$this->sqlOptions['join']['tv_episode'] = ['fk_tv_episode_tv_season', 'tv_season_id'];
			$this->sqlOptions['join']['subtitles'] = ['fk_subtitles_base_product', 'base_product_id'];
			$this->sqlOptions['join']['languages_base_product'] = ['fk_languages_base_product_base_product',
																   'base_product_id'];
			$this->sqlOptions['join']['languages'] = ['language_id',
													  'fk_subtitles_languages OR fk_languages_base_product_languages'];
		}

	}
?>
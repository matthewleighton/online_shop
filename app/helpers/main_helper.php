<?php
	class Main_helper {
		public function link_to($location, $text, $ident = []) {
			$link = "<a ";

			if(array_key_exists('id', $ident)) {
				$link .= "id='" . $ident['id'] . "' ";
			} elseif (array_key_exists('class', $ident)) {
				$link .= "class='" . $ident['class'] . "' ";
			}

			echo $link .= "href='/online_shop/public/" . $location . "'>" . $text . "</a>";
		}
	}
?>
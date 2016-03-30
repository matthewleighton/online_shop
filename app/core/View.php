<?php
	class View {
		public $data = array();
		public $partials = array('header' => true, 'footer' => true);

		public function __construct($page, $partials = []) {
			global $rootPath;

			$this->data['page'] = $page;
			foreach ($partials as $key => $value) {
					if(array_key_exists($key, $this->partials)) {
						$this->partials[$key] = $value;
					}
				}			
		}

		public function rootPath() {
			return $GLOBALS['rootPath'];
		}

		public function set($key, $value) {
			$this->data[$key] = $value;
			print_r($this->data);
		}

		public function set_title($title) {
			$this->data['title'] = $title;
		}

		public function pass_data($new_key, $new_data) {
			//array_push($this->data, $new_data);
			$this->data[$new_key] = $new_data;
		}

		public function load_page() {
			if(array_key_exists('page', $this->data)) {
				require_once('../app/views/layouts/head.php');
				require_once('../app/views/' . $this->data['page'] . '.php');
				require_once('../app/views/partials/_page_bottom.php');
			}
		}

		public function createInput($type, $name, $placeholder, $object, $options = []) {

			$contains_errors = false;
			if($object != '' && array_key_exists($name, $this->data[$object]->errorsList)) {
					$contains_errors = true;
			}

			$input = "<input type='" . $type .
					  "' name='" . $name . 
					  "' placeholder='" . $placeholder . "' ";

			if(array_key_exists($name, $_POST)) {
			  	if (is_array($_POST[$name])) {
			  		$input .= "value='" . implode(", ", $_POST[$name]) . "'";
			  	} else {
			  		$input .= "value='" . $_POST[$name]. "' ";	
			  	}
			} else if(isset($_SESSION[$object])) {
				$input .= "value='" . $_SESSION[$object]->properties[$name] ."'";
			}

			if(array_key_exists('class', $options) || $contains_errors) {
				$input .= "class='";
				if($contains_errors) {
					$input .= "field-with-errors ";
				}
				if(array_key_exists('class', $options)) {
					$input .= $options['class'];
				}
				$input .= "'";
			}

			if (array_key_exists('id', $options)) {
				$input .= " id='" . $options['id'] . "' ";
			}

			if (array_key_exists('onfocus', $options)) {
				$input .= " onfocus=\"" . $options['onfocus'] . "\" ";
			}

			$input .= ">";

			echo $input;
		}

		// Creates a form select element.
		// By default an option's text will be a clean version of its value.
		// To specify different text, submit the choice as an array with [0] = value and [1] = text.
		public function createSelect($name, $choices, $object, $ident = []) {
			
			if($object != '' && array_key_exists($name, $this->data[$object]->errorsList)) {
				if(array_key_exists('class', $ident)) {
					$ident['class'] .= " " . "field-with-errors";
				} else {
					$ident['class'] = "field-with-errors";
				}
			}

			$select = "<select name='" . $name . "'";
			$select .= $this->listIdents($ident);
			$select .= ">";

			foreach ($choices as $value) {
				$select .= "<option value='";
				if(is_array($value)) {
					$select .= $value[0];

					$value = $value[1];
				} else {
					$select .= $value;
				}

				$select .= "'>" . ucwords(str_replace('_', ' ', $value)) . "</option>";
			}
			$select .= "</select>";

			echo $select;
		}

		public function displayError($object, $attr, $ident = []) {
			if(isset($object->errorsList[$attr])) {
				echo "<ul>";
					foreach($object->errorsList[$attr] as $message) {
						echo "<li class='field-error-message'>" . $message . "</li>";
					}
				echo "</ul>";
			}
		}

		protected function link_to($location, $text, $ident = []) {
			$link = "<a ";
			$link .= $this->listIdents($ident);
			echo $link .= "href='" . $GLOBALS['rootPath'] . $location . "'>" . $text . "</a>";
		}

		protected function linkToProduct($productId, $text, $ident = []) {
			$link = "<a ";
			$link .= $this->listIdents($ident);
			echo $link .= "href='" . $GLOBALS['rootPath'] . "products/item/" . $productId . "'>" . $text ."</a>";
		}

		# TODO change all uses of image_tag to echo its return value
		protected function image_tag($image, $options = []) {
			$tag = "<img src='" . $GLOBALS['rootPath'] . "assets/img/" . $image . "' ";

			$tag .= $this->listIdents($options);

			if(array_key_exists('height', $options)) {
				$tag .= "height='" . $options['height'] . "' ";
			}
			if(array_key_exists('width', $options)) {
				$tag .= "width='" . $options['width'] . "' ";
			}
			
			return $tag .= ">";
		}

		protected function user_info($attr) {
			if($attr == 'password') {
				return nill;
			}
		}

		private function listIdents($ident) {
			$return = "";
			if(array_key_exists('id', $ident)) {
				$return .= "id='" . $ident['id'] . "' ";
			}
			if (array_key_exists('class', $ident)) {
				$return .= "class='" . $ident['class'] . "' ";
			}

			return $return;
		}

		protected function formatPrice($price) {
			return number_format($price, 2, '.', '');
		}

		protected function totalPrice($cart) {
			$total = 0;
			foreach ($cart as $item) {
				$n = $item['product_price'] * intval($item['cart_quantity']);
				$total += $n;
			}

			return number_format($total, 2);
		}

		protected function removeFromCart($productVersionId) {
			echo "<form action='" . $GLOBALS['rootPath'] . "carts/removeitem' method='post'>";
				echo "<input type='hidden' name='productVersionId' value='" . $productVersionId . "'>";
				echo "<input type='submit' value='Delete' class='cart-delete-btn'>";
			echo "</form>";
		}

		protected function productImage($baseId, $versionId, $height, $imageNumber = 0, $link = false, $ident = []) {
			if ($link) {
				$imageTag = "<a href='" . $GLOBALS['rootPath'] . "products/item/" . $versionId . "'>";
			} else {
				$imageTag = '';
			}

			$options = ['height' => $height];
			foreach ($ident as $key => $value) {
				$options[$key] = $value;
			}
		
			if (file_exists('../public/assets/img/products/p' . $baseId . '-' . $versionId . '-' . $imageNumber . '.jpg')) {
				$imageTag .= $this->image_tag('products/p' . $baseId . '-' . $versionId . '-' . $imageNumber . '.jpg', $options);
			} else if ($imageNumber == 0 && file_exists('../public/assets/img/products/p' . $baseId . '-0-0.jpg')) {
				$imageTag .= $this->image_tag('products/p' . $baseId . '-0-0.jpg', $options);
			} else {
				$imageTag .= $this->image_tag('placeholder-image.png', $options);
				if ($link) {
					$imageTag .= '</a>';
				}
				echo $imageTag;
				return false;
			}

			if ($link) {
				$imageTag .= '</a>';
			}

			return $imageTag;			
		}

		protected function formatLabel($label) {
			$label = str_replace('_', ' ', $label);
			return ucfirst($label);
		}

		// Displays a payment card number safely, with only the last 4 digits visible.
		protected function safeCardNum($num) {
			$show = substr($num, -4, 4);
			$hide = str_repeat("*", strlen(substr($num, 0, -4)));
			return $hide . $show;
		}

		protected function printAddress($address) {
			echo "<p>" . $address['full_name'] . "</p>";
			echo "<p>" . $address['address_line_1'] . "</p>";
			echo "<p>" . $address['city'] . ", " . $address['county'] . " " . $address['postcode'] . "</p>";
			echo "<p>" . $address['country'] . "</p>";
			echo "<p>Phone: " . $address['phone_number'] . "</p>";
		}

		protected function formatDate($date, $format = 'l d M. Y') {
			return date($format, strtotime($date, time()));
		}

		public function displayLogo($height = '70') {
			echo "<div class='box-page-logo'><a href='" . $this->rootPath() . "'>" .
				 "<img src='" . $GLOBALS['rootPath'] . "assets/img/logo_placeholder.png' alt='logo'" .
				 " height='" . $height . "'/></a></div>";
		}

		protected function printRunningTime($runningTime) {
			$hours = (int) ($runningTime / 60);
			$minutes = $runningTime % 60;
			$output = '';

			if($hours > 0) {
				$output .= $hours . " hour";
				$output .= ($hours == 1 ? " " : "s ");
			}

			$output .= $minutes . " minute";
			$output .= ($minutes != 1 ? "s" : '');

			return $output;
		}

		# Produces a string list of the elements of an array.
		public function arrayToString($array) {
			$arrayLength = count($array);

			if ($arrayLength == 1) {
				return $array[0];
			} else {
				$returnString = '';
				
				for ($i=0; $i < $arrayLength; $i++)	{
					if ($i > 0) {
						if ($i < ($arrayLength - 1)) {
							$returnString .= ', ';
						} else {
							$returnString .= ' and ';
						}
					}

					$returnString .= $array[$i];
				}

				return $returnString;
			}	
		}

		# Display an age rating logo
		public function displayAgeRating($ageRating, $height) {
			if (file_exists('../public/assets/img/age_ratings/' . $ageRating . '.png')) {
				$imageTag = "<img src='" . $GLOBALS['rootPath'] . "assets/img/age_ratings/" . $ageRating . ".png";
				$imageTag .= "' height='" . $height . "'>";
				return $imageTag;
			} else {
				return $ageRating;
			}
		}

		public function displayProductCreator($product) {
			if (isset($product['authors'])) {
				$creator = 'By ' . $this->arrayToString($product['authors']);
			} elseif (isset($product['directors'])) {
				$creator = 'Directed by ' . $this->arrayToString($product['directors']);
			} elseif (isset($product['musicians'])) {
				$creator = 'By ' . $this->arrayToString($product['musicians']);
			} else {
				$creator = '';
			}

			return $creator;
		}
	}
	
?>
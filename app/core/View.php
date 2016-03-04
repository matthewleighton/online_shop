<?php
	class View {
		public $data = array();
		public $partials = array('header' => true, 'footer' => true);

		public function __construct($page, $partials = []) {
			$this->data['page'] = $page;
			foreach ($partials as $key => $value) {
					if(array_key_exists($key, $this->partials)) {
						$this->partials[$key] = $value;
					}
				}			
		}

		public function rootPath() {
			return "/online_shop/public/";
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
			echo $link .= "href='/online_shop/public/" . $location . "'>" . $text . "</a>";
		}

		protected function linkToProduct($productId, $text, $ident = []) {
			$link = "<a ";
			$link .= $this->listIdents($ident);
			echo $link .= "href='/online_shop/public/products/item/" . $productId . "'>" . $text ."</a>";
		}

		protected function image_tag($image, $options = []) {
			$tag = "<img src='/online_shop/public/assets/img/" . $image . "' ";

			$tag .= $this->listIdents($options);

			if(array_key_exists('height', $options)) {
				$tag .= "height='" . $options['height'] . "' ";
			}
			if(array_key_exists('width', $options)) {
				$tag .= "width='" . $options['width'] . "' ";
			}

			echo $tag .= ">";


		}

		protected function user_info($attr) {
			if($attr == 'password') {
				return nill;
			}
		}

		private function listIdents($ident) {
			if(array_key_exists('id', $ident)) {
				return "id='" . $ident['id'] . "' ";
			} elseif (array_key_exists('class', $ident)) {
				return "class='" . $ident['class'] . "' ";
			}
		}

		protected function formatPrice($price) {
			return number_format($price, 2, '.', '');
		}

		protected function totalPrice($cart) {
			$total = 0;
			foreach ($cart as $item) {
				$n = $item['price'] * intval($item['cart_quantity']);
				$total += $n;
			}

			return number_format($total, 2);
		}

		protected function removeFromCart($product_id, $cart) {
			echo "<form action='/online_shop/public/carts/removeitem' method='post'>";
				echo "<input type='hidden' name='product_id' value='" . $product_id . "'>";
				echo "<input type='submit' value='Delete' class='cart-delete-btn'>";
			echo "</form>";
		}

		protected function productImage($product_id, $height, $ident = []) {
			echo "<a href='" . $this->rootPath() . "products/item/" . $product_id . "'>";
				if(file_exists('../public/assets/img/products/product' . $product_id . '.jpg')) {
				 	$this->image_tag('products/product' . $product_id . '.jpg', ['height' => $height, $ident]);
				 } else {
				 	$this->image_tag('placeholder-image.png', ['height' => $height]);
				 }
			echo "</a>";
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

		protected function productCreator($product) {
			if(isset($product['authors'])) {
				return $product['authors'];
			}
			//TODO - Add other ways of accessing creators as I add more product types
		}

		protected function formatDate($date, $format = 'l d M. Y') {
			return date($format, strtotime($date, time()));
		}

		public function boxPageLogo($height = '70') {
			echo "<div class='box-page-logo'><a href='" . $this->rootPath() . "'>" .
				 "<img src='" . $this->rootPath() . "assets/img/logo_placeholder.png' alt='logo'" .
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
	}
	
?>
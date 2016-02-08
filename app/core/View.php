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

		public function createInput($type, $name, $placeholder, $object, $ident = []) {

			$contains_errors = false;
			if($object != '' && array_key_exists($name, $this->data[$object]->errorsList)) {
					$contains_errors = true;
			}

			$input = "<input type='" . $type .
					  "' name='" . $name . 
					  "' placeholder='" . $placeholder . "' ";

			if(array_key_exists($name, $_POST)) {
			  	$input .= "value='" . $_POST[$name]. "' ";
			}

			if(array_key_exists('class', $ident) || $contains_errors) {
				$input .= "class='";
				if($contains_errors) {
					$input .= "field-with-errors ";
				}
				if(array_key_exists('class', $ident)) {
					$input .= $ident['class'];
				}
				$input .= "'";
			}

			$input .= ">";

			echo $input;
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
			return substr($price, 0, strlen($price) - 2);
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
			if(file_exists('../public/assets/img/products/product' . $product_id . '.jpg')) {
			 	$this->image_tag('products/product' . $product_id . '.jpg', ['height' => $height, $ident]);
			 } else {
			 	$this->image_tag('placeholder-image.png', ['height' => $height]);
			 }
		}
	}
	
?>
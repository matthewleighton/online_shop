<?php
	class Wish_lists extends Controller {
		
		public function __construct() {
			session_start();
			if (!isset($_SESSION['user_id'])) {
				if (isset($_POST['productVersionId'])) {
					$url = 'products/item/' . $_POST['productVersionId'];
				} else {
					$url = ' ';
				}
				$this->mustBeLoggedIn($url);
				break;
			}

			
			require_once('../app/models/Wish_list.php');
		}

		public function index() {
			session_start();
			$listOfWishLists = Wish_list::findByUserId($_SESSION['user_id']);

			$view = new View('wish_lists/show');
			$view->set_title('Wish Lists');

			$view->pass_data('listOfWishLists', $listOfWishLists);

			$view->load_page();
		}

		public function show($wishListId) {
			if (!Wish_list::listBelongsToUser($wishListId)) {
				$this->redirect_to('wish_lists');
				break;
			}

			#TODO add a method of properly sorting wish lists by the date each product was added.

			$wishList = Wish_list::findById($wishListId);
			$listOfWishLists = Wish_list::findByUserId($_SESSION['user_id']);
			
			if (isset($wishList['listInfo'])) {
				$listInfo = $wishList['listInfo'];
				unset($wishList['listInfo']);
			} else {
				$listInfo = ['wish_list_id' => $wishList[0]['wish_list_id'],
							 'fk_wish_list_user' => $wishList[0]['fk_wish_list_user'],
							 'wish_list_name' => $wishList[0]['wish_list_name']];
			}

			$view = new View('wish_lists/show');
			$view->set_title('Wish List - ' . $listInfo['wish_list_name']);
			
			$view->pass_data('listInfo', $listInfo);
			$view->pass_data('wishList', $wishList);
			$view->pass_data('listOfWishLists', $listOfWishLists);

			$view->load_page();
		}

		public function addItem() {
			if ($_POST['newWishList'] != '') {
				$_POST['wishListId'] = Wish_list::createNew($_POST['newWishList']);
			}

			#TODO - add a check to make sure the product is not already on the list.
			# If it is on the list already, just ignore it and continue on to the list.

			Wish_list::addProduct($_POST['wishListId'], $_POST['productVersionId']);

			$this->redirect_to('wish_lists/show/' . $_POST['wishListId']);
		}

		public function destroy($wishListId) {
			if (Wish_list::listBelongsToUser($wishListId)) {
				Wish_list::destroy($wishListId);
			}

			$this->redirect_to('wish_lists');
		}

		public function removeItem() {
			Wish_list::removeItem($_POST['productVersionId'], $_POST['wishListId']);
			$this->redirect_to('wish_lists/show/' . $_POST['wishListId']);
		}
	}
?>
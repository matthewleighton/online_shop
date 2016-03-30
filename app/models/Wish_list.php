<?php
	class Wish_list extends Model {
		
		public function findByUserId($userId) {
			$sql = 'SELECT wish_list_id, wish_list_name ';
			$sql .= 'FROM wish_list WHERE fk_wish_list_user = ?';
			$datatypes = 'i';
			$params = [$userId];

			$results = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);
			return Model::createResultsArray($results);
		}

		public function createNew($listName) {
			session_start();
			$sql = 'INSERT INTO wish_list (fk_wish_list_user, wish_list_name) VALUES (?, ?)';
			$datatypes = 'is';
			$params = [$_SESSION['user_id'], $listName];

			return Model::buildAndRunPreparedStatement($sql, $datatypes, $params, true);
		}

		public function addProduct($wishListId, $productVersionId) {
			$sql = 'INSERT INTO wish_list_product_version (fk_wish_list_product_version_wish_list,' .
				   ' fk_wish_list_product_version_product_version, added_to_list_at) VALUES (?, ?, ?)';
			$datatypes = 'iis';
			$params = [$wishListId, $productVersionId, date('Y-m-d H:i:s')];

			Model::buildAndRunPreparedStatement($sql, $datatypes, $params, true);
		}

		public function findById($wishListId) {
			$where = [];
			$where['sql'] = 'WHERE fk_wish_list_product_version_wish_list = ?';
			$where['datatypes'] = 'i';
			$where['values'] = [$wishListId];

			$join = [];
			$join['product_version'] = ['fk_product_version_base_product', 'base_product_id'];
			$join['wish_list_product_version'] = ['fk_wish_list_product_version_product_version', 'product_version_id'];
			$join['wish_list'] = ['fk_wish_list_product_version_wish_list', 'wish_list_id'];

			require_once('../app/models/Product.php');
			$wishList = Product::findProducts($where, $join);

			if (count($wishList) == 0) {
				$sql = 'SELECT * FROM wish_list WHERE wish_list_id = ?';
				$datatypes = 'i';
				$params = [$wishListId];

				$wishListInfo = Model::buildAndRunPreparedStatement($sql, $datatypes, $params);
				$wishListInfo = Model::createResultsArray($wishListInfo);

				$wishList['listInfo'] = $wishListInfo[0];
			}

			return $wishList;
		}

		public function removeItem($productVersionId, $wishListId) {
			
			echo "product_version_id=" . $productVersionId . '<br>';
			echo "wish_list_id=" . $wishListId . '<br>';

			$sql = 'DELETE FROM wish_list_product_version WHERE fk_wish_list_product_version_wish_list = ? ' . 
				   'AND fk_wish_list_product_version_product_version = ?';
			#echo $sql;die;
			$conn = Db::connect();
			$stmt = $conn->prepare($sql);

			$stmt->bind_param('ii', $wishListId, $productVersionId);
			$stmt->execute();
			$conn->close();
		}

		public function listBelongsToUser($wishListId) {
			$sql = 'SELECT fk_wish_list_user FROM wish_list WHERE wish_list_id = ?';
			$conn = Db::connect();
			$stmt = $conn->prepare($sql);

			$stmt->bind_param('i', $wishListId);
			$stmt->execute();

			$stmt->bind_result($userId);
			$stmt->fetch();

			$conn->close();

			session_start();
			if ($userId == $_SESSION['user_id']) {
				return true;
			} else {
				return false;
			}
		}

		public function destroy($wishListId) {
			$sql = 'DELETE FROM wish_list WHERE wish_list_id = ?';
			$conn = Db::connect();
			$stmt = $conn->prepare($sql);

			$stmt->bind_param('i', $wishListId);
			$stmt->execute();
			$stmt->close();
		}

	}
?>
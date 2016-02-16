<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
				if(array_key_exists('title', $this->data)) {
					echo $this->data['title'];
				} else
				echo 'Not Amazon';
			?>
		</title>
		<link rel='stylesheet' href='/online_shop/public/css/main.css'/>
		<script src="/online_shop/public/js/jquery-1.12.0.js"></script>
		<script src="/online_shop/public/js/main.js"></script>
	</head>
	<body>
			<!--TODO change logged out header to only display while logged out-->
			<?php
				if($this->partials['header'] == true) {
					include_once('../app/views/partials/_account_header.php');
				}
			?>
			<div id="wrapper">
				<?php
					if($this->partials['header'] == true) {
						//include_once('../app/views/partials/_search_header.php');
						include_once('../app/views/partials/_new_header.php');
					}
				?>
			
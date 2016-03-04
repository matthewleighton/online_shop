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
		<link rel="shortcut icon" href="/online_shop/public/assets/img/icons/icon.ico">
	</head>
	<body>
			<?php
				if($this->partials['header'] == true) {
					include_once('../app/views/partials/_account_header.php');
				}
			?>
			<div id="wrapper">
				<?php
					if($this->partials['header'] == true) {
						include_once('../app/views/partials/_search_header.php');
					}
				?>
			
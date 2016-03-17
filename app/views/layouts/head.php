<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>
			<?php
				$baseTitle = 'Matthew Leighton';
				if (array_key_exists('title', $this->data)) {
					$baseTitle .= ' | ' . $this->data['title'];
				}
				echo $baseTitle;
			?>
		</title>
		<link rel='stylesheet' href='<?php echo $GLOBALS['rootPath']; ?>css/main.css'/>
		<script src='<?php echo $GLOBALS['rootPath']; ?>js/jquery-1.12.0.js'></script>
		<script src='<?php echo $GLOBALS['rootPath']; ?>js/jquery-ui.min.js'></script>
		<script src='<?php echo $GLOBALS['rootPath']; ?>js/main.js'></script>
		<link rel='shortcut icon' href='<?php echo $GLOBALS['rootPath']; ?>assets/img/icons/icon.ico'>
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
			
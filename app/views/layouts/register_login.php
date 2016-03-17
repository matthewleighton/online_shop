<div id="login-register-page">
	<?php $this->displayLogo(); ?>
	<?php if($_GET['url'] == 'users/newuser') {
		include_once('../app/views/users/_account_creation.php');
	} else if($_GET['url'] == 'sessions/login') {
		include_once('../app/views/sessions/_login.php');
	} ?>
</div>
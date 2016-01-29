<div id="login-register-page">
	<a href="/online_shop/public"><img src='/online_shop/public/assets/img/logo_placeholder.png' alt='logo' height='70'/></a>
	<?php if($_GET['url'] == 'users/newuser') {
		include_once('../app/views/users/_account_creation.php');
	} else if($_GET['url'] == 'sessions/login') {
		include_once('../app/views/sessions/_login.php');
	} ?>
</div>
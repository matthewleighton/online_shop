<div id="box">
	<h2>Login</h2>
	<form action="login" method="post" class="input-page">
		<?php
			$this->createInput('text', 'email', 'Email', '');
			$this->createInput('password', 'password', 'Password', '');
		?>
		<?php
			if(array_key_exists('redirect', $_GET)) {
				echo "<input type='hidden' name='redirect' value='" . $_GET['redirect'] . "'>";
			}
		?>
		<?php if($this->data['loginError'] == true) { ?>
			<div>
				Invalid email/password combination
			</div>
		<?php } ?>
		<input type='submit' value='Log in'>
	</form>
	
<?php
	$link = 'users/newuser';
	if(array_key_exists('redirect', $_GET)) {
		$link .= "?redirect=" . $_GET['redirect'];
	}
?>


	<div class='register-link'>
		<p>Don't have an account? <?php $this->link_to($link, 'Click here'); ?></p>
	</div>
</div>
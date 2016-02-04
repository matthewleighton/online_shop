<div id="box">
	<h2>Login</h2>
	<form action="login" method="post">
		<?php
			$this->createInput('text', 'email', 'Email', '');
			$this->createInput('password', 'password', 'Password', '');
		?>
		<?php if($this->data['loginError'] == true) { ?>
			<div>
				Invalid email/password combination
			</div>
		<?php } ?>
		<input type='submit', value='Log in'>
	</form>


</div>
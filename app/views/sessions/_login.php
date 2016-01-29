<div id="box">
	<h2>Login</h2>
	<form action="login" method="post">
		<?php
			$this->createInput('text', 'email', 'Email', '');
			$this->createInput('text', 'password', 'Password', '');
		?>
		<input type='submit', value='Log in'>
	</form>
</div>
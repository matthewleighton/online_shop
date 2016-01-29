<div id="account-creation">
	<a href="/online_shop/public"><img src='/online_shop/public/assets/img/logo_placeholder.png' alt='logo' height='70'/></a>
	<div id="account-creation-container">
		<h2>Create an Account</h2>
		<form action="newuser" method="post"> 
			<?php $this->createInput('text', 'email', 'Email'); ?> <br>
			<?php $this->displayError($this->data['user'], 'email'); ?>

			<?php $this->createInput('password', 'password', 'Password'); ?> <br>
			<?php $this->displayError($this->data['user'], 'password'); ?>

			<?php $this->createInput('password', 'password_confirmation', 'Confirm password'); ?> <br>
			<?php $this->displayError($this->data['user'], 'password_confirmation'); ?>

			<div class="first-name-input">
				<?php $this->createInput('text', 'first_name', 'First name', ['class' => 'ac-short']); ?>
				<?php $this->displayError($this->data['user'], 'first_name'); ?>
			</div>

			<div class="last-name-input">
				<?php $this->createInput('text', 'last_name', 'Last name', ['class' => 'ac-short']); ?>
				<?php $this->displayError($this->data['user'], 'last_name'); ?>
			</div>

			<br>
			<input type="submit" value="Register">
		</form>
	</div>
</div>
<div id="box">
	<h2>Create an Account</h2>
	<form action="newuser" method="post"> 
		<?php $this->createInput('text', 'email', 'Email', 'user'); ?> <br>
		<?php $this->displayError($this->data['user'], 'email'); ?>

		<?php $this->createInput('password', 'password', 'Password', 'user'); ?> <br>
		<?php $this->displayError($this->data['user'], 'password'); ?>

		<?php $this->createInput('password', 'password_confirmation', 'Confirm password', 'user'); ?> <br>
		<?php $this->displayError($this->data['user'], 'password_confirmation'); ?>

		<div class="first-name-input">
			<?php $this->createInput('text', 'first_name', 'First name', 'user', ['class' => 'ac-short']); ?>
			<?php $this->displayError($this->data['user'], 'first_name'); ?>
		</div>

		<div class="last-name-input">
			<?php $this->createInput('text', 'last_name', 'Last name', 'user', ['class' => 'ac-short']); ?>
			<?php $this->displayError($this->data['user'], 'last_name'); ?>
		</div>

		<br>
		<input type="submit" value="Register">
	</form>
</div>
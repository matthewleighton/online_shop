<div id="account-header">
	<div>
		<span>
			<?php if(Sessions_helper::logged_in()) { ?>
				Hello, <?php echo Sessions_helper::currentUser()['first_name']; ?>!
				<span class="logout"><?php $this->link_to('sessions/logout', 'Logout'); ?></span>
			<?php } else { ?>
				Hello. <?php $this->link_to('sessions/login', 'Sign in') ?> or 
				<?php $this->link_to('users/newuser', 'register'); ?>.
			<?php } ?>
		</span>
	</div>
</div>
<div>
	<?php 
		if (isset($product['running_time'])) {
			echo "<p><b>Running time:</b> " . $this->printRunningTime($product['running_time']) . "</p>";
		}
	?>
</div>
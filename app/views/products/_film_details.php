<div>
	<?php 
		if (isset($product['running_time'])) {
			echo "<p><b>Running time:</b> " . $this->printRunningTime($product['running_time']) . "</p>";
		}

		if (isset($product['directors'])) {
			echo '<p><b>Directed by:</b> ' . $this->arrayToString($product['directors']) . '</p>';
		}

		if (isset($product['actors'])) {
			echo '<p><b>Starring:</b> ' . $this->arrayToString($product['actors']) . '</p>';
		}

		if (isset($product['languages'])) {
			echo '<p><b>Language';
			echo count($product['languages']) > 1 ? 's' : '';
			echo ': </b>' . $this->arrayToString($product['languages']) . '</p>';
		}

		if (isset($product['subtitles'])) {
			echo '<p><b>Subtitles:</b> ' . $this->arrayToString($product['subtitles']) . '</p>';
		}

		
	?>
</div>
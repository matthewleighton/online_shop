<?php
	if(!$this->data['addressList'] == []) {
		foreach ($this->data['addressList'] as $address) {
?>
			<div class='input-page div-select js-select'>
				<p hidden class='address_id'><?php echo $address['address_id']; ?></p>
<?php
				foreach($address as $label => $value) {
					if($label != 'address_id' && $label != 'user_id' && $value != '') {
						echo "<strong>" . $this->formatLabel($label) . ":</strong> " . $value . "<br>";
					}
				}
?>
			</div>


<?php
		}
	} else {
		echo "You have no saved addresses.";
	}
?>
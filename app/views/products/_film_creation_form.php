<span class='small-field-label'>Age rating: </span><br>
<?php $this->createSelect('age_rating', ['No rating', 'U', 'PG', '12a', '15', '18'], 'product'); ?>

<?php $this->createInput('text', 'running_time', 'Running time (minutes)', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'running_time'); ?>

<?php $this->createInput('text', 'director', 'Director (seperate by comma)', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'running_time'); ?>
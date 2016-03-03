<?php $this->createInput('text', 'author', 'Author (separate by comma)', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'author'); ?>

<?php $this->createInput('text', 'page_count', 'Page count', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'page_count'); ?>

<?php $this->createSelect('book_type', ['paperback', 'hardcover'], 'product'); ?><br>
<?php $this->displayError($this->data['product'], 'book_type'); ?>

<?php $this->createInput('text', 'publisher', 'Publisher', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'publisher'); ?>

<?php $this->createInput('text', 'language', 'Language', 'product'); ?> <br>
<?php $this->displayError($this->data['product'], 'language'); ?>
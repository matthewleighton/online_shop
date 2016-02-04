<?php
	class Book extends Product {
		protected $sqlOptions = ['join' => ['book' => ['book.product_id', 'product.product_id'],
							 	 			'author_book' => ['author_book.book_id', 'book.book_id'],
								 			'author' => ['author.author_id', 'author_book.author_id']],
								 'concat' => ['author.author_name', 'authors'],
								 'groupby' => 'product.product_id'];
	}
?>
<?php
	class Book extends Product {
		/*protected $sqlOptions = ['join' => ['book' => ['book.fk_book_product', 'product.product_id'],
							 	 			'author' => ['author.fk_author_product', 'product.product_id'],
								 			'person' => ['person.person_id', 'author.fk_author_person']],
								 'concat' => [['person.person_name', 'authors']],
								 'groupby' => 'product.product_id'];*/

		/*protected $sqlOptions = ['join' => ['book' => ['fk_book_product', 'product.product_id'],
										    'madeby' => ['madeby.fk_madeby_product', 'product.product_id'],
											'person' => ['person.person_id', 'madeby.fk_madeby_person']],
								 'groupby' => 'product.product_id',
								 'concat' => [["CASE WHEN person_role = 'author' THEN person.person_name ELSE NULL END", 'authors']]];*/

		protected $bookColumns = ['page_count', 'book_type', 'publisher', 'language'];

		public function __construct() {
			$this->properties['page_count'] = '';
			$this->validates('page_count', 'presence');

			$this->properties['book_type'] = '';
			$this->validates('book_type', 'presence');

			$this->properties['publisher'] = '';
			$this->validates('publisher', 'presence');
			
			parent::__construct();

			$this->sqlOptions['join']['book'] = ['fk_book_product', 'product.product_id'];
			array_push($this->sqlOptions['concat'],
				["CASE WHEN person_role = 'author' THEN person.person_name ELSE NULL END", 'authors']);


		}

		public function build() {
			$this->assignProperties($_POST);
			
			if ($this->runValidations()) {
				
				// Generating array of authors
				$authorIdArray = [];
				foreach ($_POST['authors'] as $author) {
					$sql = "SELECT * FROM author WHERE author_name='" . $author . "'";
					$results = $this->runSql($sql);
					$resultsArray = $this->createResultsArray($results);

					if (isset($resultsArray[0]['author_id'])) {
						echo "That author already exists!<br><br>";
						array_push($authorIdArray, $resultsArray[0]['author_id']);
					} else {
						echo "That is a new author!<br><br>";
						$authorId = $this->saveToDb('INSERT', 'author', ['author_name' => $author]);
						array_push($authorIdArray, $authorId);
					}
				}

				// Saving the base product to the database
				$productTableProperties = [];		
				foreach ($this->properties as $key => $value) {
					if (in_array($key, $this->productColumns)) {
						$productTableProperties[$key] = $value;
					}
				}
				$productId = $this->saveToDb('INSERT', 'product', $productTableProperties);

				// Saving product to book table
				$bookTableProperties = ['product_id' => $productId];
				foreach ($this->properties as $key => $value) {
					if (in_array($key, $this->bookColumns)) {
						$bookTableProperties[$key] = $value;
					}
				}
				$bookId = $this->saveToDb('INSERT', 'book', $bookTableProperties);
				
				// Linking the authors to the book in the author_book table
				foreach ($authorIdArray as $authorId) {
					$columnValues = ['author_id' => $authorId, 'book_id' => $bookId];
					$this->saveToDb('INSERT', 'author_book', $columnValues);
				}
			}
			
		}

	}
?>
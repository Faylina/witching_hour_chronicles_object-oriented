<?php
#********************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#********************************************************************#



				#****************************************#
				#********** CLASS BLOG ******************#
				#****************************************#

				/**
				 * @class This class represents a blog post and contains
				 * information about its ID, title, the time and date
				 *  the post was published and if the blog post 
				 * contains an image, also the path to the image as 
				 * well as its alignment. 
				 * Moreover, this class a Category object with a 
				 * category ID and label and a User object with the
				 * name, email address, location, ID and password.
				 */

				
#********************************************************************#


				class Blog {
					
					#*******************************#
					#********** ATTRIBUTES *********#
					#*******************************#
					
					/**
					 * @var integer
					 * @range(1, 11)
					 */
					private $blogID;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('headline')
					 */
					private $blogHeadline;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('blogImagePath')
					 */
					private $blogImagePath;

					/**
					 * @var string
					 * @range(5, 6)
					 * @label('blogImageAlignment')
					 */
					private $blogImageAlignment;

					/**
					 * @var string
					 * @range(5, 20.000)
					 * @label('content')
					 */
					private $blogContent;

					/**
					 * @var string
					 */
					private $blogDate;

					/**
					 * @var Object (Category)
					 */
					private $category;

					/**
					 * @var Object (User)
					 */
					private $user;

					#*************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#

					/**
					 * @construct Creates a blog object with a headline, image path, image alignment,
					 * content, date of publishing, ID and the objects Category and User.
					 * 
					 * @param NULL|string 			$blogHeadline 		= NULL				headline
					 * @param NULL|string 			$blogImagePath 		= NULL				image path
					 * @param NULL|string 			$blogImageAlignment = NULL				image alignment
					 * @param NULL|string 			$blogContent 		= NULL				content
					 * @param NULL|string 			$blogDate 			= NULL				date of publishing
					 * @param Object				$category 			= new Category()	Category object
					 * @param Object				$user 				= new User()		User object
					 * @param NULL|string|integer 	$blogID 			= NULL				blog ID
					 * 
					 * @return void
					 */
					
					public function __construct($blogHeadline 		= NULL,
												$blogImagePath 		= NULL,
												$blogImageAlignment = NULL,
												$blogContent 		= NULL,
												$blogDate 			= NULL,
												$category 			= new Category(),
												$user 				= new User(),
												$blogID 			= NULL)  
					{
						debugConstructorInvoke(__METHOD__);	

						$this->setCategory($category);
						$this->setUser($user);

						if($blogHeadline 		!== '' AND $blogHeadline 		!== NULL) $this->setBlogHeadline ($blogHeadline);
						if($blogImagePath 		!== '' AND $blogImagePath 		!== NULL) $this->setBlogImagePath($blogImagePath);
						if($blogImageAlignment 	!== '' AND $blogImageAlignment 	!== NULL) $this->setBlogImageAlignment($blogImageAlignment);
						if($blogContent 		!== '' AND $blogContent 		!== NULL) $this->setBlogContent($blogContent);
						if($blogDate 			!== '' AND $blogDate 			!== NULL) $this->setBlogDate($blogDate);
						if($blogID 				!== '' AND $blogID 				!== NULL) $this->setBlogID($blogID);
						
						debugConstructorObject(__METHOD__, $this);
					}
					
					
					#*********************************#
					#********** DESTRUCTOR ***********#
					#*********************************#

					/**
					 * @destruct Is called when the Blog object is no longer
					 * required (e.g. at the end of the script).
					 * 
					 * @return void
					 */
					
					public function __destruct() {
						debugDestructor(__METHOD__);						
					}
					
					
					#*************************************************#

					
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					#********** BLOG ID **********#

					public function getBlogID():NULL|int {
						return $this->blogID;
					}

					public function setBlogID(string|int $value):void {
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
							// error
							debugError('Date format must be integer.');		

						} else {
							// success
							$this->blogID = $value;
						}	
					}


					#********** BLOG HEADLINE **********#

					public function getBlogHeadline():NULL|string {
						return $this->blogHeadline;
					}

					public function setBlogHeadline(string $value):void {
						$this->blogHeadline = sanitizeString($value);
					}


					#********** BLOG IMAGE PATH **********#

					public function getBlogImagePath():NULL|string {
						return $this->blogImagePath;
					}

					public function setBlogImagePath(string $value):void {
						$this->blogImagePath = sanitizeString($value);
					}


					#********** BLOG IMAGE ALIGNMENT **********#

					public function getBlogImageAlignment():NULL|string {
						return $this->blogImageAlignment;
					}

					public function setBlogImageAlignment(string $value):void {
						$this->blogImageAlignment = sanitizeString($value);
					}


					#********** BLOG CONTENT **********#

					public function getBlogContent():NULL|string {
						return $this->blogContent;
					}

					public function setBlogContent(string $value):void {
						$this->blogContent = sanitizeString($value);
					}


					#********** BLOG DATE **********#

					public function getBlogDate():NULL|string {
						return $this->blogDate;
					}

					public function setBlogDate(string $value):void {
						$this->blogDate = sanitizeString($value);
					}


					#********** CATEGORY **********#

					public function getCategory():NULL|Category {
						return $this->category;
					}

					public function setCategory(Category $value):void {
						$this->category = $value;
					}


					#********** USER **********#

					public function getUser():NULL|User {
						return $this->user;
					}

					public function setUser(User $value):void {
						$this->user = $value;
					}


					#********** DELEGATION **********#

					#********** USER FULL NAME ******#

					/**
					 * returns the first and last name of the user
					 * 
					 * @return NULL|string 		output: first name last name
					 */

					public function getUserFullName():NULL|string {
						return $this->getUser()->getUserFullName();
					}
					
					
					#*************************************************#
					

					#******************************#
					#********** METHODS ***********#
					#******************************#

					#********** FETCH FROM DB **********#

					/**
					 * Fetches blog data and the respective category and 
					 * user data from the database and returns an array
					 * of Blog objects that contain a full blog post
					 * respectively.
					
					 * If a category ID is passed to the function, only
					 * blog posts of the requested category are returned.
					 * 
					 * Works without an instantiated object. 
					 * 
					 * @param Object 		$PDO 						database connection
					 * @param NULL|integer 	$categoryFilterID = NULL	Category ID
					 * 
					 * @return Array 		$blogObjectsArray			array consisting of Blog objects,
					 * 													including the integrated Category
					 * 													and User objects or alternatively
					 * 													an empty array if no data exists.
					 */

					public static function fetchFromDB(PDO $PDO, NULL|int $categoryFilterID = NULL):Array {
						debugMethod(__METHOD__);

						// to prevent error in the case of an empty database
						$blogObjectsArray = array();

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						/*
							Here are two kinds of db operation needed:
							case a) means all blog entries are loaded
							case b) means only blog entries matching a given category id are loaded
							
							For both cases we need a basic sql statement:
						*/
						$sql = 	'SELECT * FROM Blog 
								 INNER JOIN User USING(userID)
								 INNER JOIN Category USING(catID)';
						
						// case a) No condition and therefore no placeholder needed
						$placeholders = array();	

						#********** A) FETCH ALL BLOG ENTRIES **********#
						if( $categoryFilterID === NULL ) {
							debugProcessStart('Fetching all blog posts...');
							
						#********** B) FILTER BLOG ENTRIES BY CATEGORY ID **********#				
						} else {
							debugProcessStart("Fetching blog posts by category $categoryFilterID");			

							/*
								for case b) a condition for the category filter 
								has to be added to the sql statement
							*/
							$sql .=	' WHERE catID = ?';
					
							/*
								And therefore a placeholder must be assigned and filled with a value
							*/
							$placeholders[] = $categoryFilterID;
						}

						/*
							for both cases finally add the 'order by' command, which has to be 
							the last command in the sql statement (after any WHERE condition)
						*/
						$sql .= ' ORDER BY blogDate DESC';	
						

						// Step 3 DB: Prepared Statements

						try {
							// Prepare: prepare the SQL-Statement
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: execute the SQL-Statement and include the placeholder
							$PDOStatement->execute($placeholders);
							// showQuery($PDOStatement);
							
						} catch(PDOException $error) {
							debugErrorDB($error);										
						}	

						// Step 4 DB: evaluate the DB-operation and close the DB connection

						#******* CREATE OBJECT ARRAY ********#
						while( $resultSet = $PDOStatement->fetch(PDO::FETCH_ASSOC)) {

							#******* CREATE CATEGORY OBJECT ********#

							// $catLabel = NULL, $catID = NULL
							$category = new Category($resultSet['catLabel'], $resultSet['catID']);

							#******* CREATE USER OBJECT ********#
							/*
								$userFirstName 	= NULL,
								$userLastName 	= NULL,
								$userEmail 		= NULL,
								$userCity 		= NULL,
								$userPassword 	= NULL,
								$userID 		= NULL
							*/
							$user = new User(	$resultSet['userFirstName'], 
												$resultSet['userLastName'], 
												$resultSet['userEmail'], 
												$resultSet['userCity'], 
												$resultSet['userPassword'], 
												$resultSet['userID']);

							#******* CREATE BLOG OBJECT ********#
							/*
								$blogHeadline 		= NULL,
								$blogImagePath 		= NULL,
								$blogImageAlignment = NULL,
								$blogContent 		= NULL,
								$blogDate 			= NULL,
								$category 			= new Category(),
								$user 				= new User(),
								$blogID 			= NULL
							*/
							$blogObjectsArray[$resultSet['blogID']] = new Blog(	$resultSet['blogHeadline'], 
																				$resultSet['blogImagePath'], 
																				$resultSet['blogImageAlignment'], 
																				$resultSet['blogContent'], 
																				$resultSet['blogDate'],
																				$category, 
																				$user, 
																				$resultSet['blogID']);
						} // CREATE OBJECT ARRAY END

						debugArray('blogObjectsArray', $blogObjectsArray);

						return $blogObjectsArray;
					}


					#********** SAVE POST TO DB **********#

					/**
					 * Saves a new blog entry to the database consisting of a headline,
					 * the content, a Category and User ID, image alignment and path
					 * and returns the number of the saved entries. 
					 * 
					 * @param Object 	$PDO 		database connection
					 * 
					 * @return integer 	$rowCount	number of saved entries
					 */

					public function saveToDB(PDO $PDO):int {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'INSERT INTO Blog 
								(blogHeadline, 
								 blogImagePath, 
								 blogImageAlignment, 
								 blogContent, 
								 catID, 
								 userID)
								VALUES (?, ?, ?, ?, ?, ?) ';
							
						$placeholders = array( 	$this->getBlogHeadline(),
												$this->getBlogImagePath(),
												$this->getBlogImageAlignment(),
												$this->getBlogContent(),
												$this->getCategory()->getCatID(),
												$this->getUser()->getUserID()
											);

						// Step 3 DB: Prepared Statements

						try {
							// Prepare: prepare the SQL-Statement
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: execute the SQL-Statement and include the placeholder
							$PDOStatement->execute($placeholders);
							// showQuery($PDOStatement);
							
						} catch(PDOException $error) {
							debugErrorDB($error);									
						}

						// Step 4 DB: evaluate the DB-operation and close the DB connection

						$rowCount = $PDOStatement->rowCount();	
						
						debugVariable('rowCount', $rowCount);

						return $rowCount;
					}


					#********** DELETE POST FROM DB **********#

					/**
					 * Deletes a blog entry from the database with a 
					 * specific ID and returns the number of the deleted 
					 * entries. 
					 * 
					 * @param Object 	$PDO 		database connection
					 * 
					 * @return integer 	$rowCount	number of saved entries
					 */

					public function deleteFromDB(PDO $PDO):int {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'DELETE FROM Blog 
								WHERE blogID = ?';
							
						$placeholders = array( $this->getBlogID() );

						// Step 3 DB: Prepared Statements

						try {
							// Prepare: prepare the SQL-Statement
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: execute the SQL-Statement and include the placeholder
							$PDOStatement->execute($placeholders);
							// showQuery($PDOStatement);
							
						} catch(PDOException $error) {
							debugErrorDB($error);									
						}

						// Step 4 DB: evaluate the DB-operation and close the DB connection

						$rowCount = $PDOStatement->rowCount();	
						
						debugVariable('rowCount', $rowCount);

						return $rowCount;
					}

					#*************************************************#
					
				}
				
#*********************************************************************#
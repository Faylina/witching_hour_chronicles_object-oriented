<?php
#**********************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#*********************************************************************#



				#****************************************#
				#********** CLASS CATEGORY **************#
				#****************************************#

				/**
				 * @class This class represents a category and
				 * contains information about its ID and the 
				 * name of the category. 
				 */

				
#*********************************************************************#


				class Category {
					
					#*******************************#
					#********** ATTRIBUTES *********#
					#*******************************#
					
					/**
					 * @var integer
					 * @range(1, 11)
					 */
					private $catID;

					/**
					 * @var string
					 * @range(0, 50)
					 * @label('category')
					 */
					private $catLabel;

					#*************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#

					/**
					 * @construct Creates a Category object with a category name 
					 * and ID.
					 * 
					 * @param NULL|string 			$catLabel 	= NULL	category name
					 * @param NULL|string|integer 	$catID 		= NULL	category ID
					 * 
					 * @return void
					 */
					
					public function __construct( $catLabel = NULL, $catID = NULL ) {
						debugConstructorInvoke(__METHOD__);	

						if($catLabel 	!== '' 	AND $catLabel 	!== NULL) 	$this->setCatLabel($catLabel);
						if($catID 		!== '' 	AND $catID 		!== NULL) 	$this->setCatID($catID);
						
						debugConstructorObject(__METHOD__, $this);
					}
					
					
					#*********************************#
					#********** DESTRUCTOR ***********#
					#*********************************#

					/**
					 * @destruct Is called when the Category object is no 
					 * longer required (e.g. at the end of the script).
					 * 
					 * @return void
					 */
					
					public function __destruct() {
						debugDestructor(__METHOD__);				
					}
					
					
					#************************************************#

					
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					#********** CAT ID **********#

					public function getCatID():NULL|int {
						return $this->catID;

					}

					public function setCatID(string|int $value):void {
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
							// error
							debugError('Date format must be integer.');		

						} else {
							// success
							$this->catID = $value;
						}	
					}


					#********** CAT LABEL **********#

					public function getCatLabel():NULL|string {
						return $this->catLabel;
					}

					public function setCatLabel(string $value):void {
						$this->catLabel = sanitizeString($value);
					}
					
					
					#*************************************************#
					

					#******************************#
					#********** METHODS ***********#
					#******************************#

					#********** CHECK IF EXISTS **********#

					/**
					 * Checks if the provided category exists in 
					 * the database. 
					 * 
					 * @param Object 	$PDO 			database connection
					 * 
					 * @return integer 	$categoryCheck	number of the found entries that 
					 * 									correspond with the provided category
					 * 									ID 
					 */

					public function checkIfExists(PDO $PDO):int {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

							$sql = 'SELECT COUNT(catLabel)
									FROM Category
									WHERE catLabel = ?';

							$placeholders = array($this->getCatLabel()); 
					
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

						$categoryCheck = $PDOStatement->fetchColumn();

						debugVariable('categoryCheck', $categoryCheck);

						return $categoryCheck;
					}


					#********** FETCH ALL FROM DB **********#

					/**
					 * Fetches category data from the database and 
					 * returns an array of Category objects that 
					 * contain one category respectively.
					 * 
					 * Works without an instantiated object. 
					 * 
					 * @param Object $PDO 						database connection
					 * 
					 * @return Array $allCategoryObjectsArray	array consisting of Category objects,
					 * 											or alternatively an empty array if no 
					 * 											data exists.
					 */

					public static function fetchAllFromDB(PDO $PDO):Array {
						debugMethod(__METHOD__);

						// to prevent error in the case of an empty database
						$allCategoryObjectsArray = array();

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'SELECT * FROM Category';

						$placeholders = array();

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

						while( $resultSet = $PDOStatement->fetch(PDO::FETCH_ASSOC)) {

							// $catLabel = NULL, $catID = NULL
							$allCategoryObjectsArray[$resultSet['catID']] = new Category($resultSet['catLabel'], $resultSet['catID']);
						}

						debugArray('allCategoryObjectsArray', $allCategoryObjectsArray);

						return $allCategoryObjectsArray;
					}


					#********** SAVE TO DB **********#

					/**
					 * Saves a new category entry to the database consisting of the 
					 * category name and returns the number of the saved entries. 
					 * 
					 * @param Object 	$PDO 		database connection
					 * 
					 * @return integer 	$rowCount	number of saved entries
					 */

					public function saveToDB(PDO $PDO):int {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'INSERT INTO Category (catLabel) VALUES (?)';
							
						$placeholders = array( $this->getCatLabel() );

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

					#************************************************#
					
				}
				
#*********************************************************************#
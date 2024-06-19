<?php
#***********************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#***********************************************************************#


				#*******************************#
				#********** CLASS AUTHOR *********#
				#*******************************#

				/**
				 * @class This class represents a author and
				 * contains information about their name, ID,
				 * email address, location and password for 
				 * their account on the website.
				 */


#**********************************************************************#


				class Author {
					
					#*******************************#
					#********** ATTRIBUTES *********#
					#*******************************#
					
					/**
					 * @var integer
					 * @range(1, 11)
					 */
					private $authorID;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $authorFirstName;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $authorLastName;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('email')
					 */
					private $authorEmail;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $authorCity;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('password')
					 */
					private $authorPassword;
					
					#**********************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#

					/**
					 * @construct Creates a Author object with their name, ID,
					 * email address, location and password.
					 * 
					 * @param NULL|string 			$authorFirstName 	= NULL	first name
					 * @param NULL|string 			$authorLastName 	= NULL	last name
					 * @param NULL|string 			$authorEmail 		= NULL	email address
					 * @param NULL|string 			$authorCity 		= NULL	location
					 * @param NULL|string 			$authorPassword 	= NULL	password
					 * @param NULL|string|integer 	$authorID 			= NULL	author ID
					 * 
					 * @return void
					 */

					
					public function __construct($authorFirstName 	= NULL,
												$authorLastName 	= NULL,
												$authorEmail 		= NULL,
												$authorCity 		= NULL,
												$authorPassword 	= NULL,
												$authorID 			= NULL) 
					{
						debugConstructorInvoke(__METHOD__);		

						if($authorFirstName 	!== '' AND $authorFirstName 	!== NULL) $this->setAuthorFirstName($authorFirstName);
						if($authorLastName 		!== '' AND $authorLastName 		!== NULL) $this->setAuthorLastName($authorLastName);
						if($authorEmail 		!== '' AND $authorEmail 		!== NULL) $this->setAuthorEmail($authorEmail);
						if($authorCity 			!== '' AND $authorCity 			!== NULL) $this->setAuthorCity($authorCity);
						if($authorPassword 		!== '' AND $authorPassword 		!== NULL) $this->setAuthorPassword($authorPassword);
						if($authorID 			!== '' AND $authorID 			!== NULL) $this->setAuthorID($authorID);
						
						debugConstructorObject(__METHOD__, $this);	
					}
					
					
					#*********************************#
					#********** DESTRUCTOR ***********#
					#*********************************#

					/**
					 * @destruct Is called when the Author object is no 
					 * longer required (e.g. at the end of the script).
					 * 
					 * @return void
					 */
					
					public function __destruct() {
						debugDestructor(__METHOD__);					
					}
					
					
					#*********************************************#

					
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					#********** AUTHOR ID **********#

					public function getAuthorID():NULL|int {
						return $this->authorID;
					}

					public function setAuthorID(string|int $value):void {
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
							// error
							debugError('Date format must be integer.');					

						} else {
							// success
							$this->authorID = $value;
						}	
					}


					#********** AUTHOR FIRST NAME **********#

					public function getAuthorFirstName():NULL|string {
						return $this->authorFirstName;
					}

					public function setAuthorFirstName(string $value):void {
						$this->authorFirstName = sanitizeString($value);
					}


					#********** AUTHOR LAST NAME **********#

					public function getAuthorLastName():NULL|string {
						return $this->authorLastName;
					}

					public function setAuthorLastName(string $value):void {
						$this->authorLastName = sanitizeString($value);
					}


					#********** AUTHOR EMAIL **********#

					public function getAuthorEmail():NULL|string {
						return $this->authorEmail;
					}

					public function setAuthorEmail(string $value):void {
						$this->authorEmail = sanitizeString($value);
					}


					#********** AUTHOR CITY **********#

					public function getAuthorCity():NULL|string {
						return $this->authorCity;
					}

					public function setAuthorCity(string $value):void {
						$this->authorCity = sanitizeString($value);
					}


					#********** AUTHOR PASSWORD **********#

					public function getAuthorPassword():NULL|string {
						return $this->authorPassword;
					}

					public function setAuthorPassword(string $value):void {
						$this->authorPassword = sanitizeString($value);
					}


					#********** VIRTUAL ATTRIBUTE **********#

					#********** AUTHOR FULL NAME *************#

					/**
					 * returns the first and last name of the author
					 * 
					 * @return NULL|string 			output: first name last name
					 */

					public function getAuthorFullName():NULL|string {
						return $this->getAuthorFirstName() . ' ' . $this->getAuthorLastName();
					}
					
					
					#*********************************************#
					

					#******************************#
					#********** METHODS ***********#
					#******************************#

					#********** FETCH FROM DB **********#

					/**
					 * Fetches author data from the database that
					 * corresponds to the provided author email 
					 * address and returns the requested author 
					 * object, if it exists in the database.
					 * 
					 * @param Object $PDO 			database connection
					 * 
					 * @return Object $authorObject	the requested Author object
					 */

					public function fetchFromDB(PDO $PDO):Author {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'SELECT 	authorID, authorFirstName, authorLastName, authorPassword 
								FROM 	Author 
								WHERE 	authorEmail = ?';
						
						$placeholders = array($this->getAuthorEmail());

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
						
						$authorData = $PDOStatement->fetch(PDO::FETCH_ASSOC);

						/*
							$authorFirstName 	= NULL,
							$authorLastName 	= NULL,
							$authorEmail 		= NULL,
							$authorCity 		= NULL,
							$authorPassword 	= NULL,
							$authorID 			= NULL
						*/ 

						$authorObject = new Author(authorFirstName: $authorData['authorFirstName'], authorLastName: $authorData['authorLastName'], authorPassword: $authorData['authorPassword'], authorID: $authorData['authorID']);

						debugObject('authorObject', $authorObject);

						return $authorObject;

					}
					
					#*************************************************#
					
				}
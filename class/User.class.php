<?php
#***********************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#***********************************************************************#


				#*******************************#
				#********** CLASS USER *********#
				#*******************************#

				/**
				 * @class This class represents a user and
				 * contains information about their name, ID,
				 * email address, location and password for 
				 * their account on the website.
				 */


#**********************************************************************#


				class User {
					
					#*******************************#
					#********** ATTRIBUTES *********#
					#*******************************#
					
					/**
					 * @var integer
					 * @range(1, 11)
					 */
					private $userID;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $userFirstName;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $userLastName;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('email')
					 */
					private $userEmail;

					/**
					 * @var string
					 * @range(0, 255)
					 */
					private $userCity;

					/**
					 * @var string
					 * @range(0, 255)
					 * @label('password')
					 */
					private $userPassword;
					
					#**********************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#

					/**
					 * @construct Creates a User object with their name, ID,
					 * email address, location and password.
					 * 
					 * @param NULL|string 			$userFirstName 	= NULL	first name
					 * @param NULL|string 			$userLastName 	= NULL	last name
					 * @param NULL|string 			$userEmail 		= NULL	email address
					 * @param NULL|string 			$userCity 		= NULL	location
					 * @param NULL|string 			$userPassword 	= NULL	password
					 * @param NULL|string|integer 	$userID 		= NULL	user ID
					 * 
					 * @return void
					 */

					
					public function __construct($userFirstName 	= NULL,
												$userLastName 	= NULL,
												$userEmail 		= NULL,
												$userCity 		= NULL,
												$userPassword 	= NULL,
												$userID 		= NULL) 
					{
						debugConstructorInvoke(__METHOD__);		

						if($userFirstName 	!== '' AND $userFirstName 	!== NULL) $this->setUserFirstName($userFirstName);
						if($userLastName 	!== '' AND $userLastName 	!== NULL) $this->setUserLastName($userLastName);
						if($userEmail 		!== '' AND $userEmail 		!== NULL) $this->setUserEmail($userEmail);
						if($userCity 		!== '' AND $userCity 		!== NULL) $this->setUserCity($userCity);
						if($userPassword 	!== '' AND $userPassword 	!== NULL) $this->setUserPassword($userPassword);
						if($userID 			!== '' AND $userID 			!== NULL) $this->setUserID($userID);
						
						debugConstructorObject(__METHOD__, $this);	
					}
					
					
					#*********************************#
					#********** DESTRUCTOR ***********#
					#*********************************#

					/**
					 * @destruct Is called when the User object is no 
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
				
					#********** USER ID **********#

					public function getUserID():NULL|int {
						return $this->userID;
					}

					public function setUserID(string|int $value):void {
						if( ($value = filter_var($value, FILTER_VALIDATE_INT)) === false ) {
							// error
							debugError('Date format must be integer.');					

						} else {
							// success
							$this->userID = $value;
						}	
					}


					#********** USER FIRST NAME **********#

					public function getUserFirstName():NULL|string {
						return $this->userFirstName;
					}

					public function setUserFirstName(string $value):void {
						$this->userFirstName = sanitizeString($value);
					}


					#********** USER LAST NAME **********#

					public function getUserLastName():NULL|string {
						return $this->userLastName;
					}

					public function setUserLastName(string $value):void {
						$this->userLastName = sanitizeString($value);
					}


					#********** USER EMAIL **********#

					public function getUserEmail():NULL|string {
						return $this->userEmail;
					}

					public function setUserEmail(string $value):void {
						$this->userEmail = sanitizeString($value);
					}


					#********** USER CITY **********#

					public function getUserCity():NULL|string {
						return $this->userCity;
					}

					public function setUserCity(string $value):void {
						$this->userCity = sanitizeString($value);
					}


					#********** USER PASSWORD **********#

					public function getUserPassword():NULL|string {
						return $this->userPassword;
					}

					public function setUserPassword(string $value):void {
						$this->userPassword = sanitizeString($value);
					}


					#********** VIRTUAL ATTRIBUTE **********#

					#********** USER FULL NAME *************#

					/**
					 * returns the first and last name of the user
					 * 
					 * @return NULL|string 			output: first name last name
					 */

					public function getUserFullName():NULL|string {
						return $this->getUserFirstName() . ' ' . $this->getUserLastName();
					}
					
					
					#*********************************************#
					

					#******************************#
					#********** METHODS ***********#
					#******************************#

					#********** FETCH FROM DB **********#

					/**
					 * Fetches user data from the database that
					 * corresponds to the provided user email 
					 * address and returns the requested User 
					 * object, if it exists in the database.
					 * 
					 * @param Object $PDO 			database connection
					 * 
					 * @return Object $userObject	the requested User object
					 */

					public function fetchFromDB(PDO $PDO):User {
						debugMethod(__METHOD__);

						// Step 2 DB: Create the SQL-Statement and a placeholder-array

						$sql = 'SELECT 	userID, userFirstName, userLastName, userPassword 
								FROM 	User 
								WHERE 	userEmail = ?';
						
						$placeholders = array($this->getUserEmail());

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
						
						$userData = $PDOStatement->fetch(PDO::FETCH_ASSOC);

						/*
							$userFirstName 	= NULL,
							$userLastName 	= NULL,
							$userEmail 		= NULL,
							$userCity 		= NULL,
							$userPassword 	= NULL,
							$userID 		= NULL
						*/ 

						$userObject = new User(userFirstName: $userData['userFirstName'], userLastName: $userData['userLastName'], userPassword: $userData['userPassword'], userID: $userData['userID']);

						debugObject('userObject', $userObject);

						return $userObject;

					}
					
					#*************************************************#
					
				}
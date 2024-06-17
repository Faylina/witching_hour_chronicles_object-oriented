<?php
#********************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				declare(strict_types=1);
				
				
#********************************************************************#
			
			
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				require_once('./include/config.inc.php');
				require_once('./include/db.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/dateTime.inc.php');
				require_once('./include/debugging.inc.php');
				
				
				#********** INCLUDE CLASSES **********#
				require_once('./class/User.class.php');
				require_once('./class/Category.class.php');
				require_once('./class/Blog.class.php');

#***************************************************************************************#

				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#				

				session_name('wwwwitchinghourchroniclesobjectcom');
				
				#********** START | CONTINUE SESSION	**********#

				if( session_start() === false ) {
					// error
					debugError('Error starting the session.');			
									
				} else {
					// success
					debugSuccess('The session has been started successfully.');							

					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#	
					

					#********** A) NO VALID LOGIN **********#
					if( isset($_SESSION['user']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// error
						debugAuth('User is not logged in.');	

						#********** DENY PAGE ACCESS **********#

						// 1. Delete session file
						session_destroy();
						
						// 2. Redirect to homepage
						header('LOCATION: index.php');
						
						// 3. Fallback in case of an error: end processing of the script
						exit();
					
					#********** B) VALID LOGIN **********#
					} else {
						// success
						debugAuth('Valid login.');				

						session_regenerate_id(true);						
						
						// fetch user data from session

						$loggedInUser = $_SESSION['user'];
						
					} // CHECK FOR VALID LOGIN END

				} // SECURE PAGE ACCESS END


#***************************************************************************************#	

			
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				#********* ERROR VARIABLES **************#
				$errorCategory			= NULL;
				$errorHeadline 			= NULL;
				$errorImageUpload 		= NULL;
				$errorContent 			= NULL;

				$dbError				= NULL;
				$dbSuccess				= NULL;
				$dbDeleteError          = NULL;
                $dbDeleteSuccess        = NULL;
                $info                   = NULL;
                $alert                  = NULL;

				#********* BLOG VARIABLES ***************#
				$newCategory 			= NULL;
				$newBlog 				= NULL;

				#********* VIEW & EDIT VARIABLES ********#
                $showView               = false;
                $showEdit               = false;
                $chosenBlog             = NULL;

				#********* GENERATE LIST OF ALLOWED MIME TYPES *********#

                $allowedMIMETypes       = implode(', ', array_keys(IMAGE_ALLOWED_MIME_TYPES));
                $mimeTypes              = strtoupper( str_replace( array('image/jpeg, ', 'image/'), '', $allowedMIMETypes));


#***************************************************************************************#

	
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				#********** PREVIEW GET ARRAY ***************#

				debugArray('_GET', $_GET);
				
				// Step 1 URL: Check whether the parameters have been sent

				if( isset($_GET['action']) ) {

					debugProcessStart("URL-parameter 'action' has been committed.");
								
					// Step 2 URL: Read, sanitize and output URL data
					debugProcessStart('The URL parameters are being read and sanitized...');

					$action = sanitizeString($_GET['action']);

					debugVariable('action', $action);

					// Step 3 URL: Branching

					#********** LOGOUT **********#
					if( $_GET['action'] === 'logout' ) {
						debugProcessStart('Logging out...');

						// 1. Delete session file
						session_destroy();

						// 2. Reload homepage
						header("Location: index.php");

						// 3. Fallback in case of an error: end processing of the script
						exit();


					#*************** DELETION **************#

					} elseif( $action === 'delete') {

                        debugProcessStart('Deleting data from database...');

						// fetch the blogID of the post to be deleted

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

						$chosenBlog = new Blog(blogID: $_SESSION['postToBeDeleted']->getBlogID(), blogImagePath: $_SESSION['postToBeDeleted']->getBlogImagePath());

						debugObject('chosenBlog', $chosenBlog);

                        #****************************************#
                        #************ DB OPERATIONS *************#
                        #****************************************#

                        // Step 1 DB: Connect to database

                        $PDO = dbConnect();

                        // Step 2 + 3 DB

                        $rowCount = $chosenBlog->deleteFromDB($PDO);

						// Step 4 DB: evaluate the DB-operation and close the DB connection
                        debugVariable('rowCount', $rowCount);
                        
                        if( $rowCount !== 1 ) {
                            // error
                            debugErrorDB('Deletion failed!');	
                        
                            // error message for user
                            $dbDeleteError = 'The blog post could not be deleted. Please try again later.';

                            // error message for admin
                            $logError   = 'Error trying to DELETE a BLOG POST to database.';

                            /******** WRITE TO ERROR LOG ******/

                            // create file

                            if( file_exists('./logfiles') === false ) {
                                mkdir('./logfiles');
                            }
                        
                            // create error message

                            $logEntry    = "\t<p>";
                            $logEntry   .= date('Y-m-d | h:i:s |');
                            $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                            $logEntry   .= '<i>' . $logError . '</i>';
                            $logEntry   .= "</p>\n";

                            // write error message to log

                            file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

                        } else {
                            // success
                            debugSuccess("$rowCount blog post has been successfully deleted.");
                        
                            $dbDeleteSuccess = 'The blog post has been successfully deleted.';

							#*********** DELETE OLD IMAGE FROM SERVER ************#

							if($chosenBlog->getBlogImagePath() !== NULL) {
								if( @unlink( $chosenBlog->getBlogImagePath()) === false ) {
									// error
									debugError("Error when attempting to delete the old image at '{$chosenBlog->getBlogImagePath()}'");	

									// error message for admin
									$logError   = 'Error trying to DELETE an OLD IMAGE from server.';

									/******** WRITE TO ERROR LOG ******/

									// create file

									if( file_exists('./logfiles') === false ) {
										mkdir('./logfiles');
									}

									// create error message

									$logEntry    = "\t<p>";
									$logEntry   .= date('Y-m-d | h:i:s |');
									$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
									$logEntry   .= '<i>' . $logError . '</i>';
									$logEntry   .= "</p>\n";

									// write error message to log

									file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

								} else {
									// success
									debugSuccess("Old image at '{$chosenBlog->getBlogImagePath()}' has been successfully deleted.");

								} // DELETE OLD IMAGE FROM SERVER END

							} // CHECK IF IMAGE EXISTS END

                        } // EVALUATE DB OPERATION END
                        
                        // close DB connection
                        dbClose($PDO);


                    #*************** CONFIRMATIONS **************#
                    
                    } elseif( $action === 'cancelDelete' OR $action = 'okay') {
                        debugProcessStart('Reloading page after cancel or confirmation...');

                        // delete blog ID from session
                        $_SESSION['postToBeDeleted'] = '';

                        // 2. Reload homepage
                        header('LOCATION: dashboard.php');

                        // 3. Fallback in case of an error: end processing of the script
                        exit();

					} // BRANCHING END
					
				} // PROCESS URL PARAMETERS END


#***************************************************************************************#			
	
				#*************************************************#
				#********** PROCESS FORM 'NEW CATEGORY' **********#
				#*************************************************#

				#********** PREVIEW POST ARRAY **********#

				debugArray('_POST', $_POST);

				// Step 1 FORM: Check whether the form has been sent
				if( isset($_POST['formNewCategory']) === true ) {

					debugProcessStart('The form "formNewCategory" has been sent.');						
												
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');
					
					$newCategory = new Category($_POST['b5']);

					debugObject('newCategory', $newCategory);
					
					// Step 3 FORM: Field validation
					debugProcessStart('Validating fields...');
					
					$errorCategory = validateInputString($newCategory->getCatLabel(), maxLength: 50);
					
					#********** CHECK IF CATEGORY NAME ALREADY EXISTS **********#
					
					#****************************************#
					#********** DB OPERATION ****************#
					#****************************************#
					
					// Step 1 DB: Connect to database
					
					$PDO = dbConnect();
					
					// Step 2 + 3 DB
					
					$categoryCheck = $newCategory->checkIfExists($PDO);
					
					// Step 4 DB: evaluate the DB-operation and close the DB connection
					
					if( $categoryCheck !== 0 ) {
						// error
						debugError("The category {$newCategory->getCatLabel()} already exists.");
					
						$errorCategory = 'This category already exists.'; 
					}

					dbClose($PDO);

					#********** FINAL FORM VALIDATION **********#

					if( $errorCategory !== NULL ) {
						// error
						debugError('The form contains errors!');

					} else {
						// success
						debugSuccess('The form is formally free of errors.');						
					
						// Step 4 FORM: data processing
										
						#********** SAVE CATEGORY TO DB **********#

						#****************************************#
						#********** DB OPERATION ****************#
						#****************************************#
					
						// Step 1 DB: Connect to database
					
						$PDO = dbConnect();
					
						// Step 2 + 3 DB
					
						$rowCount = $newCategory->saveToDB($PDO);
					
						// Step 4 DB: evaluate the DB-operation and close the DB connection
					
						if( $rowCount !== 1 ) {
							// error
							debugErrorDB("Error when attempting to save $rowCount category!");
					
							$dbError = 'An error has occurred! Please try again later.';
					
							// error message for admin
							$logErrorForAdmin = 'Error trying to SAVE a new CATEGORY to database.';
					
							#******** WRITE TO ERROR LOG ******#
					
							// create folder
					
							if( file_exists('./logfiles') === false ) {
								mkdir('./logfiles');
							}
					
							// create error message
					
							$logEntry    = "\t<p>";
							$logEntry   .= date('Y-m-d | h:i:s |');
							$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
							$logEntry   .= '<i>' . $logErrorForAdmin . '</i>';
							$logEntry   .= "</p>\n";
					
							// write error message to log
					
							file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);
										
						} else {
							// success								
							$newCategory->setCatID($PDO->lastInsertId());

							debugSuccess("The category {$newCategory->getCatLabel()} has been saved under the ID {$newCategory->getCatID()}.");
					
							$dbSuccess = "The category {$newCategory->getCatLabel()} has been saved successfully.";
													
							// clear the form
							$newCategory = NULL;
												
						} // SAVE CATEGORY TO DB END
					
						dbClose($PDO);
					
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM 'NEW CATEGORY' END

			
#***************************************************************************************#


				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#

				debugProcessStart('Fetching category data from database...');

				#****************************************#
				#********** DB OPERATION ****************#
				#****************************************#

				// Step 1 DB: Connect to database
				$PDO = dbConnect();	

				// Step 2 + 3 DB

				$allCategoryObjectsArray = Category::fetchAllFromDB($PDO);

				// Step 4 DB: Disconnect from database
				dbClose($PDO);


#***************************************************************************************#


				#***************************************************#
				#********** PROCESS FORM 'NEW BLOG POST' **********#
				#***************************************************#

				#********** PREVIEW POST ARRAY **********#

				debugArray('_POST', $_POST);
				
				// Step 1 FORM: Check whether the form has been sent
				if( isset($_POST['articleForm']) === true ) {	

					debugProcessStart('The form "articleForm" has been sent.');
					
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');
					
					#********** CREATE CATEGORY OBJECT **********#

					// $catLabel = NULL, $catID = NULL
					$category = new Category(catID:$_POST['b1']);
					

					#********** CREATE BLOG OBJECT **********#

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
					
					$newBlog = new Blog(blogHeadline:$_POST['b2'], blogImageAlignment:$_POST['b3'], blogContent:$_POST['b4'], category:$category, user:$loggedInUser); 
					
					debugObject('newBlog', $newBlog);
					
					// Step 3 FORM: Field validation
					debugProcessStart('Validating fields...');
					
					$errorCategory			= validateInputString($newBlog->getCategory()->getCatID(), maxLength:11);
					$errorHeadline 			= validateInputString($newBlog->getBlogHeadline());
					// The image alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
					$errorImageAlignment 	= validateInputString($newBlog->getBlogImageAlignment(), minLength:4, maxLength:5);
					$errorContent 			= validateInputString($newBlog->getBlogContent(), minLength:5, maxLength:20000);
					
					#********** WHITELISTING 1: CHECK IF CATEGORY NAME EXISTS IN DATABASE **********#

					if( array_key_exists($newBlog->getCategory()->getCatID(), $allCategoryObjectsArray) === false) {
						// error
						debugError('This category does not exist.');
					
						$errorCategory = 'This category does not exist.';
					}
					
					#********** WHITELISTING 2: IMAGE ALIGNMENT ***********#
					
					if( $newBlog->getBlogImageAlignment() !== 'left' AND $newBlog->getBlogImageAlignment() !== 'right') {
						// error
						debugError('Invalid image alignment.');
					
						$errorImageAlignment = 'Invalid image alignment.';
					}
					
					#********** FINAL FORM VALIDATION PART I (FIELDS VALIDATION) **********#					
					if( $errorCategory 			!== NULL OR 
						$errorHeadline 			!== NULL OR
						$errorImageAlignment 	!== NULL OR 
						$errorContent 			!== NULL ) 
					{
						// error
						debugError('FINAL FORM VALIDATION PART I: The form contains errors!');	
											
					} else {
						// success
						debugSuccess('FINAL FORM VALIDATION PART I: The form is formally free of errors.');	
					
						#**************************************************#
				        #************ OPTIONAL: IMAGE UPLOAD **************#
				        #**************************************************#

						debugProcessStart('Checking image upload...');
											
						// Check if a file has been uploaded
						if( $_FILES['image']['tmp_name'] === '' ) {

							debugOccurrence('Image upload is inactive');
											
						} else {
							debugOccurrence('Image upload is active');
					
							$validatedImageArray = validateImageUpload($_FILES['image']['tmp_name']);

							debugArray('validatedImageArray', $validatedImageArray);

							#********** VALIDATE IMAGE UPLOAD RESULTS **********#
							if( $validatedImageArray['imageError'] !== NULL ) {
								// error
								debugError("Image upload error: " . $validatedImageArray['imageError']);	

								$errorImageUpload = $validatedImageArray['imageError'];
													
							} elseif( $validatedImageArray['imagePath'] !== NULL ) {
								// success
								debugSuccess("The image has successfully saved here:" . $validatedImageArray['imagePath'] . ".");
					
								// save image path
								$newBlog->setBlogImagePath($validatedImageArray['imagePath']);

								debugObject('newBlog', $newBlog);

							} // VALIDATE IMAGE UPLOAD RESULTS END	

						} // OPTIONAL: FILE UPLOAD END

						#********** FINAL FORM VALIDATION PART II (IMAGE UPLOAD) **********#

						if( $errorImageUpload !== NULL ) {
							// error
							debugError("FINAL FORM VALIDATION PART II: Error for image upload: $validatedImageArray[imageError]");
										
						} else {
							// success
							debugSuccess('FINAL FORM VALIDATION PART II: The form is completely free of errors.');	

							// Step 4 FORM: data processing
					
							#********** SAVE BLOG ENTRY DATA INTO DB **********#
							debugProcessStart('Saving new blog post to database...');
					
							#****************************************#
							#********** DB OPERATION ****************#
							#****************************************#
					
							// Step 1 DB: Connect to database

							$PDO = dbConnect();

							// Step 2 + 3 DB

							$rowCount = $newBlog->saveToDB($PDO);

							// Step 4 DB: evaluate the DB-operation and close the DB connection
					
							if( $rowCount !== 1 ) {
								// error
								debugErrorDB("Error when attempting to save $rowCount category!");
					
								// error message for user
								$dbError = 'The blog post could not be saved. Please try again later.';
					
								// error message for admin
                                $logError   = 'Error trying to SAVE a new BLOG POST to database.';
					
								#******** WRITE TO ERROR LOG ******#
					
								// create file

								if( file_exists('./logfiles') === false ) {
									mkdir('./logfiles');
								}

								// create error message

								$logEntry    = "\t<p>";
								$logEntry   .= date('Y-m-d | h:i:s |');
								$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
								$logEntry   .= '<i>' . $logErrorForAdmin . '</i>';
								$logEntry   .= "</p>\n";
					
								// write error message to log

								file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);
														
							} else {
								// success
								$newBlog->setBlogID($PDO->lastInsertId());
												
								debugSuccess("$rowCount blog post has been saved to the database.");

                                $dbSuccess = "A new blog post has been saved.";
													
								// clear the form
								$newBlog = NULL;
													
							} // SAVE BLOG ENTRY INTO DB END

							dbClose($PDO);

						} // FINAL FORM VALIDATION PART II (IMAGE UPLOAD) END

					} // FINAL FORM VALIDATION PART I (FIELDS VALIDATION) END

				} // PROCESS FORM 'NEW BLOG POST' END


#*************************************************************************#



                #****************************************#
				#********* PROCESS EDIT FORM ************#
				#****************************************#

                #********** PREVIEW POST ARRAY **********#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent
                if( isset($_POST['editForm']) === true ) {

                    debugProcessStart('The form "editForm" has been sent.');
                    
                    // Step 2 FORM: Read, sanitize and output form data
                    debugProcessStart('Reading and sanitizing form data...');

					#********** CREATE CATEGORY OBJECT **********#

					// $catLabel = NULL, $catID = NULL
					$category = new Category(catID:$_POST['b8']);
					

					#********** CREATE BLOG OBJECT **********#

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
					
					$editedBlog = new Blog(blogHeadline:$_POST['b9'], blogImagePath:$_POST['b13'], blogImageAlignment:$_POST['b10'], blogContent:$_POST['b11'], category:$category, user:$loggedInUser, blogID:$_POST['b12']); 
					
					debugObject('editedBlog', $editedBlog);

                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');

                    $errorHeadline              = validateInputString( $editedBlog->getBlogHeadline() );
					$errorEditedImagePath       = validateInputString( $editedBlog->getBlogImagePath(), mandatory:false );
                    // $alignment is not mandatory but should return a value either way. It would indicate an error should it return empty.
                    $errorAlignment             = validateInputString( $editedBlog->getBlogImageAlignment(), minLength:4, maxLength:5 );
                    $errorContent               = validateInputString( $editedBlog->getBlogContent(), maxLength:10000 );
					$errorCategory              = validateInputString( $editedBlog->getCategory()->getCatID(), maxLength:11 );
                    $errorEditedBlogID          = validateInputString( $editedBlog->getBlogID(), maxLength:11 );

                    #**************** FINAL FORM VALIDATION 1 *****************#

                    if( $errorHeadline 			!== NULL OR 
                        $errorEditedImagePath 	!== NULL OR 
                        $errorAlignment 		!== NULL OR
                        $errorContent 			!== NULL OR
                        $errorCategory 			!== NULL OR 
                        $errorEditedBlogID 		!== NULL ) 
                    {
                        // error
                        debugError('FINAL FORM VALIDATION PART I: The form contains errors!');	

                        $showEdit = true;

                    } else {
                        // success
                        debugSuccess('FINAL FORM VALIDATION PART I: The form is formally free of errors.');	

                        #****************************************#
				        #************ IMAGE UPLOAD **************#
				        #****************************************#

                        debugProcessStart('Checking image upload...');

                        #************ CHECK IF IMAGE UPLOAD IS ACTIVE **************#

                        if( $_FILES['image']['tmp_name'] === '') {
                            // image upload is not active
                            debugOccurrence('Image upload is inactive');

                        } else {
                            // image upload is active
                            debugOccurrence('Image upload is active');

                            #************ VALIDATE IMAGE UPLOAD ********************#

                            $validatedImageArray = validateImageUpload( $_FILES['image']['tmp_name'] );

                            debugArray('validatedImageArray', $validatedImageArray);

                            if( $validatedImageArray['imageError'] !== NULL ) {
                                // error
                                debugError("Image upload error: " . $validatedImageArray['imageError']);	

                                $errorImageUpload = $validatedImageArray['imageError'];

                            } else {
                                // success
                                debugSuccess("The image has successfully saved here:" . $validatedImageArray['imagePath'] . ".");	

                                #*********** DELETE OLD IMAGE FROM SERVER ************#
								if($editedBlog->getBlogImagePath() !== NULL) {

									if( @unlink( $editedBlog->getBlogImagePath()) === false ) {
										// error
										debugError("Error when attempting to delete the old image at '{$editedBlog->getBlogImagePath()}'");	

										// error message for admin
										$logError   = 'Error trying to DELETE an OLD IMAGE from server.';

										/******** WRITE TO ERROR LOG ******/

										// create file

										if( file_exists('./logfiles') === false ) {
											mkdir('./logfiles');
										}

										// create error message

										$logEntry    = "\t<p>";
										$logEntry   .= date('Y-m-d | h:i:s |');
										$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
										$logEntry   .= '<i>' . $logError . '</i>';
										$logEntry   .= "</p>\n";

										// write error message to log

										file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

									} else {
										// success
										debugSuccess("Old image at '{$editedBlog->getBlogImagePath()}' has been successfully deleted.");

									} // DELETE OLD IMAGE FROM SERVER END

								} // CHECK IF IMAGE EXISTS END

                                $editedBlog->setBlogImagePath($validatedImageArray['imagePath']);

                            } // VALIDATE IMAGE UPLOAD END

                        } // IMAGE UPLOAD END

                        #**************** FINAL FORM VALIDATION 2 (IMAGE UPLOAD VALIDATION) *****************#

                        if( $errorImageUpload !== NULL ) {
                            // error
                            debugError("FINAL FORM VALIDATION PART II: Error for image upload: $validatedImageArray[imageError]");

                        } else {
                            // success
                            debugSuccess('FINAL FORM VALIDATION PART II: The form is completely free of errors.');


                            // Step 4 FORM: data processing

                            #**************** UPLOAD DATA TO DATABASE *****************#

                            debugProcessStart('Updating blog post...');

                            #****************************************#
				            #************ DB OPERATIONS *************#
				            #****************************************#

                            // Step 1 DB: Connect to database

                            $PDO = dbConnect();

                            // Step 2 + 3 DB

							$rowCount = $editedBlog->updateToDB($PDO);

							debugVariable('rowCount', $rowCount);

                            // Step 4 DB: evaluate the DB-operation and close the DB connection

                            if( $rowCount !== 1 ) {
                                // error
                                debugErrorDB('The blog post could not be updated.');

                                // error message for user
                                $dbError    = 'The blog post could not be updated. Please try again later.';

                                // error message for admin
                                $logError   = 'Error trying to UPDATE a BLOG POST to database.';

                                /******** WRITE TO ERROR LOG ******/

                                // create file

                                if( file_exists('./logfiles') === false ) {
                                    mkdir('./logfiles');
                                }

                                // create error message

                                $logEntry    = "\t<p>";
                                $logEntry   .= date('Y-m-d | h:i:s |');
                                $logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
                                $logEntry   .= '<i>' . $logError . '</i>';
                                $logEntry   .= "</p>\n";

                                // write error message to log

                                file_put_contents('./logfiles/error_log.html', $logEntry, FILE_APPEND);

                            } else {
                                // success
                                debugSuccess("$rowCount blog post has been successfully updated.");

                                $dbSuccess = 'Your blog post has been updated.'; 

                            } // UPLOAD DATA TO DATABASE END

                            // close the DB connection
                            dbClose($PDO);

                        } // FINAL FORM VALIDATION 2 END

                    } // FINAL FORM VALIDATION 1 END

                } // PROCESS EDIT FORM END
		

#***************************************************************************************#


				#************************************************#
				#********** FETCH BLOG ENTRIES FROM DB **********#
				#************************************************#

				debugProcessStart("Fetching blog posts from database...");	

				#****************************************#
				#********** DB OPERATION ****************#
				#****************************************#
				
				// Step 1 DB: Connect to database
				$PDO = dbConnect();

				// Step 2 + 3 DB
				$blogObjectsArray = Blog::fetchFromDB($PDO);

				// Step 4 DB: disconnect from DB
				dbClose($PDO);


#*************************************************************************#


                #********************************************#
				#******** PROCESS VIEW & EDIT FORM **********#
				#********************************************#

                #******** PREVIEW POST ARRAY ****************#

                debugArray('_POST', $_POST);

                // Step 1 FORM: Check whether the form has been sent

                if( isset($_POST['previousPostsForm']) === true ) {
                    debugProcessStart('The form "previousPostsForm" has been sent.');
					
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');
                    
					$chosenBlog = new Blog(blogID:$_POST['b6']);
                    $operation  = sanitizeString($_POST['b7']);

                    debugObject('chosenBlog', $chosenBlog);
                    debugVariable('operation', $operation);
                    
                    // Step 3 FORM: Field validation
                    debugProcessStart('Validating fields...');
                    
                    $errorChosenBlog    = validateInputString($chosenBlog->getBlogID(), maxLength:11);
                    $errorOperation     = validateInputString($operation, minLength:4, maxLength:6);

					#********** WHITELISTING OPERATION **********#

					if(	$operation !== 'view' AND 
						$operation !== 'edit' AND 
						$operation !== 'delete') 
					{
						$errorOperation = 'This is not a valid operation.';
					}
                    

                    #********** FINAL FORM VALIDATION **********#
                    
                    if( $errorChosenBlog !== NULL OR $errorOperation !== NULL ) {
                        // error
                        debugError('The form contains errors!');
                    
                    } else {
                        //success
                        debugSuccess('The form is formally free of errors.');	
                    
                        // Step 4 FORM: data processing

                        #************ VIEW POST ************************#

                        if( $operation === 'view' ) {
                            debugProcessStart('Showing blog post...');

                            $showView = true; 


                        #************ START EDITING PROCESS *************#

                        } elseif( $operation === 'edit' ) {
                            debugProcessStart('Starting editing process...');

                            #********* USER AUTHORIZATION **********#

                            foreach( $blogObjectsArray AS $blogObject ) {

                                // find the blog in the blogArray that was chosen for editing
                                if ( $blogObject->getBlogID() == $chosenBlog->getBlogID() ) {

                                    // retrieve the user ID of the blog post to be edited
                                    $blogAuthorID = $blogObject->getUser()->getUserID();
                                }
                            }

                            // check whether the user is the author of the blog post
                            if( $blogAuthorID !== $loggedInUser->getUserID() ) {
                                // the user is not the author of the chosen blog post -> editing is prevented
                                debugError('The user is not the author of this post and may not alter the blog post.');	

                                $info = 'You have no permission to edit this post.';

                            } else {
                                // the user is the author of the chosen blog post -> editing is allowed
                                debugSuccess('The user is confirmed to be the author of this post.');

                                $showEdit = true;
                            }

                         #************ START DELETION PROCESS *************#

                        } elseif( $operation === 'delete' ) {
                            debugProcessStart('Starting deletion process...');

                            #********* USER AUTHORIZATION **********#

                            foreach( $blogObjectsArray AS $blogObject ) {

                                // find the blog in the blogArray that was chosen for deletion
                                if ( $blogObject->getBlogID() == $chosenBlog->getBlogID() ) {

                                    // retrieve the user ID of the blog post to be deleted
                                    $blogAuthorID       = $blogObject->getUser()->getUserID();
                                    $blogTitleToDelete  = $blogObject->getBlogHeadline();

									if($blogObject->getBlogImagePath() !== NULL) {
                                        $chosenBlog->setBlogImagePath($blogObject->getBlogImagePath());
                                    } 
                                }
                            }

                            // check whether the user is the author of the blog post
                            if( $blogAuthorID !== $loggedInUser->getUserID() ) {
                                // the user is not the author of the chosen blog post -> deletion is prevented
                                debugError('The blog post was not deleted because the user is not the author.');	

                                $info = 'You have no permission to delete this post.';

                            } else {
                                // the user is the author of the chosen blog post -> deletion is allowed
                                debugSuccess('The user is confirmed to be the author of this post.');

                                // store blog ID of the post to be deleted in session
                                $_SESSION['postToBeDeleted'] 	= $chosenBlog;

                                $alert = "Do you really want to delete the blog post $blogTitleToDelete?";

                            } // USER AUTHORIZATION END

                        } // PROCESS OPERATIONS END
                    
                    } // FINAL FORM VALIDATION END

                } // PROCESS VIEW & EDIT FORM END

#***************************************************************************************#				
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="./css/images/favicon.ico">
        <title>Witching Hour Chronicles - Dashboard</title>
        <link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
    </head>

    <body>

        <!-- ------------- LINK TO THE CODING SORCERESS BEGIN ------------------------- -->
         
        <div class="coding-sorceress">
            <a href="../../portfolio.php#projects"><< Go back to The Coding Sorceress</a>
         </div>

        <!-- ------------- LINK TO THE CODING SORCERESS END --------------------------- -->
         

        <!-- ------------- NAVIGATION BEGIN --------------------------- -->

        <nav class="navigation">

            <!-- ------------- NAV LINKS BEGIN ---------------------------- -->

            <a class="link" href="./index.php"><< Homepage</a>
            <a class="link" href="?action=logout">Logout >></a>

            <!-- ------------- NAV LINKS END ------------------------------ -->

        </nav>

        <!-- ------------- NAVIGATION END ----------------------------- -->


        <!-- ------------- HEADER BEGIN ------------------------------- -->
        <header>

            <img class="logo" src="./css/images/logo.png" alt="Parchment paper with a teal quill, a full moon in the background">
            <div class="title">
                <h1>Witching Hour Chronicles</h1>
                <div class="active-user">Happy writing, <?= $loggedInUser->getUserFullName() ?>!</div>
            </div>

        </header>
        <!-- ------------- HEADER END ---------------------------------- -->


        <!-- ------------- USER MESSAGE BEGIN ---------------------------------- -->

        <?php if(   $dbError            !== NULL OR 
                    $dbSuccess          !== NULL OR 
                    $info               !== NULL OR 
                    $alert              !== NULL OR 
                    $dbDeleteError      !== NULL OR  
                    $dbDeleteSuccess    !== NULL ): ?>
			<div class="overlay">
				<popupBox>
					<!-- Message -->
					<?php if( $dbError ):?>
						<h3 class="popup-error"><?= $dbError ?></h3>
					<?php elseif( $dbSuccess ): ?>
						<h3 class="popup-success"><?= $dbSuccess ?></h3>
					<?php elseif( $dbDeleteError ): ?>
						<h3 class="popup-error"><?= $dbDeleteError ?></h3>
					<?php elseif( $dbDeleteSuccess ): ?>
						<h3 class="popup-success"><?= $dbDeleteSuccess ?></h3>
					<?php elseif( $info ): ?>
						<h3 class="popup-error"><?= $info ?></h3>
					<?php elseif( $alert ): ?>
						<h3 class="popup-success"><?= $alert ?></h3>
					<?php endif ?>

					<!-- Button -->
					<?php if( $dbError OR $dbSuccess OR $info ): ?>
						<a class="button" href="./dashboard.php">Okay</a>
					<?php elseif( $alert ): ?>
						<a class="button" href="?action=cancelDelete">Cancel</a>
						<a class="button" href="?action=delete">Delete Post</a>
					<?php elseif( $dbDeleteError OR $dbDeleteSuccess ): ?>
						<a class="button" href="?action=okay">Okay</a>
					<?php endif ?>
				</popupBox> 
			</div>
        <?php endif ?>

        <!-- ------------- USER MESSAGE END ------------------------------------ -->


        <!-- ------------- MAIN CONTENT BEGIN ---------------------------------- -->

        <div class="forms">

            
            <?php if( $showView === true ): ?>

                <!-- ------------- BLOG POST BEGIN ---------------------------------- -->

                <div class="blog">

                    <!-- -------- Generate blog articles ---------- -->
                    <?php foreach( $blogObjectsArray AS $blogObject): ?>

                        <?php if( $blogObject->getBlogID() == $chosenBlog->getBlogID() ): ?>

                            <!-- Convert ISO time from DB to US time and split into date and time -->
                            <?php $dateArray = isoToUSDateTime( $blogObject->getBlogDate() ) ?>

                            <!-- Link to create new post -->
                            <a href="dashboard.php"><< Write a new blog post</a>

                            <!-- Blog header -->
                            <div class="blog-category">Category: <?= $blogObject->getCategory()->getCatLabel() ?></div>
                            <div class="blog-title"><?= $blogObject->getBlogHeadline() ?></div>
                            <div class="blog-meta">
                                <?= $blogObject->getUserFullName() ?> (<?= $blogObject->getUser()->getUserCity() ?>) 
                                wrote on <?= $dateArray['date'] ?> at <?= $dateArray['time'] ?> o'clock:
                            </div>

                            <!-- Blog content -->
                            <div class="container clearfix">
                                <!-- Prevent empty images from displaying --> 
                                <?php if( $blogObject->getBlogImagePath() !== NULL ): ?>
                                    <img class="<?= $blogObject->getBlogImageAlignment() ?>" src="<?= $blogObject->getBlogImagePath() ?>" alt="image for the blog article">
                                <?php endif ?>

                                <div class="blog-content"><?php echo nl2br( $blogObject->getBlogContent() ) ?></div>
                            </div>

                            <br>
                            <hr>
                            <br>

                        <?php endif ?>

                    <?php endforeach ?>
                </div>
                <!-- ------------- BLOG POST END ------------------------------------ -->
                        

            <?php elseif( $showEdit === true ): ?>

                <div class="article-form">  

                    <?php if( $chosenBlog !== NULL ): ?>

                        <!--------------- Edit form loaded for the first time ----------------->

                        <?php foreach( $blogObjectsArray AS $blogObject): ?>

                            <?php if( $blogObject->getBlogID() == $chosenBlog->getBlogID() ): ?>

                                <!-- ------------- EDIT FORM BEGIN ------------------------- -->

                                <form action="" class="edit-form" method="POST" enctype="multipart/form-data">

                                    <!-- Link to create new post -->
                                    <a href="dashboard.php"><< Write a new blog post</a>
                                    <br>
                                    <div class="form-heading">Edit blog post</div>
                                    <br>
                                    <input type="hidden" name="editForm">
                                    <input type="hidden" name="b12" value="<?= $blogObject->getBlogID() ?>">
                                    <input type="hidden" name="b13" value="<?= $blogObject->getBlogImagePath() ?>">

                                    <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                                    <!-- ------------- Category ------------- -->
                                    <label for="b8">Choose a category</label>
                                    <select name="b8" id="b8" class="form-text">
                                        <?php foreach( $allCategoryObjectsArray AS $dbCategoryObject ): ?>
                                            <option value="<?= $dbCategoryObject->getCatID() ?>" <?php if($dbCategoryObject->getCatID() == $blogObject->getCategory()->getCatID()) echo 'selected'?>>
                                                <?= $dbCategoryObject->getCatLabel() ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>

                                    <br>
                                    <!-- ------------- Title ---------------- -->
                                    <label for="b9">Write the title of your post</label>
                                    <div class="error"><?= $errorHeadline ?></div>
                                    <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $blogObject->getBlogHeadline() ?>">

                                    <br>
                                    <!-- ------------- Image Upload ---------- -->
                                    <fieldset>
                                        <legend>Upload an image</legend>

                                        <!-- ------------- Database Image ---------- -->

                                        <?php if( $blogObject->getBlogImagePath() !== NULL ): ?>
                                            <img class="left" src="<?= $blogObject->getBlogImagePath()?>" alt="image for the blog article">
                                        <?php endif ?>

                                        <!-- ------------- Image Info Text ---------- -->
                                        <p class="image-info">
                                            You may upload an image of the type <?= $mimeTypes ?>. <br>
                                            The width of the image may not exceed <?= IMAGE_MAX_WIDTH ?> pixels. <br>
                                            The height of the image may not exceed <?= IMAGE_MAX_HEIGHT ?> pixels. <br>
                                            The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024/1000 ?> MB.
                                        </p>
                                        <br>
                                        <!-- ------------- Image Upload ---------- -->
                                        <div class="error"><?= $errorImageUpload ?></div>
                                        <input type="file" name="image" class="image-button">
                                        <br>
                                        <br>
                                        <!-- ------------- Image Alignment ---------- -->
                                        <label for="b10">Choose the alignment of the image</label>
                                        <br>
                                        <select name="b10" id="b10" class="form-select">
                                            <option value="left" <?php if( $blogObject->getBlogImageAlignment() === 'left') echo 'selected' ?>>Left</option>
                                            <option value="right" <?php if( $blogObject->getBlogImageAlignment() === 'right') echo 'selected' ?>>Right</option>
                                        </select>
                                        <br>
                                    </fieldset>
                                    <br>

                                    <!-- ------------- Content ------------------ -->
                                    <label for="b11">Write your blog post</label>
                                    <div class="error"><?= $errorContent ?></div>
                                    <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $blogObject->getBlogContent() ?></textarea>
                                    <br>
                                    <input type="submit" class="form-button" value="Publish">
                                </form>
                                <!-- ------------- EDIT FORM END ---------------------------- -->

                            <?php endif ?>
                        <?php endforeach ?>
                    
                    <?php else: ?>
                        
                        <!-- Edit form in the case of an input error --> 

                        <!-- ------------- EDIT FORM BEGIN ------------------------- -->

                        <form action="" class="edit-form" method="POST" enctype="multipart/form-data">

                            <!-- Link to create new post -->
                            <a href="dashboard.php"><< Write a new blog post</a>
                            <br>
                            <div class="form-heading">Edit blog post</div>
                            <br>
                            <input type="hidden" name="editForm">
                            <input type="hidden" name="b12" value="<?= $editedBlog->getBlogID() ?>">
                            <input type="hidden" name="b13" value="<?= $editedBlog->getBlogImagePath() ?>">

                            <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                            <!-- ------------- Category ------------- -->
                            <label for="b8">Choose a category</label>
                            <select name="b8" id="b8" class="form-text">
                                <?php foreach( $allCategoryObjectsArray AS $dbCategoryObject ): ?>
                                    <option value="<?= $dbCategoryObject->getCatID() ?>" <?php if($dbCategoryObject->getCatID() == $editedBlog->getCategory()->getCatID()) echo 'selected'?>>
                                        <?= $dbCategoryObject->getCatLabel() ?>
                                    </option>
                                <?php endforeach ?>
                            </select>

                            <br>
                            <!-- ------------- Title ---------------- -->
                            <label for="b9">Write the title of your post</label>
                            <div class="error"><?= $errorHeadline ?></div>
                            <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $editedBlog->getBlogHeadline() ?>">

                            <br>
                            <!-- ------------- Image Upload ---------- -->
                            <fieldset>
                                <legend>Upload an image</legend>

                                <!-- ------------- Database Image ---------- -->

                                <?php if( $editedBlog->getBlogImagePath() !== NULL ): ?>
                                    <img class="left" src="<?= $editedBlog->getBlogImagePath() ?>" alt="image for the blog article">
                                <?php endif ?>

                                <!-- ------------- Image Info Text ---------- -->
                                <p class="image-info">
                                    You may upload an image of the type <?= $mimeTypes ?>. <br>
                                    The width of the image may not exceed <?= IMAGE_MAX_WIDTH ?> pixels. <br>
                                    The height of the image may not exceed <?= IMAGE_MAX_HEIGHT ?> pixels. <br>
                                    The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024/1000 ?> MB.
                                </p>
                                <br>
                                <!-- ------------- Image Upload ---------- -->
                                <div class="error"><?= $errorImageUpload ?></div>
                                <input type="file" name="image" class="image-button">
                                <br>
                                <br>
                                <!-- ------------- Image Alignment ---------- -->
                                <label for="b10">Choose the alignment of the image</label>
                                <br>
                                <select name="b10" id="b10" class="form-select">
                                    <option value="left" <?php if( $editedBlog->getBlogImageAlignment() === 'left') echo 'selected' ?>>Left</option>
                                    <option value="right" <?php if( $editedBlog->getBlogImageAlignment() === 'right') echo 'selected' ?>>Right</option>
                                </select>
                                <br>
                            </fieldset>
                            <br>

                            <!-- ------------- Content ------------------ -->
                            <label for="b11">Write your blog post</label>
                            <div class="error"><?= $errorContent ?></div>
                            <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $editedBlog->getBlogContent() ?></textarea>
                            <br>
                            <input type="submit" class="form-button" value="Publish">
                            </form>
                            <!-- ------------- EDIT FORM END ---------------------------- -->

                    <?php endif ?>
                </div>

            <?php else: ?>

                <!-- ------------- NEW BLOG POST FORM BEGIN ------------------------- -->

                <form class="article-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-heading">Write a new blog post</div>
                    <br>
                    <input type="hidden" name="articleForm">

                    <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                    <!-- ------------- Category ------------- -->
                    <label for="b1">Choose a category</label>
                    <select name="b1" id="b1" class="form-text">
						<?php if( empty($allCategoryObjectsArray) === false ): ?>	
							<?php foreach( $allCategoryObjectsArray AS $categoryObject ): ?>
								<option value="<?= $categoryObject->getCatID() ?>" <?php if($categoryObject->getCatID() == $newBlog?->getCategory()->getCatID()) echo 'selected'?>>
									<?= $categoryObject->getCatLabel() ?>
								</option>
							<?php endforeach ?>
						<?php else: ?>
							<option value='' class="error">Please create a category first. </option>			
						<?php endif ?>
                    </select>

                    <br>
                    <!-- ------------- Headline ---------------- -->
                    <label for="b2">Write the headline of your post</label>
                    <div class="error"><?= $errorHeadline ?></div>
                    <input type="text" class="form-text" name="b2" id="b2" placeholder="Headline" value="<?= $newBlog?->getBlogHeadline() ?>">

                    <br>
                    <!-- ------------- Image Upload ---------- -->
                    <fieldset>
                        <legend>Upload an image</legend>

                        <!-- ------------- Image Info Text ---------- -->
                        <p class="image-info">
                            You may upload an image of the type <?= $mimeTypes ?>. <br>
                            The width of the image may not exceed <?= IMAGE_MAX_WIDTH ?> pixels. <br>
                            The height of the image may not exceed <?= IMAGE_MAX_HEIGHT ?> pixels. <br>
                            The size of the file may not exceed <?= IMAGE_MAX_SIZE/1024/1000 ?> MB.
                        </p>
                        <br>
                        <!-- ------------- Image Upload ---------- -->
                        <div class="error"><?= $errorImageUpload ?></div>
                        <input type="file" name="image" class="image-button">
                        <br>
                        <br>
                        <!-- ------------- Image Alignment ---------- -->
                        <label for="b3">Choose the alignment of the image</label>
                        <br>
                        <select name="b3" id="b3" class="form-select">
                            <option value="left" <?php if( $newBlog?->getBlogImageAlignment() === 'left') echo 'selected' ?>>Left</option>
                            <option value="right" <?php if( $newBlog?->getBlogImageAlignment() === 'right') echo 'selected' ?>>Right</option>
                        </select>
                        <br>
                    </fieldset>
                    <br>

                    <!-- ------------- Content ------------------ -->
                    <label for="b4">Write your blog post</label>
                    <div class="error"><?= $errorContent ?></div>
                    <textarea name="b4" id="b4" class="textarea" cols="30" rows="25" placeholder="..."><?= $newBlog?->getBlogContent() ?></textarea>
                    <br>
                    <input type="submit" class="form-button" value="Publish">
                </form>
                    
                <!-- ------------- NEW BLOG POST FORM END ---------------------------- -->

            <?php endif ?>

            <div class="mini-forms">
                <!-- ------------- CATEGORY FORM BEGIN ------------------------- -->

                <form class="category-form" action="" method="POST">

                    <div class="form-heading">Create a new category</div>
                    
                    <input type="hidden" name="formNewCategory">
                    <br>
                    <label for="b5">Name the new category</label>
                    <div class="error"><?= $errorCategory ?></div>
                    <input type="text" class="form-text" name="b5" id="b5" placeholder="Category name" value="<?= $newCategory?->getCatLabel() ?>">
                    <br>
                    <input type="submit" class="form-button" value="Create category">

                </form>

                <!-- ------------- CATEGORY FORM END --------------------------- -->


                <!-- ------------- EDIT & VIEW FORM BEGIN ---------------------- -->

                <form class="category-form" action="" method="POST">

                    <div class="form-heading">Previous blog posts</div>
                    
                    <input type="hidden" name="previousPostsForm">
                    <br>
                    <!-- Blog post title -->
                    <label for="b6">Select a blog post</label>
                    <select name="b6" id="b6" class="form-text">
                        <?php foreach( $blogObjectsArray AS $blogObject ): ?>
                            <option value="<?= $blogObject?->getBlogID() ?>">
                                <?= $blogObject?->getBlogHeadline() ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <br>
                    <!-- Operation selection -->
                    <div class="radio-buttons">
                        <div>
                            <input type="radio" name="b7" id="view" value="view" checked>
                            <label for="view">View</label>
                        </div>
                        <div>
                            <input type="radio" name="b7" id="edit" value="edit">
                            <label for="edit">Edit</label>
                        </div>
                        <div>
                            <input type="radio" name="b7" id="delete" value="delete">
                            <label for="delete">Delete</label>
                        </div>
                    </div>
                    <br>
                    <input type="submit" class="form-button" value="Proceed">

                </form>

                <!-- ------------- EDIT & VIEW FORM END ------------------------ -->
            </div>
            
        </div>     
        
        <!-- ------------- MAIN CONTENT END ---------------------------------- -->

        <!-- ------------- FOOTER BEGIN -------------------------------- -->
        <footer>
            <div class="footer-container">
                <ul>
                    <li>Copyright</li> 
                    <li>&copy;</li> 
					<?php if(date('Y') > 2024): ?>
                        <li>THE CODING SORCERESS 2024 - <?= date('Y') ?></li>
                    <?php else: ?>
                        <li>THE CODING SORCERESS 2024</li>
                    <?php endif ?>
                </ul>
            </div>
        </footer>
        <!-- ------------- FOOTER END ---------------------------------- -->
    
    </body>
</html>
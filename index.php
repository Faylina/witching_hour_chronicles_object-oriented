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

#********************************************************************#


				#**************************************#
				#********** OUTPUT BUFFERING **********#
				#**************************************#
				
				if( ob_start() === false ) {
					// error
					debugError('Error attempting to start output buffering.');			
					
				} else {
					// success
					debugSuccess('Output buffering has been successfully started.');							
				}

#********************************************************************#
/*

				#*************************************#
				#********** TESTING CLASSES **********#
				#*************************************#
				
				#********** TESTING CONSTRUCTOR AND SETTERS FOR CLASS CATEGORY **********#

				debugProcessStart('Testing empty Category object:');
				$category1 = new Category();
				
				debugProcessStart('Testing filled Category object:');
				// $catLabel = NULL, $catID = NULL
				$category2 = new Category('Food', '3');
				
				debugProcessStart('Testing partly filled Category object:');
				// $catLabel = NULL, $catID = NULL
				$category3 = new Category(catID:4);


				#********** TESTING GETTERS FOR CLASS CATEGORY **********#

				debugProcessStart('Testing empty Category object:');

				debugVariable('catLabel', 	$category1->getCatLabel());
				debugVariable('catID', 		$category1->getCatID());
				
				debugProcessStart('Testing filled Category object:');

				debugVariable('catLabel', 	$category2->getCatLabel());
				debugVariable('catID', 		$category2->getCatID());
				
				
				#*******************************************************************#
				
				echo '<br><hr><br>';
				
				#*******************************************************************#


				#********** TESTING CONSTRUCTOR AND SETTERS FOR CLASS USER *********#

				debugProcessStart('Testing empty User object:');
				$user1 = new User();
				
				debugProcessStart('Testing filled User object:');

				//$userFirstName 	= NULL,
				//$userLastName 	= NULL,
				//$userEmail 		= NULL,
				//$userCity 		= NULL,
				//$userPassword 	= NULL,
				//$userID 			= NULL
				
				$user2 = new User('first name', 'last name', 'email@at.com', 'location', '1234', '5');
				
				debugProcessStart('Testing partly filled User object:');

				//$userFirstName 	= NULL,
				//$userLastName 	= NULL,
				//$userEmail 		= NULL,
				//$userCity 		= NULL,
				//$userPassword 	= NULL,
				//$userID 			= NULL
				
				$user3 = new User(userLastName:'last name', userEmail:'email@at.com', userPassword:'1234');


				#********** TESTING GETTERS FOR CLASS USER **********#

				debugProcessStart('Testing empty User object:');

				debugVariable('userFirstName', 	$user1->getUserFirstName());
				debugVariable('userLastName', 	$user1->getUserLastName());
				debugVariable('userEmail', 		$user1->getUserEmail());
				debugVariable('userCity', 		$user1->getUserCity());
				debugVariable('userPassword', 	$user1->getUserPassword());
				debugVariable('userID', 		$user1->getUserID());
				
				debugProcessStart('Testing filled User object:');

				debugVariable('userFirstName', 	$user2->getUserFirstName());
				debugVariable('userLastName', 	$user2->getUserLastName());
				debugVariable('userEmail', 		$user2->getUserEmail());
				debugVariable('userCity', 		$user2->getUserCity());
				debugVariable('userPassword', 	$user2->getUserPassword());
				debugVariable('userID', 		$user2->getUserID());

				#*******************************************************************#

				echo '<br><hr><br>';

				#*******************************************************************#
				
				
				#********** TESTING CONSTRUCTOR AND SETTERS FOR CLASS BLOG *********#

				debugProcessStart('Testing empty Blog object:');
				$blog1 = new Blog();
				
				debugProcessStart('Testing filled Blog object:');

				//$blogHeadline 		= NULL,
				//$blogImagePath 		= NULL,
				//$blogImageAlignment 	= NULL,
				//$blogContent 			= NULL,
				//$blogDate 			= NULL,
				//$category 			= new Category(),
				//$user 				= new User(),
				//$blogID 				= NULL
				
				$blog2 = new Blog('Title', 'Path', 'left', 'Lots of text...', 'date', $category2, $user2, '3');
				
				debugProcessStart('Testing partly filled Blog object:');

				//$blogHeadline 		= NULL,
				//$blogImagePath 		= NULL,
				//$blogImageAlignment 	= NULL,
				//$blogContent 			= NULL,
				//$blogDate 			= NULL,
				//$category 			= new Category(),
				//$user 				= new User(),
				//$blogID 				= NULL
			
				$blog3 = new Blog(blogContent:'Lots of text...', category:$category2, blogID:'3');


				#********** TESTING GETTERS FOR CLASS BLOG **********#

				debugProcessStart('Testing empty Blog object:');

				debugVariable('blogHeadline', 		$blog1->getBlogHeadline());
				debugVariable('blogImagePath', 		$blog1->getBlogImagePath());
				debugVariable('blogImageAlignment', $blog1->getBlogImageAlignment());
				debugVariable('blogContent', 		$blog1->getBlogContent());
				debugVariable('blogDate', 			$blog1->getBlogDate());
				debugObject('category', 			$blog1->getCategory());
				debugObject('user', 				$blog1->getUser());
				debugVariable('blogID', 			$blog1->getBlogID());
				
				debugProcessStart('Testing filled Blog object:');

				debugVariable('blogHeadline', 		$blog2->getBlogHeadline());
				debugVariable('blogImagePath', 		$blog2->getBlogImagePath());
				debugVariable('blogImageAlignment', $blog2->getBlogImageAlignment());
				debugVariable('blogContent', 		$blog2->getBlogContent());
				debugVariable('blogDate', 			$blog2->getBlogDate());
				debugObject('category', 			$blog2->getCategory());
				debugObject('user', 				$blog2->getUser());
				debugVariable('blogID', 			$blog2->getBlogID());

				#*******************************************************************#

				echo '<br><hr><br>';

				#*******************************************************************#
*/

#*************************************************************************#

				
				#****************************************#
				#********* INITIALIZE VARIABLES *********#
				#****************************************#

                $loggedIn   = false;
                $errorLogin = NULL; 
                $filterID   = NULL;


#*******************************************************************************************#

				#************************************#
				#********** VALIDATE LOGIN **********#
				#************************************#
				
				session_name("wwwwitchinghourchroniclescom");
				
				
				#********** START/CONTINUE SESSION **********#
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

						#************ DENY PAGE ACCESS ***********#
						session_destroy();
						
						#************ FLAG AS LOGGED OUT *********#
						$loggedIn = false;
					
					
					#********** B) VALID LOGIN **********#
					} else {
						// success
						debugAuth('Valid login.');			
					
						#************ GENERATE NEW SESSION ID ***********#
						session_regenerate_id(true);
								
						#************ FLAG AS LOGGED IN *****************#
						$loggedIn = true;
						
					} // CHECK FOR VALID LOGIN END
					
				} // VALIDATE LOGIN END


#*******************************************************************************************#


				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#
				
				#********** PREVIEW POST ARRAY **********#

				debugArray('_POST', $_POST);

				#****************************************#
						
				// Step 1 FORM: Check whether the form has been sent

				if( isset($_POST['formLogin']) === true ) {
					debugProcessStart('The form "loginForm" has been sent.');
										
					// Step 2 FORM: Read, sanitize and output form data
					debugProcessStart('Reading and sanitizing form data...');
					
					/*
						$userFirstName 	= NULL,
						$userLastName 	= NULL,
						$userEmail 		= NULL,
						$userCity 		= NULL,
						$userPassword 	= NULL,
						$userID 		= NULL
					*/

					$userLogin 	= new User(userEmail:$_POST['b1']);
					$password 	= sanitizeString($_POST['b2']);

					debugObject('userLogin', $userLogin);
					
					// Step 3 FORM: Field validation
					debugProcessStart('Validating fields...');
					
					$errorLoginEmail 		= validateEmail($userLogin->getUserEmail());
					$errorLoginPassword 	= validateInputString($password);
					
					
					#********** FINAL FORM VALIDATION **********#					
					if( $errorLoginEmail !== NULL OR $errorLoginPassword !== NULL ) {
						// error
						debugError('The login form contains errors!');		
						
						// neutral user message
						$loginError = 'Invalid email or password.';
											
					} else {
						// success
						debugSuccess('The form is formally free of errors.');					
														
						// Step 4 FORM: data processing
					
						#**********************************#
						#********** DB OPERATION **********#
						#**********************************#
						
						#********** FETCH USER DATA FROM DB BY EMAIL **********#	
					
						// Step 1 DB: Connect to database
						$PDO = dbConnect();
					
						// Step 2 + 3 DB 
						$userObjectFromDB = $userLogin->fetchFromDB($PDO);
					
						// Step 4 DB: close DB connection
						dbClose($PDO);

						debugObject('userObjectFromDB', $userObjectFromDB);
					
						#********** VERIFY LOGIN EMAIL **********#			
					
						if( $userObjectFromDB === false ) {
							// error
							debugError('The email could not be found in the database!');
												
							// neutral user message
							$loginError = 'Invalid email or password.';
						
						} else {
							// success
							debugSuccess('The email has been found in the database.');
																			
							#********** VERIFY PASSWORD **********#
													
							if( password_verify( $password, $userObjectFromDB->getUserPassword()) === false ) {
								// error
								debugError('The password in the form does not match the password in the database!');
					
								// neutral user message
								$loginError = 'Invalid email or password.';
													
							} else {
								// success
								debugSuccess('The password in the form matches the password in the database.');
																
								#********** START SESSION **********#
					
								if( session_start() === false ) {
									// error
									debugError('Error starting session!');		
					
									$loginError = 'Login is not possible. Please allow cookies in your browser.';
															
									// error message for admin
									$logErrorForAdmin = 'Error during login process.';
					
									#******** WRITE TO ERROR LOG ******#
							
									// create file
							
									if( file_exists('./logdocs') === false ) {
										mkdir('./logdocs');
									}
							
									// create error message
							
									$logEntry    = "\t<p>";
									$logEntry   .= date('Y-m-d | h:i:s |');
									$logEntry   .= 'FILE: <i>' . __FILE__ . '</i> |';
									$logEntry   .= '<i>' . $logErrorForAdmin . '</i>';
									$logEntry   .= "</p>\n";
							
									// put error message into the error log
							
									file_put_contents('./logdocs/error_log.html', $logEntry, FILE_APPEND);
															
								} else {
									// success
									debugSuccess('The session has been started successfully.');		
																								
									#********** SAVE USER DATA INTO SESSION **********#
					
									debugProcessStart('Writing user data to session...');
														
									$_SESSION['IPAddress'] 		= $_SERVER['REMOTE_ADDR'];
									$_SESSION['user']			= $userObjectFromDB;
									
									#********** REDIRECT TO DASHBOARD **********#

									header('Location: dashboard.php');

								} // START SESSION END

							} // VERIFY PASSWORD END

					} // VERIFY LOGIN NAME END

				} // FINAL FORM VALIDATION END

			} // PROCESS FORM LOGIN END

#***************************************************************************************#


?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link rel="icon" type="image/x-icon" href="./css/images/favicon.ico">
		<title>Witching Hour Chronicles - Homepage</title>

		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
		
	</head>
	<body>
		<!-- ------------- NAVIGATION BEGIN --------------------------- -->

        <nav class="navigation">

            <!-- toggle navigation depending on the login status -->
            <?php if ( $loggedIn === false ): ?>

                <!-- ------------- LOGIN FORM BEGIN --------------------------- -->

                <form action="" method="POST">
                    <input type="hidden" name="loginForm">

                    <fieldset>
                        <legend>Author Login</legend>
                        <div class="error"><?= $errorLogin ?></div>
                        <input class="login-field" type="text" name="b1" placeholder="Email">
                        <input class="login-field" type="password" name="b2" placeholder="Password">

                        <input class="submit-button" type="submit" value="Login">
                    </fieldset>
                </form>

                <!-- ------------- LOGIN FORM END ----------------------------- -->

            <?php else: ?>

                <!-- ------------- NAV LINKS BEGIN ---------------------------- -->

                <a class="link" href="./dashboard.php"><< Dashboard</a>
                <a class="link" href="?action=logout">Logout >></a>

                <!-- ------------- NAV LINKS END ------------------------------ -->
            <?php endif ?>

        </nav>
        <!-- ------------- NAVIGATION END ----------------------------- -->


        <!-- ------------- HEADER BEGIN ------------------------------- -->
        <header>

            <img class="logo" src="./css/images/logo.png" alt="Parchment paper with a teal quill, a full moon in the background">
            <div class="title">
                <h1>Witching Hour Chronicles</h1>
                <a href="index.php">Show all blog articles</a>   
            </div>

        </header>
        <!-- ------------- HEADER END ---------------------------------- -->


        <!-- ------------- MAIN CONTENT BEGIN -------------------------- -->

        <div class="content">

            <!-- ------------- BLOG BEGIN ---------------------------------- -->

            <div class="blog">

                <!-- -------- Generate blog articles ---------- -->
                <?php foreach( $blogArray AS $value): ?>

                    <!-- Convert ISO time from DB to EU time and split into date and time -->
                    <?php $dateArray = isoToEuDateTime( $value['blogDate'] ) ?>

                    <!-- Blog header -->
                    <div class="blog-category">Category: <?= $value['catLabel'] ?></div>
                    <div class="blog-title"><?= $value['blogHeadline'] ?></div>
                    <div class="blog-meta">
                        <?= $value['userFirstName'] ?> <?= $value['userLastName'] ?> (<?= $value['userCity'] ?>) 
                        wrote on <?= $dateArray['date'] ?> at <?= $dateArray['time'] ?> o'clock:
                    </div>

                    <!-- Blog content -->
                    <div class="container clearfix">
                        <!-- Prevent empty images from displaying --> 
                        <?php if( $value['blogImagePath'] !== NULL ): ?>
                            <img class="<?= $value['blogImageAlignment']?>" src="<?= $value['blogImagePath']?>" alt="image for the blog article">
                        <?php endif ?>

                        <div class="blog-content"><?php echo nl2br( $value['blogContent'] ) ?></div>
                    </div>

                    <br>
                    <hr>
                    <br>

                <?php endforeach ?>
            </div>
            <!-- ------------- BLOG END ------------------------------------ -->


            <!-- ------------- CATEGORIES BEGIN ---------------------------- -->

            <div class="categories">
                <div class="blog-title">Categories</div>
                <?php foreach( $categoryArray AS $value ): ?>
                    <a href="?action=filterByCategory&catID=<?= $value['catID'] ?>"><?= $value['catLabel'] ?></a>
                <?php endforeach ?>
            </div>

            <!-- ------------- CATEGORIES END ------------------------------ -->

        </div>
        <!-- ------------- MAIN CONTENT END -------------------------- -->


        <!-- ------------- FOOTER BEGIN -------------------------------- -->
        <footer>
            <div class="footer-container">
                <ul>
                    <li>Copyright</li> 
                    <li>&copy;</li> 
                    <li>Faylina 2024</li>
                </ul>
                <div><strong>Disclaimer:</strong> All images, apart from the logo and background, were generated by AI.</div>
            </div>
        </footer>
        <!-- ------------- FOOTER END ---------------------------------- -->

	</body>
</html>

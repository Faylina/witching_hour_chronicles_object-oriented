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
						$errorLogin = 'Invalid email or password.';
											
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
							$errorLogin = 'Invalid email or password.';
						
						} else {
							// success
							debugSuccess('The email has been found in the database.');
																			
							#********** VERIFY PASSWORD **********#
													
							if( password_verify( $password, $userObjectFromDB->getUserPassword()) === false ) {
								// error
								debugError('The password in the form does not match the password in the database!');
					
								// neutral user message
								$errorLogin = 'Invalid email or password.';
													
							} else {
								// success
								debugSuccess('The password in the form matches the password in the database.');
																
								#********** START SESSION **********#
					
								if( session_start() === false ) {
									// error
									debugError('Error starting session!');		
					
									$errorLogin = 'Login is not possible. Please allow cookies in your browser.';
															
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


				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#

				debugProcessStart('Fetching categories...');

				#****************************************#
				#********** DB OPERATION ****************#
				#****************************************#

				// Step 1 DB: Connect to database

				$PDO = dbConnect();	

				// Step 2 + 3 DB

				$allCategoryObjectsArray = Category::fetchAllFromDB($PDO);

				// Step 4 DB: close DB connection
				dbClose($PDO);


#***************************************************************************************#

			
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				#********** PREVIEW GET ARRAY ***************#

				debugArray('_GET', $_GET);
				
				// Step 1 URL: Check whether the parameters have been sent

				if( isset($_GET['action']) === true ) {

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
					

					#********** FILTER BY CATEGORY **********#					
					} elseif( $action === 'filterByCategory' ) {
						debugProcessStart("The blog posts are being filtered by category...");		
																		
						#********** FETCH SECOND URL PARAMETER **********#
						if( isset($_GET['catID']) === true ) {

							debugProcessStart("URL-parameter 'catID' has been committed.");

							// Read, sanitize and output URL data
							debugProcessStart('The URL parameters are being read and sanitized...');
							
							// $catLabel = NULL, $catID = NULL
							$categoryFilterObject = new Category(catID:$_GET['catID']);

							debugObject('categoryFilterObject', $categoryFilterObject);
												
							#****** WHITELISTING: CHECK WHETHER CATEGORY ID EXISTS IN DB *********#
					
							if( array_key_exists($categoryFilterObject->getCatID(), $allCategoryObjectsArray) === false ) {
								// error
								debugError('This category does not exist in the database.');

								$categoryFilterObject = NULL;

								header("Location: index.php");

							} else {
								// success
								debugSuccess('This category exists in the database.');
					
								$filterID = $categoryFilterObject->getCatID();
							}
							
						} // FETCH SECOND URL PARAMETER END

					} // BRANCHING END

				} // PROCESS URL PARAMETERS END


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
				$blogObjectsArray = Blog::fetchFromDB($PDO, $filterID);

				// Step 4 DB: disconnect from DB
				dbClose($PDO);

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
                    <input type="hidden" name="formLogin">

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

				<?php if( empty($blogObjectsArray) === true ): ?>
					<p>No blog posts have been written yet. Get creative!</p>
				
				<?php else: ?>

					<!-- -------- Generate blog articles ---------- -->
					<?php foreach( $blogObjectsArray AS $blogObject): ?>

						<!-- Convert ISO time from DB to EU time and split into date and time -->
						<?php $dateArray = isoToUSDateTime( $blogObject->getBlogDate() ) ?>

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

					<?php endforeach ?>
				<?php endif ?>
            </div>
            <!-- ------------- BLOG END ------------------------------------ -->


            <!-- ------------- CATEGORIES BEGIN ---------------------------- -->

            <div class="categories">
                <div class="blog-title">Categories</div>

				<?php if( empty($allCategoryObjectsArray) === true ): ?>
					<p>There are no categories yet. Go ahead and create some. :&#41;</p>
			
				<?php else: ?>
					<?php foreach( $allCategoryObjectsArray AS $categoryObject ): ?>
						<a href="?action=filterByCategory&catID=<?= $categoryObject->getCatID() ?>" 
						<?php if( $categoryObject->getCatID() == $filterID ) echo 'class="active"' ?>>
							<?= $categoryObject->getCatLabel() ?></a>
					<?php endforeach ?>
				<?php endif ?>
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

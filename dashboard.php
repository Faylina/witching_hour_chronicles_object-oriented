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
				$errorCatLabel			= NULL;
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

					} // BRANCHING END
					
				} // PROCESS URL PARAMETERS END

#***************************************************************************************#				
?>

<!DOCTYPE html>
<html lang="de">

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
                    <a class="button" onclick="document.getElementsByTagName('popupBox')[0].style.display = 'none'">Okay</a>
                <?php elseif( $alert ): ?>
                    <a class="button" href="?action=cancelDelete">Cancel</a>
                    <a class="button" href="?action=delete">Delete Post</a>
                <?php elseif( $dbDeleteError OR $dbDeleteSuccess ): ?>
                    <a class="button" href="?action=okay">Okay</a>
                <?php endif ?>
            </popupBox> 
        <?php endif ?>

        <!-- ------------- USER MESSAGE END ------------------------------------ -->


        <!-- ------------- MAIN CONTENT BEGIN ---------------------------------- -->

        <div class="forms">

            
            <?php if( $showView === true ): ?>

                <!-- ------------- BLOG POST BEGIN ---------------------------------- -->

                <div class="blog">

                    <!-- -------- Generate blog articles ---------- -->
                    <?php foreach( $blogArray AS $value): ?>

                        <?php if( $value['blogID'] == $chosenBlog ): ?>

                            <!-- Convert ISO time from DB to EU time and split into date and time -->
                            <?php $dateArray = isoToEuDateTime( $value['blogDate'] ) ?>

                            <!-- Link to create new post -->
                            <a href="dashboard.php"><< Write a new blog post</a>

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

                        <?php endif ?>

                    <?php endforeach ?>
                </div>
                <!-- ------------- BLOG POST END ------------------------------------ -->
                        

            <?php elseif( $showEdit === true ): ?>

                <div class="article-form">  

                    <?php if( $chosenBlog !== NULL ): ?>

                        <!--------------- Edit form loaded for the first time ----------------->

                        <?php foreach( $blogArray AS $value): ?>

                            <?php if( $value['blogID'] == $chosenBlog ): ?>

                                <!-- ------------- EDIT FORM BEGIN ------------------------- -->

                                <form action="" class="edit-form" method="POST" enctype="multipart/form-data">

                                    <!-- Link to create new post -->
                                    <a href="dashboard.php"><< Write a new blog post</a>
                                    <br>
                                    <div class="form-heading">Edit blog post</div>
                                    <br>
                                    <input type="hidden" name="editForm">
                                    <input type="hidden" name="b12" value="<?= $value['blogID'] ?>">
                                    <input type="hidden" name="b13" value="<?= $value['blogImagePath']?>">

                                    <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                                    <!-- ------------- Category ------------- -->
                                    <label for="b8">Choose a category</label>
                                    <select name="b8" id="b8" class="form-text">
                                        <?= $value['catID'] ?>
                                        <?php foreach( $categoryArray AS $dbCategory ): ?>
                                            <option value="<?= $dbCategory['catID'] ?>" <?php if($dbCategory['catID'] == $value['catID']) echo 'selected'?>>
                                                <?= $dbCategory['catLabel'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>

                                    <br>
                                    <!-- ------------- Title ---------------- -->
                                    <label for="b9">Write the title of your post</label>
                                    <div class="error"><?= $errorTitle ?></div>
                                    <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $value['blogHeadline'] ?>">

                                    <br>
                                    <!-- ------------- Image Upload ---------- -->
                                    <fieldset>
                                        <legend>Upload an image</legend>

                                        <!-- ------------- Database Image ---------- -->

                                        <?php if( $value['blogImagePath'] !== NULL ): ?>
                                            <img class="left" src="<?= $value['blogImagePath']?>" alt="image for the blog article">
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
                                        <div class="error"><?= $errorImage ?></div>
                                        <input type="file" name="image">
                                        <br>
                                        <br>
                                        <!-- ------------- Image Alignment ---------- -->
                                        <label for="b10">Choose the alignment of the image</label>
                                        <br>
                                        <select name="b10" id="b10" class="form-select">
                                            <option value="left" <?php if( $value['blogImageAlignment'] === 'left') echo 'selected' ?>>Left</option>
                                            <option value="right" <?php if( $value['blogImageAlignment'] === 'right') echo 'selected' ?>>Right</option>
                                        </select>
                                        <br>
                                    </fieldset>
                                    <br>

                                    <!-- ------------- Content ------------------ -->
                                    <label for="b11">Write your blog post</label>
                                    <div class="error"><?= $errorContent ?></div>
                                    <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $value['blogContent'] ?></textarea>
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
                            <input type="hidden" name="b12" value="<?= $editedBlogID ?>">
                            <input type="hidden" name="b13" value="<?= $editedImagePath ?>">

                            <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                            <!-- ------------- Category ------------- -->
                            <label for="b8">Choose a category</label>
                            <select name="b8" id="b8" class="form-text">
                                <?= $editedCategory ?>
                                <?php foreach( $categoryArray AS $dbCategory ): ?>
                                    <option value="<?= $dbCategory['catID'] ?>" <?php if($dbCategory['catID'] == $editedCategory) echo 'selected'?>>
                                        <?= $dbCategory['catLabel'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>

                            <br>
                            <!-- ------------- Title ---------------- -->
                            <label for="b9">Write the title of your post</label>
                            <div class="error"><?= $errorTitle ?></div>
                            <input type="text" class="form-text" name="b9" id="b9" placeholder="Title" value="<?= $editedTitle ?>">

                            <br>
                            <!-- ------------- Image Upload ---------- -->
                            <fieldset>
                                <legend>Upload an image</legend>

                                <!-- ------------- Database Image ---------- -->

                                <?php if( $editedImagePath !== NULL ): ?>
                                    <img class="left" src="<?= $editedImagePath ?>" alt="image for the blog article">
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
                                <div class="error"><?= $errorImage ?></div>
                                <input type="file" name="image">
                                <br>
                                <br>
                                <!-- ------------- Image Alignment ---------- -->
                                <label for="b10">Choose the alignment of the image</label>
                                <br>
                                <select name="b10" id="b10" class="form-select">
                                    <option value="left" <?php if( $editedAlignment === 'left') echo 'selected' ?>>Left</option>
                                    <option value="right" <?php if( $editedAlignment === 'right') echo 'selected' ?>>Right</option>
                                </select>
                                <br>
                            </fieldset>
                            <br>

                            <!-- ------------- Content ------------------ -->
                            <label for="b11">Write your blog post</label>
                            <div class="error"><?= $errorContent ?></div>
                            <textarea name="b11" id="b11" class="textarea" cols="30" rows="25"><?= $editedContent ?></textarea>
                            <br>
                            <input type="submit" class="form-button" value="Publish">
                            </form>
                            <!-- ------------- EDIT FORM END ---------------------------- -->

                    <?php endif ?>
                </div>

            <?php else: ?>

                <!-- ------------- BLOG POST FORM BEGIN ------------------------- -->

                <form class="article-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-heading">Write a new blog post</div>
                    <br>
                    <input type="hidden" name="articleForm">

                    <!-- security by obscurity: field names are deliberately chosen to be obscure -->

                    <!-- ------------- Category ------------- -->
                    <label for="b1">Choose a category</label>
                    <select name="b1" id="b1" class="form-text">
                        <?php foreach( $categoryArray AS $value ): ?>
                            <option value="<?= $value['catID'] ?>" <?php if($value['catID'] == $category) echo 'selected'?>>
                                <?= $value['catLabel'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <br>
                    <!-- ------------- Title ---------------- -->
                    <label for="b2">Write the title of your post</label>
                    <div class="error"><?= $errorTitle ?></div>
                    <input type="text" class="form-text" name="b2" id="b2" placeholder="Title" value="<?= $title ?>">

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
                        <div class="error"><?= $errorImage ?></div>
                        <input type="file" name="image">
                        <br>
                        <br>
                        <!-- ------------- Image Alignment ---------- -->
                        <label for="b3">Choose the alignment of the image</label>
                        <br>
                        <select name="b3" id="b3" class="form-select">
                            <option value="left" <?php if( $alignment === 'left') echo 'selected' ?>>Left</option>
                            <option value="right" <?php if( $alignment === 'right') echo 'selected' ?>>Right</option>
                        </select>
                        <br>
                    </fieldset>
                    <br>

                    <!-- ------------- Content ------------------ -->
                    <label for="b4">Write your blog post</label>
                    <div class="error"><?= $errorContent ?></div>
                    <textarea name="b4" id="b4" class="textarea" cols="30" rows="25"><?= $content ?></textarea>
                    <br>
                    <input type="submit" class="form-button" value="Publish">
                </form>
                    
                <!-- ------------- BLOG POST FORM END ---------------------------- -->

            <?php endif ?>

            <div class="mini-forms">
                <!-- ------------- CATEGORY FORM BEGIN ------------------------- -->

                <form class="category-form" action="" method="POST">

                    <div class="form-heading">Create a new category</div>
                    
                    <input type="hidden" name="categoryForm">
                    <br>
                    <label for="b5">Name the new category</label>
                    <div class="error"><?= $errorCategory ?></div>
                    <input type="text" class="form-text" name="b5" id="b5" placeholder="Category name" value="<?= $newCategory ?>">
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
                        <?php foreach( $blogArray AS $value ): ?>
                            <option value="<?= $value['blogID'] ?>">
                                <?= $value['blogHeadline'] ?>
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
                    <li>Faylina 2024</li>
                </ul>
            </div>
        </footer>
        <!-- ------------- FOOTER END ---------------------------------- -->
    
    </body>
</html>
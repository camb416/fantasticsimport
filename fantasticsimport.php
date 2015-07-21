<?php
/*
Plugin Name: Fantasticsimport
Version: 0.1-alpha
Description: Shim for manually entering fantasticsmag stories
Author: Cameron Browning
Author URI: http://cameronbrowning.com
*/
add_action('admin_menu', 'fantasticsimport_setup_menu');

function fantasticsimport_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Fantasticsimport', 'manage_options', 'fantasticsimport', 'test_init' );
}

function test_init(){
  test_handle_post();
?>
<!-- test from tutorial -->
    <!--h1>Hello World!</h1>
    <h2>Upload a File</h2>
    <!-- Form to handle the upload - The enctype value here is very important -->
    <!--form  method="post" enctype="multipart/form-data">
            <input type='file' id='test_upload_pdf' name='test_upload_pdf'></input>
            <?php submit_button('Upload') ?>
    </form -->
<h1>Upload a Fantasticsmag Story</h1>
    <form class="fanimp"  method="post">
      Story Post ID: <input type="Text" name="storyid" value=""><br>
      Story Title: <input type="Text" name="fileurl" value=""><br>
      Image URLs: <input type="Text" name="imgs" value=""><br>
      Featured Fashions: <input type="Text" name="fashions" value=""><br>
      People: <input type="Text" name="people" value=""><br>
      Editorial Tags: <input type="Text" name="name" value=""><br>
      Body: <textarea name="body" rows="8" cols="50"></textarea><br>
      Description: <textarea name="description" rows="8" cols="50" ></textarea><br>
      Sidebar: <input type="Text" name="sidebar" value=""><br>
      Pages: <input type="Text" name="pages" value=""><br>
      alias:  <input type="Text" name="alias" value=""><br>
      Publish Status: <input type="checkbox" name="ispublished" value=""><br>

      <?php submit_button('Submit Story'); ?>
    </form>

<?php
}

function test_handle_post(){


// this is where we deal with teh aftermath of actually
// submitting some data.

//  echo "say something!";
// lets list things out so we can see what's up
// shorthand for the post var
$s = $_POST;

echo  $s['storyid'] .
      $s['fileurl'].
      $s['imgs'].
      $s['fashions'].
      $s['people'].
      $s['name'].
      $s['body'].
      $s['description'].
      $s['sidebar'].
      $s['pages'].
      $s['alias'];

      // checkbox resturns empty string for checked
      if (isset($s['ispublished'])){
          echo "ITS PUBLISHED";
      } else { // or doesn't show up if not checked.
        echo "ITS REALLY NOT";
      }

    // First check if the file appears on the _FILES array
    if(isset($_FILES['test_upload_pdf'])){
            $pdf = $_FILES['test_upload_pdf'];

            // Use the wordpress function to upload
            // test_upload_pdf corresponds to the position in the $_FILES array
            // 0 means the content is not associated with any other posts
            $uploaded=media_handle_upload('test_upload_pdf', 0);
            // Error checking using WP functions
            if(is_wp_error($uploaded)){
                    echo "Error uploading file: " . $uploaded->get_error_message();
            }else{
                    echo "File upload successful!";
            }
    } else {
      echo "well i guess its not set";
    }
}

?>

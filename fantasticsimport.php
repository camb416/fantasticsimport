<?php
/*
Plugin Name: Fantasticsimport
Version: 0.1-alpha
Description: Shim for manually entering fantasticsmag stories
Author: Cameron Browning
Author URI: http://cameronbrowning.com
*/
add_action('admin_menu', 'fantasticsimport_setup_menu');

$nodes = array();


function fantasticsimport_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Fantasticsimport', 'manage_options', 'fantasticsimport', 'test_init' );
}


function   get_data_from_file($_filename){
global $nodes;
echo "load the file: " . $_filename . ".inc";

     $file = file_get_contents('http://localhost/fmagwp/wp-content/plugins/fantasticsimport/exports/2007/'.$_filename.'.inc');

    eval("\$nodes = $file;");
//$b = serialize($myArr);
//var_dump($nodes[0]["nid"]);

}


function render_first_form(){
?>
<h1>First Form</h1>
<form class="fanimp" method="post">
  Post ID: <input type="Text" name="storyid" value=""><br>
<?php submit_button('Submit Story'); ?>
</form>
<?php
}
function render_second_form(){
  global $nodes;
  $node = $nodes[0];
    //var_dump($mydata[0]['nid']);

    $pages_csv = "";

    for($i=0;$i<count($node['field_page']);$i++){
        $thispage = $node['field_page'][$i];
        $prefix = "";
        if(0!==$i){
            $prefix = "\n";
        }
        $pages_csv .= $prefix. "http://fantasticsmag.com/" . $thispage['filepath'];
        // echo("http://fantasticsmag.com/".$thispage['filepath'].",");
    }

    //featured fashions
    $fashion_csv = $node['taxonomy']['tags']['1'];
    $editorial_tags = $node['taxonomy']['tags']['4'];



  ?>

  <form class="fanimp"  method="post">
      <table>
          <th></th>


            <tr><td>Story Post ID: </td><td><input type="Text" name="storyid" value="<?=$node['nid']?>"></td></tr>
            <tr><td>Story Title: </td><td><input type="Text" name="fileurl" value="<?=$node['title']?>"></td></tr>
            <!--tr><td>Image URLs: </td><td><input type="Text" name="imgs" value="<?=$node['nid']?>"></td></tr-->
            <tr><td>Featured Fashions: </td><td><input type="Text" name="fashions" value="<?=$fashion_csv?>"></td></tr>
            <tr><td>People: </td><td><input type="Text" name="people" value="<?=$node['nid']?>"></td></tr>
            <tr><td>Editorial Tags: </td><td><input type="Text" name="name" value="<?=$editorial_tags?>"></td></tr>
            <tr><td>Body: </td><td><textarea name="body" rows="8" cols="64"><?=$node['body']?></textarea></td></tr>
            <tr><td>Description: </td><td><textarea name="description" rows="8" cols="64" ><?=$node['field_description'][0]['value']?></textarea></td></tr>
            <tr><td>Sidebar: </td><td><textarea rows="8" cols="64" name="sidebar" value=""><?=$node['field_sidebar'][0]['value']?></textarea></td></tr>
            <tr><td>Pages: </td><td><textarea rows="8" cols="64" name="pages" value=""><?=$pages_csv?></textarea></td></tr>
            <tr><td>alias:  </td><td><input type="Text" name="alias" value="<?=$node['path']?>"></td></tr>

            <?php // TODO: dont think this is working... ?>
            <tr><td>Publish Status: </td><td><input type="checkbox" name="ispublished" <?php if($node['status'] == '1') echo('checked'); ?></td></tr>

          <tr><td><?php submit_button('Submit Story'); ?></td></tr>
      </table>
  </form>
  <?php
    echo "ok.";
}

function test_init(){

?>
<h1>Upload a Fantasticsmag Story</h1>
<?php

//////////////////////////////////////
// just for testing...
//////////////////////////////////////

get_data_from_file("5198");
render_second_form();

/*
//////////////////////////////////////
// actual code below...
//////////////////////////////////////
//print_r($_GET);  // for all GET variables
//print_r($_POST); // for all POST variables
$s = $_POST;
if(empty($s['storyid'])){
  echo "it's empty... render the first form";
  render_first_form();
} else {
    echo "it's not empty... render the second form";
  get_data_from_file($s['storyid']);
  render_second_form();

}


  test_handle_post();
///////////////////
*/

?>
<!-- test from tutorial -->
    <!--h1>Hello World!</h1>
    <h2>Upload a File</h2>
    <!-- Form to handle the upload - The enctype value here is very important -->
    <!--form  method="post" enctype="multipart/form-data">
            <input type='file' id='test_upload_pdf' name='test_upload_pdf'></input>
            <?php submit_button('Upload') ?>
    </form -->







<!--

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
  -->

<?php
}

function test_handle_post(){


// this is where we deal with teh aftermath of actually
// submitting some data.

//  echo "say something!";
// lets list things out so we can see what's up
// shorthand for the post var
$s = $_POST;
/*
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
*/
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

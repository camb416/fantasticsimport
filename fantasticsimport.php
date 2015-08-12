<?php
/*
Plugin Name: Fantasticsimport
Version: 0.1-alpha
Description: Shim for manually entering fantasticsmag stories
Author: Cameron Browning
Author URI: http://cameronbrowning.com
*/


if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once( 'wp-cli.php' );
}

add_action('admin_menu', 'fantasticsimport_setup_menu');

$nodes = array();


function countTheNodes($ns){
    return count($ns);
}


function fantasticsimport_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Fantasticsimport', 'manage_options', 'fantasticsimport', 'test_init' );
}


function   get_data_from_file($_filename){
global $nodes;
echo "load the file: " . $_filename . ".inc";

     $file = file_get_contents('http://fmag.dev/wp-content/plugins/fantasticsimport/exports/2007/'.$_filename.'.inc');
//var_dump($file);
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


            <tr><td>Story Post ID: </td><td><input type="Text" name="postid" value="<?=$node['nid']?>"></td></tr>
            <tr><td>Story Title: </td><td><input type="Text" name="title" value="<?=$node['title']?>"></td></tr>
          <tr><td>Post Date: </td><td><input type="Text" name="created" value="<?=$node['created']?>"></td></tr>
            <!--tr><td>Image URLs: </td><td><input type="Text" name="imgs" value="<?=$node['nid']?>"></td></tr-->
            <tr><td>Featured Fashions: </td><td><input type="Text" name="fashions_csv" value="<?=$fashion_csv?>"></td></tr>
            <tr><td>People: </td><td><input type="Text" name="people_csv" value=""></td></tr>
            <tr><td>Editorial Tags: </td><td><input type="Text" name="tags_csv" value="<?=$editorial_tags?>"></td></tr>
            <tr><td>Body: </td><td><textarea name="body" rows="8" cols="64"><?=$node['body']?></textarea></td></tr>
            <tr><td>Description: </td><td><textarea name="description" rows="8" cols="64" ><?=$node['field_description'][0]['value']?></textarea></td></tr>
            <tr><td>Sidebar: </td><td><textarea rows="8" cols="64" name="sidebar" value=""><?=$node['field_sidebar'][0]['value']?></textarea></td></tr>
            <tr><td>Pages: </td><td><textarea rows="8" cols="64" name="pages" value=""><?=$pages_csv?></textarea></td></tr>
            <tr><td>alias:  </td><td><input type="Text" name="alias" value="<?=$node['path']?>"></td></tr>

            <?php // TODO: dont think this is working... ?>
            <tr><td>Publish Status: </td><td><input type="checkbox" name="ispublished" <?php if($node['status'] == '1') echo('checked'); ?>></td></tr>

          <tr><td><?php submit_button('Submit Story'); ?></td></tr>
      </table>
  </form>
  <?php
    echo "ok.";
}

function createPostPost($nodeArray){
    $node = $nodeArray[0];

    $pages_csv = "";

    for($i=0;$i<count($node['field_page']);$i++){

        $thispage = $node['field_page'][$i];
        print_r($thispage);
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

    $obj = array();
    $obj['postid'] = $node['nid'];
    $obj['title'] = $node['title'];
    $obj['created'] = $node['created'];
    $obj['fashions_csv'] = $fashion_csv;
    $obj['people_csv'] = $node['people_csv'];
    $obj['tags_csv'] = $editorial_tags;
    $obj['body'] = $node['body'];
    $obj['description'] = $node['field_description'][0]['value'];
    $obj['sidebar'] = $node['field_sidebar'][0]['value'];
    $obj['pages'] = $pages_csv;
    $obj['alias'] = $node['path'];

    if($node['status'] == '1'){
        $obj['ispublished'] = '1';
    }

    print_r($obj);
}


function process_the_post($s){
  //$s is the post variable
  var_dump($s);

$poststatus = 'private';
if($s['ispublished'] === 'on'){
  $poststatus = 'publish';
}

$post = array(

  'post_content'   => $s['body'].'<!-- more -->'.$s['description'],
  'post_name'      => $s['alias'],
  'post_title'     => $s['title'],
  'post_status'    => $poststatus,
  'post_type'      => 'fmag_story',
  'ping_status'    => 'closed',
  'post_date'      => date( "Y-m-d H:i:s" ,intval($s['created'])),
  'comment_status' => 'closed',
  );



 $err = wp_insert_post($post,true);
if($err){
  var_dump($err);
}
    if(is_int($err)){
        if($err>0){


            //// lets do the attachments now
            // The ID of the post this attachment is for.
            $parent_post_id = $err;
            
         $returnVal =  wp_set_object_terms( $err, str_getcsv ($s['fashions_csv'],',' ), "fashion" );
        $returnVal2 =  wp_set_object_terms( $err, str_getcsv ($s['people_csv'],',' ), "person" );
        $returnVal3 =  wp_set_object_terms( $err, str_getcsv ($s['tags_csv'],',' ), "term" );
         
         
         
$thisPost = get_post($err);
 $taxonomy_names = get_object_taxonomies( 'fmag_story' );
   print_r( $taxonomy_names);
var_dump($returnVal);
         
            $pagesArray = explode("\n",$s['pages']);

            for($i = 0; $i<count($pagesArray);$i++){


                echo ("PAGE ".$i . " of " . count($pagesArray)."<br />");
                flush();

          $url =  $snip = str_replace("\r", '', $pagesArray[$i]); // remove carriage returns;
echo "URL: ".$url;

            // let's sideload it...

            $tmp = download_url( $url );
            if( is_wp_error( $tmp ) ){
                // download failed, handle error
                var_dump($tmp);
            }
            $post_id = $parent_post_id;
            $desc = "";
            $file_array = array();

            // Set variables for storage
            // fix file filename for query strings
            preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
            $file_array['name'] = basename($matches[0]);
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if ( is_wp_error( $tmp ) ) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }

            // do the validation and storage stuff
            $id = media_handle_sideload( $file_array, $post_id, $desc );

            // If error storing permanently, unlink
            if ( is_wp_error($id) ) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            $src = wp_get_attachment_url( $id );

            }



        }
    }




}

/**
 * Use Gallery Metabox
 */
function be_gallery_metabox_page_and_rotator( $post_types ) {
    return array( 'fmag_story' );
}
add_action( 'be_gallery_metabox_post_types', 'be_gallery_metabox_page_and_rotator' );


/**
 * Meta box for edit Fmag Story

add_action( 'add_meta_boxes', 'attached_images_meta' );

function attached_images_meta() {
    $screens = array( 'fmag_story', 'post', 'page' ); //add more in here as you see fit
    foreach ($screens as $screen) {
        add_meta_box(
            'attached_images_meta_box', //this is the id of the box
            'Attached Images', //this is the title
            'attached_images_meta_box', //the callback
            $screen, //the post type
            'side' //the placement
        );
    }
}
function attached_images_meta_box($post){
    $args = array('post_type'=>'attachment','post_parent'=>$post->ID);
    $count = count(get_children($args));
    echo '<a href="#" class="button insert-media add_media" data-editor="content">'.$count.' Images</a>';
}
*/
function test_init(){

?>
<h1>Upload a Fantasticsmag Story</h1>
<?php

//////////////////////////////////////
// just for testing...
//////////////////////////////////////

//get_data_from_file("5198");
//render_second_form();


//////////////////////////////////////
// actual code below...
//////////////////////////////////////
//print_r($_GET);  // for all GET variables
//print_r($_POST); // for all POST variables
$s = $_POST;
echo "test";
if(empty($s['storyid']) && empty($s['postid']) ){
  echo "it's empty... render the first form";
  render_first_form();
} else if(!empty($s['postid'])){
  process_the_post($s);

echo "third form please";

} else if(!empty($s['storyid'])){
  echo "it's not empty... render the second form";
  get_data_from_file($s['storyid']);
  render_second_form();

}


  test_handle_post();
///////////////////


?>


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

add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'fmag_story',
array(
  'labels' => array(
    'name' => __( 'Stories' ),
    'singular_name' => __( 'Story' )
    ),
    'public' => true,
    'has_archive' => true,
    'rewrite' => array('slug' => 'stories',
        'with_front' => false),
    )
  );
}

 
function people_init(){

// Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
    'name'                       => _x( 'People', 'taxonomy general name' ),
    'singular_name'              => _x( 'Person', 'taxonomy singular name' ),
    'search_items'               => __( 'Search People' ),
    'popular_items'              => __( 'Popular People' ),
    'all_items'                  => __( 'All People' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Person' ),
    'update_item'                => __( 'Update Person' ),
    'add_new_item'               => __( 'Add New Person' ),
    'new_item_name'              => __( 'New Person Name' ),
    'separate_items_with_commas' => __( 'Separate people with commas' ),
    'add_or_remove_items'        => __( 'Add or remove people' ),
    'choose_from_most_used'      => __( 'Choose from the most used people' ),
    'not_found'                  => __( 'No people found.' ),
    'menu_name'                  => __( 'People' ),
  );

  $args = array(
    'hierarchival' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => 'update_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'people' ),
    );

  register_taxonomy('person', 'fmag_story', $args);
}
add_action( 'init', 'people_init');

function fashions_init(){

// Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
    'name'                       => _x( 'Fashions', 'taxonomy general name' ),
    'singular_name'              => _x( 'Fashion Brand', 'taxonomy singular name' ),
    'search_items'               => __( 'Search Fashion Brands' ),
    'popular_items'              => __( 'Popular Fashion Brands' ),
    'all_items'                  => __( 'All Fashion Brands' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Fashion Brand' ),
    'update_item'                => __( 'Update Fashion Brand' ),
    'add_new_item'               => __( 'Add New Fashion Brand' ),
    'new_item_name'              => __( 'New Fashion Brand Name' ),
    'separate_items_with_commas' => __( 'Separate brands with commas' ),
    'add_or_remove_items'        => __( 'Add or remove brands' ),
    'choose_from_most_used'      => __( 'Choose from the most used fashions' ),
    'not_found'                  => __( 'No fashions found.' ),
    'menu_name'                  => __( 'Fashions' ),
  );

  $args = array(
    'hierarchival' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => 'update_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'fashions' ),
    );

  register_taxonomy('fashion', 'fmag_story', $args);
}
add_action( 'init', 'fashions_init');

function editorial_terms_init(){

// Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
    'name'                       => _x( 'Editorial Terms', 'taxonomy general name' ),
    'singular_name'              => _x( 'Editorial Term', 'taxonomy singular name' ),
    'search_items'               => __( 'Search Editorial Terms' ),
    'popular_items'              => __( 'Popular Editorial Terms' ),
    'all_items'                  => __( 'All Editorial Terms' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Editorial Term' ),
    'update_item'                => __( 'Update Editorial Term' ),
    'add_new_item'               => __( 'Add New Editorial Term' ),
    'new_item_name'              => __( 'New Editorial Term' ),
    'separate_items_with_commas' => __( 'Separate terms with commas' ),
    'add_or_remove_items'        => __( 'Add or remove terms' ),
    'choose_from_most_used'      => __( 'Choose from the most used terms' ),
    'not_found'                  => __( 'No terms found.' ),
    'menu_name'                  => __( 'Editorial Terms' ),
  );

  $args = array(
    'hierarchival' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => 'update_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'terms' ),
    );

  register_taxonomy('term', 'fmag_story', $args);
}
add_action( 'init', 'editorial_terms_init');



// custom credits field
/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function fantasticsimport_add_meta_box() {

  $screens = array( 'fmag_story' );

  foreach ( $screens as $screen ) {

    add_meta_box(
      'fantasticsimport_sectionid',
      __( 'Credits Block', 'fantasticsimport_textdomain' ),
      'fantasticsimport_meta_box_callback',
      $screen
    );
  }
}
add_action( 'add_meta_boxes', 'fantasticsimport_add_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function fantasticsimport_meta_box_callback( $post ) {

  // Add a nonce field so we can check for it later.
  wp_nonce_field( 'fantasticsimport_save_meta_box_data', 'fantasticsimport_meta_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta( $post->ID, '_my_meta_value_key', true );

  /*echo '<label for="fantasticsimport_new_field">';
  _e( 'Credits Block', 'fantasticsimport_textdomain' );
  echo '</label><br />';
  echo '<textarea id="fantasticsimport_new_field" name="fantasticsimport_new_field" rows="16" cols="64">' . esc_attr( $value ) . '</textarea>';
*/
  //so, dont ned to use esc_attr in front of get_post_meta
      $valueeee2=  get_post_meta($_GET['post'], 'fmag_credits_block' , true ) ;
      wp_editor( htmlspecialchars_decode($valueeee2), 'mettaabox_ID_stylee', $settings = array('textarea_name'=>'fmag_credits_block') );


}
function save_my_postdata( $post_id )
{
    if (!empty($_POST['fmag_credits_block']))
        {
        $datta=htmlspecialchars($_POST['fmag_credits_block']);
        update_post_meta($post_id, 'fmag_credits_block', $datta );
        }
}
add_action( 'save_post', 'save_my_postdata' );

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function fantasticsimport_save_meta_box_data( $post_id ) {

  /*
   * We need to verify this came from our screen and with proper authorization,
   * because the save_post action can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['fantasticsimport_meta_box_nonce'] ) ) {
    return;
  }

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['fantasticsimport_meta_box_nonce'], 'fantasticsimport_save_meta_box_data' ) ) {
    return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  // Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }

  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }

  /* OK, it's safe for us to save the data now. */

  // Make sure that it is set.
  if ( ! isset( $_POST['fantasticsimport_new_field'] ) ) {
    return;
  }

  // Sanitize user input.
  $my_data = sanitize_text_field( $_POST['fantasticsimport_new_field'] );

  // Update the meta field in the database.
  update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}

add_action( 'save_post', 'fantasticsimport_save_meta_box_data' );
/*
// attachments stuff
add_filter( 'attachments_settings_screen', '__return_false' ); // disable the Settings screen for Attachments
add_filter( 'attachments_default_instance', '__return_false' ); // disable the default instance

function my_attachments( $attachments )
{
  $fields         = array(
    array(
      'name'      => 'title',                         // unique field name
      'type'      => 'text',                          // registered field type
      'label'     => __( 'Title', 'attachments' ),    // label to display
      'default'   => 'title',                         // default value upon selection
    ),
    array(
      'name'      => 'caption',                       // unique field name
      'type'      => 'textarea',                      // registered field type
      'label'     => __( 'Caption', 'attachments' ),  // label to display
      'default'   => 'caption',                       // default value upon selection
    ),
  );

  $args = array(

    // title of the meta box (string)
    'label'         => 'My Attachments',

    // all post types to utilize (string|array)
    'post_type'     => array( 'fmag_story' ),

    // meta box position (string) (normal, side or advanced)
    'position'      => 'normal',

    // meta box priority (string) (high, default, low, core)
    'priority'      => 'high',

    // allowed file type(s) (array) (image|video|text|audio|application)
    'filetype'      => null,  // no filetype limit

    // include a note within the meta box (string)
    'note'          => 'Attach files here!',

    // by default new Attachments will be appended to the list
    // but you can have then prepend if you set this to false
    'append'        => true,

    // text for 'Attach' button in meta box (string)
    'button_text'   => __( 'Attach Files', 'attachments' ),

    // text for modal 'Attach' button (string)
    'modal_text'    => __( 'Attach', 'attachments' ),

    // which tab should be the default in the modal (string) (browse|upload)
    'router'        => 'browse',

    // whether Attachments should set 'Uploaded to' (if not already set)
    'post_parent'   => false,

    // fields array
    'fields'        => $fields,

  );

  $attachments->register( 'my_attachments', $args ); // unique instance name
}

add_action( 'attachments_register', 'my_attachments' );
*/

function my_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    create_post_type();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'my_rewrite_flush' );
?>

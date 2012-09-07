<?php /*
Plugin Name: Video functionality plugin.
Description: All the functionality needed for the Vimeo/Youtube video post type.
Version: 1.
License: GPL
Author: Josh Wilson
Author URI: http://www.joshuef.com
*/


// include('onFilm_options.php');
include('video_loop.php');

// TO DO:
// Include a class for generation of a video loop. With div class modifiers and thumbnail, title, text selectors



add_action( 'init', 'register_cpt_video' );

function register_cpt_video() {

    $labels = array( 
        'name' => _x( 'Videos', 'video' ),
        'singular_name' => _x( 'Video', 'video' ),
        'add_new' => _x( 'Add New', 'video' ),
        'add_new_item' => _x( 'Add New Video', 'video' ),
        'edit_item' => _x( 'Edit Video', 'video' ),
        'new_item' => _x( 'New Video', 'video' ),
        'view_item' => _x( 'View Video', 'video' ),
        'search_items' => _x( 'Search Videos', 'video' ),
        'not_found' => _x( 'No videos found', 'video' ),
        'not_found_in_trash' => _x( 'No videos found in Trash', 'video' ),
        'parent_item_colon' => _x( 'Parent Video:', 'video' ),
        'menu_name' => _x( 'Videos', 'video' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Something to add to the menu',
        'supports' => array( 'title', 'editor','page-attributes', 'thumbnail', 'custom-fields', 'comments' ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'video', $args );
}


add_filter('pre_get_posts', 'query_post_type');
function query_post_type($query) {
  if(is_category() || is_tag()) {
    $post_type = get_query_var('post_type');
	if($post_type)
	    $post_type = $post_type;
	else
	    $post_type = array('post','video'); // replace cpt to your custom post type
    $query->set('post_type',$post_type);
	return $query;
    }
}






//______________________________________________________________________Custom Meta boxes

// To change: custom_meta, video, $vid_box, display_html

// custom meta boxe
$prefix = 'onfilm_'; // a custom prefix to help us avoid pulling data from the wrong meta box

$vid_box = array(
    'id' => 'video_box', // the id of our meta box
    'title' => 'Video Information', // the title of the meta box
    'page' => 'video', // display this meta box on post editing screens
    'context' => 'normal',
    'priority' => 'high', // keep it near the top
    'fields' => array( // all of the options inside of our meta box
        array(
            'name' => 'Video Link',
            'desc' => 'A link to YouTube or Vimeo, include "http://"',
            'id' => $prefix . 'vid_link',
            'type' => 'text',
            'std' => ''
        ),
        // array(
        //             'name' => 'Additional Details',
        //             'desc' => 'Enter extra post details here',
        //             'id' => $prefix . 'add_details',
        //             'type' => 'textarea',
        //             'std' => ''
        //         ),
        //         array(
        //             'name' => 'Choose a Fruit',
        //             'id' => $prefix . 'fruit',
        //             'type' => 'select',
        //             'options' => array('Apples', 'Oranges', 'Pears', 'Pineapples')
        //         ),
        // 		array(
        // 			'name' => 'Check 1',
        // 			'id' => $prefix . 'check1',
        // 			'type' => 'checkbox',
        // 		),
        // 		array(
        // 			'name' => 'Check 2',
        // 			'id' => $prefix . 'check2',
        // 			'type' => 'checkbox',
        // 		),
        // 		array(
        // 			'name' => 'Check 3',
        // 			'id' => $prefix . 'check3',
        // 			'type' => 'checkbox',
        // 		),
    )
);





// Add meta box to editor
function video_add_box() {
    global $vid_box; // get all of the options from the $vid_box array

    add_meta_box($vid_box['id'], $vid_box['title'], 'display_html', $vid_box['page'], $vid_box['context'], $vid_box['priority']);

}
add_action('admin_menu', 'video_add_box');






// Callback function to show fields in meta box
function display_html() {
    global $vid_box, $post; // get the variables from global $vid_box and $post

    // Use nonce for verification to check that the person has adequate priveleges
    echo '<input type="hidden" name="video_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	// create the table which the options will be displayed in
    echo '<table class="form-table">';

    foreach ($vid_box['fields'] as $field) { // do this for each array inside of the fields array
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        echo '<tr>', // create a table row for each option
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        switch ($field['type']) {

            case 'text': // the HTML to display for type=text options
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '
<br>', $field['desc'];
                break;     

            case 'textarea': // the HTML to display for type=textarea options
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '
<br>', $field['desc'];
                break;

            case 'select': // the HTML to display for type=select options
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
                    echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>';
                break;

            case 'radio': // the HTML to display for type=radio options
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                }
                break;

            case 'checkbox': // the HTML to display for type=checkbox options
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                break;
        }
        echo     '<td>',
            '</tr>';
    }

    echo '</table>';
}




// Save data from meta box
function video_save_data($post_id) {
    global $vid_box;

    // verify nonce -- checks that the user has access
         if (!wp_verify_nonce($_POST['video_box_nonce'], basename(__FILE__))) {
             return $post_id;
         }
      
       // check autosave
              if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                  return $post_id;
              }
           
              // check permissions
              if ('page' == $_POST['post_type']) {
                  if (!current_user_can('edit_page', $post_id)) {
                      return $post_id;
                  }
              } elseif (!current_user_can('edit_post', $post_id)) {
                  return $post_id;
              }

    foreach ($vid_box['fields'] as $field) { // save each option
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];

        if ($new && $new != $old) { // compare changes to existing values
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
	
	// SAVE EXTRA VIDEO INFO
	
	
		$vid_url = get_post_meta( $post_id, 'onfilm_vid_link', true);
	//	echo $vid_url;
		$oembedUrls = array (
					  'www.youtube.com' => 'http://www.youtube.com/oembed?url=$1&format=xml',
					  'www.dailymotion.com' => 'http://www.dailymotion.com/api/oembed?url=$1&format=xml',
					  'www.vimeo.com' => 'http://vimeo.com/api/oembed.xml?url=$1&format=xml',
					  'www.blip.tv' => 'http://blip.tv/oembed/?url=$1&format=xml',
					  'www.hulu.com' => 'http://www.hulu.com/api/oembed?url=$1&format=xml',
					  'www.viddler.com' => 'http://lab.viddler.com/services/oembed/?url=$1&format=xml',
					  'www.qik.com' => 'http://qik.com/api/oembed?url=$1&format=xml',
					  'www.revision3.com' => 'http://revision3.com/api/oembed/?url=$1&format=xml',
					  'www.scribd.com' => 'http://www.scribd.com/services/oembed?url=$1&format=xml',
					  'www.wordpress.tv' => 'http://wordpress.tv/oembed/?url=$1&format=xml',
					  'www.5min.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.collegehumor.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.thedailyshow.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.funnyordie.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.livejournal.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.metacafe.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.xkcd.com' => 'http://www.oohembed.com/oohembed/?url=$1',
					  'www.yfrog.com' => 'http://www.oohembed.com/oohembed/?url=$1'
					);
		// 	
		if (!empty( $vid_url )){
							$parts = parse_url( $vid_url );
							$host = $parts['host'];
							//echo $host . ' ';
							if (empty($host) || !array_key_exists($host,$oembedUrls)){
								//echo 'Unrecognized host'; DO NOTHING
							} else {
								$oembedContents = @file_get_contents(str_replace('$1',$vid_url,$oembedUrls[$host])) ;
							//	$oembedData = @json_decode($oembedContents);
			
					$xml = simplexml_load_string( $oembedContents ); //process the oembed
			
							$vid_thumbnail = (string)$xml->thumbnail_url ; // print the thumb
							
							
							
							$full_embed = html_entity_decode($xml->html);
							
							// $full_embed = preg_replace('/(width|height)="[0-9]*"/g', '$1=""', $pre_embed);
							
							
							
					if ($parts['host'] == 'www.vimeo.com' ) {
							$embed_vid_link = 'http://player.vimeo.com/video/'. $xml->video_id;
						  	$short_host = 'vimeo';
					} else { $short_host = null;}
				
			
					if ($parts['host'] == 'www.youtube.com' )  {
							 $yt_regex = '~v=(.{11})|\/embed\/(.{11})~';
							preg_match( $yt_regex, $vid_url, $matches);			
							$video_id = $matches[1];
			
							$embed_vid_link = 'http://www.youtube.com/embed/' . $video_id . '?fs=1&feature=oembed';
				} // youtube
				
				} //else ie. host compatible
			} //if not emppty vid url
	
	
	
			//______________________________________________________________________ save video detials (thumb + embed)

	
				// keywords are between 
			  
	// Save the host name for conditional tags on thumbs
		$oldhost = get_post_meta($post_id, 'host_name', true);  // get the video thumbnail
		$newhost = $short_host;

			 if ($newhost && $newhost != $oldhost) { // compare changes to existing values
			   		update_post_meta($post_id, 'host_name', $newhost);
				        } elseif ('' == $newhost && $oldhost) {
		 		            delete_post_meta($post_id, 'host_name', $oldhost);
		 		        }

		//save the embed code
	$oldembed = get_post_meta($post_id, 'embed_code', true);  // get the video thumbnail
	$newembed = $full_embed;
		
		 if ($newembed && $newembed != $oldembed) { // compare changes to existing values
		   		update_post_meta($post_id, 'embed_code', $newembed);
			        } elseif ('' == $newembed && $oldembed) {
	 		            delete_post_meta($post_id, 'embed_code', $oldembed);
	 		        }
	
	// Save the thumbnail
	$oldvidpic = get_post_meta($post_id, 'video_thumb', true);  // get the video thumbnail
	$newvidpic = $vid_thumbnail;

		 if ($newvidpic && $newvidpic != $oldvidpic) { // compare changes to existing values
		   		update_post_meta($post_id, 'video_thumb', $newvidpic);
			        } elseif ('' == $newvidpic && $oldvidpic) {
	 		            delete_post_meta($post_id, 'video_thumb', $oldvidpic);
	 		        }

		
		// save the embed link
		$oldvid = get_post_meta($post_id, 'embed_link', true); // get the video embed link
		$newvid = $embed_vid_link;
		
			 if ($newvid && $newvid != $oldvid) { // compare changes to existing values
			            update_post_meta($post_id, 'embed_link', $newvid);
			        } elseif ('' == $newvid && $oldvid) {
			            delete_post_meta($post_id, 'embed_link', $oldvid);
			        }
}
add_action('save_post', 'video_save_data'); // save the data



// 

// Add the ability to use post thumbnails if it isn't already enabled.
// Not required. Use only of you want to have more than the large,
// medium or thumbnail options WP uses by default.

if ( function_exists( 'add_image_size' ) ) add_theme_support( 'post-thumbnails' );

// Add custom thumbnail sizes to your theme. These sizes will be auto-generated
// by the media manager when adding images to it on a new post.
if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 't1x1', 145, 200, true );
	add_image_size( 't2x1', 307, 200, true );
	add_image_size( 't2x2', 307, 417, true );
}

///////////////////////////////////////////////
//
// Start WPOutfitters.com Custom Galley Function
//
//////////////////////////////////////////////

function wpo_get_images($size = 'thumbnail', $limit = '0', $offset = '0', $big = 'large', $post_id = '$post->ID', $link = '1', $img_class = 'attachment-image', $wrapper = 'div', $wrapper_class = 'attachment-image-wrapper') {
	global $post;

	$images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );

	if ($images) {

		$num_of_images = count($images);

		if ($offset > 0) : $start = $offset--; else : $start = 0; endif;
		if ($limit > 0) : $stop = $limit+$start; else : $stop = $num_of_images; endif;

		$i = 0;
		foreach ($images as $attachment_id => $image) {
			if ($start <= $i and $i < $stop) {
			$img_title = $image->post_title;   // title.
			$img_description = $image->post_content; // description.
			$img_caption = $image->post_excerpt; // caption.
			//$img_page = get_permalink($image->ID); // The link to the attachment page.
			$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
			if ($img_alt == '') {
			$img_alt = $img_title;
			}
				if ($big == 'large') {
				$big_array = image_downsize( $image->ID, $big );
 				$img_url = $big_array[0]; // large.
				} else {
				$img_url = wp_get_attachment_url($image->ID); // url of the full size image.
				}

			// FIXED to account for non-existant thumb sizes.
			$preview_array = image_downsize( $image->ID, $size );
			if ($preview_array[3] != 'true') {
			$preview_array = image_downsize( $image->ID, 'thumbnail' );
 			$img_preview = $preview_array[0]; // thumbnail or medium image to use for preview.
 			$img_width = $preview_array[1];
 			$img_height = $preview_array[2];
			} else {
 			$img_preview = $preview_array[0]; // thumbnail or medium image to use for preview.
 			$img_width = $preview_array[1];
 			$img_height = $preview_array[2];
 			}
 			// End FIXED to account for non-existant thumb sizes.

 			///////////////////////////////////////////////////////////
			// This is where you'd create your custom image/link/whatever tag using the variables above.
			// This is an example of a basic image tag using this method.
			?>
			<?php if ($wrapper != '0') : ?>
			<<?php echo $wrapper; ?> class="<?php echo $wrapper_class; ?>">
			<?php endif; ?>
			<?php if ($link == '1') : ?>
			<a href="<?php echo $img_url; ?>" title="<?php echo $img_title; ?>">
			<?php endif; ?>
			<img class="<?php echo $img_class; ?>" src="<?php echo $img_preview; ?>" alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
			<?php if ($link == '1') : ?>
			</a>
			<?php endif; ?>
			<?php if ($img_caption != '') : ?>
			<div class="attachment-caption"><?php echo $img_caption; ?></div>
			<?php endif; ?>
			<?php if ($img_description != '') : ?>
			<div class="attachment-description"><?php echo $img_description; ?></div>
			<?php endif; ?>
			<?php if ($wrapper != '0') : ?>
			</<?php echo $wrapper; ?>>
			<?php endif; ?>
			<?
			// End custom image tag. Do not edit below here.
			///////////////////////////////////////////////////////////

			}
			$i++;
		}

	}
} 

///////////////////////////////////////////////
//
// End WPOutfitters.com WP Custom Galley Function
//
//////////////////////////////////////////////




//___________________________________________________________________________ datepicker for admin


// Admin date picker enque_________________________________________________


function my_admin_init() {
	$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker', $pluginfolder . '/js/jquery.ui.datepicker.js', array('jquery', 'jquery-ui-core') );
	wp_enqueue_style('jquery.ui.theme', $pluginfolder . '/js/smoothness/jquery-ui-1.8.16.custom.css');
}
add_action('admin_init', 'my_admin_init');


function my_admin_footer() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.mydatepicker').datepicker({
			dateFormat : 'dd-mm-yy'
		});
	});
	</script>
	<?php
}
add_action('admin_footer', 'my_admin_footer');






?>
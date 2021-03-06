<?php
/**
 * @package Publications_Archive  
 * @version 0.1
 */
/*
Plugin Name: Publications Archive 
Plugin URI: http://www.20papercups.net
Description: Maintains a list of publications for a lab or research center.
Author: Michael Marner
Version: 0.1
Author URI: http://www.20papercups.net
*/


// Let's create the custom post type for a publication
add_action('init', 'wclCreatePublicationType');


// Create the metadata boxes for the publication post type
add_action('add_meta_boxes', 'wclPublicationAddMetaBoxes');

// Actually save the custom fields
add_action('save_post', 'wclPublicationSaveMeta');

// Modify the form so we can support file uploads
add_action('admin_footer','wclFixForm');

add_action('admin_print_scripts', 'wclAdminScripts');
add_action('admin_print_styles', 'wclAdminStyles');
add_action('admin_head','wclHideEditor');

function wclAdminScripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', '/wp-content/plugins/publications/functions/my-scripts.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
}

function wclAdminStyles() {
	wp_enqueue_style('thickbox');
}

function wclHideEditor(){
	if (get_post_type()=='wcl_publication'){
		echo ' <style> .postarea{display:none} </style>';
	}
}

/**
 * Just to make things easier, this array stores all the information
 * for the publication meta data box.
 */
$pubBox = array (
	'id' => 'wcl_publication_meta',
	'title' => "Publication Info",
	'page' => 'wcl_publication',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array (
		array(
			'name' => 'Publication Type',
			'desc' => '',
			'id' => 'wcl_type',
			'type' => 'select',
			'options' => array ('Conference Paper', 'Journal Article', 'Book Chapter', 'PhD Thesis', 'Honours Thesis'),
			'std' => '2012'
		),
		array(
			'name' => 'Publication Year',
			'desc' => '',
			'id' => 'wcl_year',
			'type' => 'text',
			'std' => '2012'
		),
		array(
			'name' => 'Proceedings/Journal Name',
			'desc' => 'eg: Proceedings of the 11th International Symposium on Mixed and Augmented Reality',
			'id' => 'wcl_proceedings',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Abstract',
			'desc' => '',
			'id' => 'wcl_abstract',
			'type' => 'textarea',
			'std' => ''
		),
		array(
			'name' => 'Conference/Publisher Location',
			'desc' => '',
			'id' => 'wcl_location',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Video Link',
			'desc' => 'A Youtube link to the video',
			'id' => 'wcl_video',
			'type' => 'text',
			'std' => 'http://www.youtube.com/'
		),
		array(
			'name' => 'PDF',
			'desc' => '',
			'id' => 'wcl_pdf',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => '',
			'desc' => 'Select a PDF',
			'id' => 'wcl_pdf_button',
			'type' => 'button',
			'std' => 'Browse'
		),
//		array(
//			'name' => 'Thumbnail Image',
//			'desc' => '',
//			'id' => 'wcl_image',
//			'type' => 'text',
//			'std' => ''
//		),
//		array(
//			'name' => '',
//			'desc' => 'Select an Image',
//			'id' => 'wcl_image_button',
//			'type' => 'button',
//			'std' => 'Browse'
//		),
		array(
			'name' => 'Author 1',
			'desc' => '',
			'id' => 'wcl_author1',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 2',
			'desc' => '',
			'id' => 'wcl_author2',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 3',
			'desc' => '',
			'id' => 'wcl_author3',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 4',
			'desc' => '',
			'id' => 'wcl_author4',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 5',
			'desc' => '',
			'id' => 'wcl_author5',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 6',
			'desc' => '',
			'id' => 'wcl_author6',
			'type' => 'author',
			'std' => ''
		),
		array(
			'name' => 'Author 7',
			'desc' => 'Use the dropdown menu if the author is a member of the lab, otherwise type their name.',
			'id' => 'wcl_author7',
			'type' => 'author',
			'std' => ''
		),
	)
);


function wclShowPublicationMetaBox()
{
	global $pubBox, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	echo '<table class="form-table">';
	foreach ($pubBox['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		echo '<tr>',
			'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
			'<td>';
		switch ($field['type']) {
		case 'text':
			echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
			break;
		case 'textarea':
			echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
			break;
		case 'select':
			echo '<select name="', $field['id'], '" id="', $field['id'], '">';
			foreach ($field['options'] as $option) {
				echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
			}
			echo '</select>';
			break;
		case 'radio':
			foreach ($field['options'] as $option) {
				echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
			}
			break;
		case 'checkbox':
			echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
			break;
		case 'button':
			echo '<input type="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
			break;

		case 'author':
			echo '<input type="text" style="width: 40%" name="' , $field['id'] , '" id="' , $field['id'] , '" value="', $meta ? $meta : $field['std'] , '" />';
			echo '<select name="' . $field['id'] . '_preset" id="' . $field['id'] . '_preset" onChange="document.getElementById(\'' . $field['id'] . '\').value = options[selectedIndex].text">';
			echo '<option></option>';
			$users = get_users();
			foreach ($users as $user) {
				echo '<option value="' . $user->ID . '"', get_post_meta($post->ID, $field['id'] . '_preset', true) == $user->ID ? ' selected="selected"' : '', '>' , $user->display_name , '</option>';
			}
			echo '</select>';
			if (!empty($field['desc']))
			{
				echo '<br>';
				echo $field['desc'];
			}
			break;
		}
		echo '</td><td>',
			'</td></tr>';
	}
	echo '</table>';
}


/**
 * Registers the Publication post type.
 */
function wclCreatePublicationType() {
	register_post_type('wcl_publication',
		array(
			'labels' => array(
				'name' => __( 'Publications' ),
				'singular_name' => __( 'Publication' ),
				'add_new' => _x('Add New', 'wcl_publication'),
				'add_new_item' => __('Add New Publication')
			),
			'public' => true,
			'rewrite' => array (
			'slug' => 'publications'
			),
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail', 'editor')
		)
	);
 
	/*
	register_taxonomy( 'publication_author', 'wcl_publication', 
		array( 'hierarchical' => false, 
		labels => array(
			'name' => 'Authors', 
			'singular_name' => "Author",
			'add_new_item' => "Add New Author"
		),
		'query_var' => true, 
		'rewrite' => true, 
		'show_tagcloud' => true));
	 */
}


function wclPublicationAddMetaBoxes() {
	global $pubBox;
	add_meta_box($pubBox['id'], $pubBox['title'], 'wclShowPublicationMetaBox', 'wcl_publication');
}

// Save data from meta box
function wclPublicationSaveMeta($post_id) {
	global $pubBox;
	// verify nonce
	if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
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
	foreach ($pubBox['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
		if ($field['type'] == "author")
		{
			$old = get_post_meta($post_id, $field['id'] . "_preset", true);
			$new = $_POST[$field['id'] . "_preset"];
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'] . "_preset", $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'] . "_preset", $old);
			}
		}
	}
}

function wclFixForm(){
	echo  '<script type="text/javascript">
		      jQuery("#post").attr("enctype", "multipart/form-data");
	        </script>
				  ';
}

function wclRenderPublicationList()
{
	$currentYear = -1;
	$output = "";
	$args = array('post_type' => 'wcl_publication', 'meta_key' => 'wcl_year', 'orderby' => 'meta_value_num', 'nopaging' => true);
	$loop = new WP_Query($args);

	while ($loop->have_posts()) {
		$loop->the_post();
		if (get_post_meta(get_the_ID(), 'wcl_year', true) != $currentYear)
		{
			$currentYear = get_post_meta(get_the_ID(), 'wcl_year', true);
			$output .= '<h1>' . $currentYear . '</h1>';
		}

		$output .= '<p>';
		//$image = get_post_meta(get_the_ID(), "wcl_image", true);
		$image = get_the_post_thumbnail(get_the_ID(), array(105,105), array('class' => 'alignleft'));
		if (!empty($image))
		{
			//$output .= '<img width="60" height="60" src="' . $image . '" style="float:left;">';
			$output .= $image;
		}
		for ($i=1; $i<=7; $i++)
		{
			$user = get_users(array('include' => get_post_meta(get_the_ID(), "wcl_author$i_preset", true)));
			$author = get_post_meta(get_the_ID(), "wcl_author$i_preset", true) == "" ? get_post_meta(get_the_ID(), "wcl_author$i", true) : $user[0]->display_name;
			if ($author == "")
				break;
			$output .= $author . ", ";
		}
		$output .= '"' . get_the_title() . '", ';
		
		$proceedings = get_post_meta(get_the_ID(), 'wcl_proceedings', true);
		if (!empty($proceedings)) {
			$output .= 'in <i>' . $proceedings . '</i>, ';
		}
		$output .= get_post_meta(get_the_ID(), 'wcl_location', true);
		$output .= " $currentYear.";
		$output .= '<br>';
		$pdf = get_post_meta(get_the_ID(), "wcl_pdf", true);
		if (!empty($pdf))
		{
			$output .= '<a href="' . $pdf . '">[pdf]</a> ';
		}
		$video = get_post_meta(get_the_ID(), "wcl_video", true);
		if (!empty($video))
		{
			$output .= '<a href="' . $video . '">[video]</a> ';
		}
		//$output .= get_post_meta(get_the_ID(), 'wcl_year', true);
		$output .= "</p>";
		$output .= '<div style="clear:both"></div>';
	}

	return $output;
}

add_shortcode('wcl_publications', 'wclRenderPublicationList');

?>

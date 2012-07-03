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


// Now we set that function up to execute when the admin_notices action is called
//add_action( 'admin_notices', 'hello_dolly' );


add_action('init', 'create_publication_type');
add_action('add_meta_boxes', 'wcl_publication_add_boxes');

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
			'name' => 'Location',
			'desc' => '',
			'id' => 'wcl_location',
			'type' => 'text',
			'std' => ''
		),
		array(
			'name' => 'Video Link',
			'desc' => '',
			'id' => 'wcl_video',
			'type' => 'text',
			'std' => 'http://www.youtube.com/'
		),
	)
);


function show_pub_box()
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
		}
		echo '</td><td>',
			'</td></tr>';
	}
	echo '</table>';
}


/**
 * Registers the Publication post type.
 */
function create_publication_type() {
	register_post_type('wcl_publication',
		array(
			'labels' => array(
				'name' => __( 'Publications' ),
				'singular_name' => __( 'Publication' ),
				'add_new' => _x('Add New', 'wcl_publication'),
				'add_new_item' => __('Add New Publication')
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'thumbnail')
		)
	);

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
}


function wcl_publication_add_boxes() {
	global $pubBox;
	add_meta_box($pubBox['id'], $pubBox['title'], 'show_pub_box', 'wcl_publication');
}

?>

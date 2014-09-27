<?php
/**
 * Define and require all the necessary 'bits and pieces'
 * and build all necessary Static Homepage and Featured area functions.
 *
 * @package Dynamik
 */

/**
 * Call Genesis's core functions.
 */
require_once( get_template_directory() . '/lib/init.php' );

/**
 * Define child theme constants.
 */
define( 'CHILD_THEME_NAME', 'Parivarthan' );
define( 'CHILD_THEME_URL', 'http://iksa.in/' );
define( 'CHILD_THEME_VERSION', '1.0' );

add_filter( 'avatar_defaults', 'child_default_avatar' );
/**
 * Display a Custom Avatar if one exists with the correct name
 * and in the correct images directory.
 *
 * @since 1.0
 * @return custom avatar.
 */
function child_default_avatar( $avatar_defaults )
{
	$custom_avatar_image = '';
	if( file_exists( CHILD_DIR . '/images/custom-avatar.png' ) )
		$custom_avatar_image = CHILD_URL . '/images/custom-avatar.png';
	elseif( file_exists( CHILD_DIR . '/images/custom-avatar.jpg' ) )
		$custom_avatar_image = CHILD_URL . '/images/custom-avatar.jpg';
	elseif( file_exists( CHILD_DIR . '/images/custom-avatar.gif' ) )
		$custom_avatar_image = CHILD_URL . '/images/custom-avatar.gif';
	elseif( file_exists( CHILD_DIR . '/images/custom-avatar.jpg' ) )
		$custom_avatar_image = CHILD_URL . '/images/custom-avatar.jpg';

	$custom_avatar = apply_filters( 'child_custom_avatar_path', $custom_avatar_image );
	$avatar_defaults[$custom_avatar] = 'Custom Avatar';
	
	return $avatar_defaults;
}

add_action( 'genesis_meta', 'child_responsive_viewport' );
/**
 * Add viewport meta tag to the genesis_meta hook
 * to force 'real' scale of site when viewed in mobile devices.
 *
 * @since 1.0
 */
function child_responsive_viewport() {
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>' . "\n";
}

/**
 * Enable Custom Post Format functionality.
 */
add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
add_theme_support( 'genesis-post-format-images' );

/**
 * Add support for Genesis HTML5 Markup.
 */
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

/**
 * Add support for Genesis 'Fancy Dropdowns'.
 */
add_filter( 'genesis_superfish_enabled', '__return_true' );

add_filter( 'genesis_author_box_gravatar_size', 'child_author_box_gravatar_size' );
/**
 * Modify the size of the Gravatar in the author box.
 *
 * @since 1.0
 */
function child_author_box_gravatar_size( $size )
{
	return 160;
}

add_filter( 'genesis_comment_list_args', 'child_comments_gravatar_size' );
/**
 * Modify the size of the Gravatar in comments.
 *
 * @since 1.0
 */
function child_comments_gravatar_size( $args )
{
	$args['avatar_size'] = 96;
	return $args;
}

/**
 * This is altered version of the genesis_get_custom_field() function
 * which includes the additional ability to work with array() values.
 *
 * @since 1.0
 */
function dynamik_get_custom_field( $field, $single = true, $explode = false )
{
	if( null === get_the_ID() )
		return '';

	$custom_field = get_post_meta( get_the_ID(), $field, $single );

	if( !$custom_field )
		return '';

	if( !$single )
	{
		$custom_field_string = implode( ',', $custom_field );
		if( $explode )
		{
			$custom_field_array_pre = explode( ',', $custom_field_string );
			foreach( $custom_field_array_pre as $key => $value )
			{
				$custom_field_array[$value] = $value;
			}
			return $custom_field_array;
		}
		return $custom_field_string;
	}

	return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );
}

/**
 * Create a Dynamik Label conditional tag which
 * allows content to be conditionally placed on pages and posts
 * that have specific Dynamik Labels assigned to them.
 *
 * @since 1.0
 */
function dynamik_has_label( $label = 'label' )
{
	$labels_meta_array = dynamik_get_custom_field( '_dyn_labels', false, true ) != '' ? dynamik_get_custom_field( '_dyn_labels', false, true ) : array();

	if( is_singular() )
	{
		if( in_array( $label, $labels_meta_array ) ) return true;
	}

	return false;
}

/**
 * Create a Genesis Simple Sidebars conditional tag which
 * allows content to be conditionally placed on pages and posts
 * that have specific simple sidebars assigned to them.
 *
 * @since 1.0
 */
function dynamik_is_ss( $sidebar_id = 'sb-id' )
{
	if( !defined( 'SS_SETTINGS_FIELD' ) )
		return false;

	static $taxonomies = null;

	if( is_singular() )
	{
		if( $sidebar_id == genesis_get_custom_field( '_ss_sidebar' ) ) return true;
	}

	if( is_category() )
	{
		$term = get_term( get_query_var( 'cat' ), 'category' );
		if( isset( $term->meta['_ss_sidebar'] ) && $sidebar_id == $term->meta['_ss_sidebar'] ) return true;
	}

	if( is_tag() )
	{
		$term = get_term( get_query_var( 'tag_id' ), 'post_tag' );
		if( isset( $term->meta['_ss_sidebar'] ) && $sidebar_id == $term->meta['_ss_sidebar'] ) return true;
	}

	if( is_tax() )
	{
		if ( null === $taxonomies )
			$taxonomies = ss_get_taxonomies();

		foreach ( $taxonomies as $tax )
		{
			if ( 'post_tag' == $tax || 'category' == $tax )
				continue;

			if ( is_tax( $tax ) )
			{
				$obj = get_queried_object();
				$term = get_term( $obj->term_id, $tax );
				if( isset( $term->meta['_ss_sidebar'] ) && $sidebar_id == $term->meta['_ss_sidebar'] ) return true;
				break;
			}
		}
	}

	return false;
}

/**
 * Enable Shortcodes in Text Widgets.
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Register Custom Widget Areas.
 */

/**
 * Build and Hook-In Custom Widget Areas.
 */

/**
 * Build and Hook-In Custom Hook Boxes.
 */

/**
 * Filter in specific body classes based on option values.
 */
add_filter( 'body_class', 'child_body_classes' );
/**
 * Determine which classes will be filtered into the body class.
 *
 * @since 1.0
 * @return array of all classes to be filtered into the body class.
 */
function child_body_classes( $classes ) {
	if ( is_front_page() ) {
		
		
		
		
		
	}
	
	
	
	
	

	$classes[] = 'override';
	
	return $classes;
}

add_filter( 'post_class', 'child_post_classes' );
/**
 * Create an array of useful post classes.
 *
 * @since 1.0
 * @return an array of child post classes.
 */
function child_post_classes( $classes )
{
	$classes[] = 'override';

	return $classes;
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_google_fonts' );
/**
 * Enqueue Google fonts.
 *
 * @since 1.0
 * @return an enqueue of Google fonts.
 */
function child_enqueue_google_fonts() {
	wp_enqueue_style( 'child_enqueued_google_fonts', '//fonts.googleapis.com/css?family=Lato:300,400|PT+Sans|', array(), CHILD_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_responsive_scripts' );
/**
 * Enqueue Responsive Design javascript code.
 *
 * @since 1.0
 */
function child_enqueue_responsive_scripts() {	
	wp_enqueue_script( 'responsive', CHILD_URL . '/js/responsive.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
}

if ( file_exists( get_stylesheet_directory() . '/lib/functions.php' ) ) {
	require_once( get_stylesheet_directory() . '/lib/functions.php' );
}


	
//* Reposition the Genesis breadcrumb to bottom of page
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_entry_footer', 'genesis_do_breadcrumbs' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );




//end functions.php
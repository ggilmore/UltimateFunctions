<?php 

/*

	WordPress Functions Template
	Version 1.01
	Last Updated November 9, 2011
	Author: Gray Gilmore - designbygray.ca

	Many of the functions below were provided from the wonderful duo of Chris Coyier and Jeff Starr over at Digging Into WordPress digwp.com
	
	Other functions were gathered from hours of Googling and troubleshooting.

*/

/*
	Navigation:
	
	== Custom Post Type
		+ Make Custom Post Types Searchable	
	
	== Post Thumbnail
	
	== Custom Menu Support
	
	== Enable Threaded Comments
	
	== wp_head
		+ Remove junk from head
		+ Add feed links
		
	== wp_footer
		+ Add Google Analytics
		
	== Excerpts
		+ Custom Excerpt Length
		+ Custom Excerpt Ellipses for 2.9+
		+ No more jumping for read more link
		
	== Favicons & Logos
		+ Add favicon
		+ Add Apple icon
		+ Add favicon to admin
		+ Change login screen logo
		
	== Kill the admin nag
	
	== Categories, IDs and Trees
		+ Put category id in body and post class
		+ Get the first category ID
		+ Find the ID (is_tree)
		
	== Widgetize Your Site
		+ Shorcodes in Widgets
	
	== Don't Ping Yourself
	
	== Editor / TinyMCE
		+ Style the editor
		+ Add css style selector dropdown
	
	
	
*/



// add custom_post_type functionality

add_action( 'init', 'create_my_post_types' );

function create_my_post_types() {
	register_post_type( 'adverts',
		array(
			'labels' => array(
				'name' => __( 'Names' ),
				'singular_name' => __( 'Name' ),
				'add_new' => __( 'Add New' ),
				'add_new_item' => __( 'Add New Name' ),
				'edit' => __( 'Edit' ),
				'edit_item' => __( 'Edit Name' ),
				'new_item' => __( 'NewNameName' ),
				'view' => __( 'View Advert' ),
				'view_item' => __( 'View Name' ),
				'search_items' => __( 'Search Names' ),
				'not_found' => __( 'No names found' ),
				'not_found_in_trash' => __( 'No names found in Trash' ),
				'parent' => __( 'Parent Name' ),
			),
			'public' => true,
			'menu_position' => 5, // Change this to order item in left sidebar
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ), // Remove items that you don't want this Custom Post Type to use
		)
	);
}

// make custom post types searchable
function searchAll( $query ) {
 if ( $query->is_search ) { $query->set( 'post_type', array( 'site','plugin', 'theme','person' )); }
 return $query;
}
add_filter( 'the_search_query', 'searchAll' );


// add post-thumbnail functionality
if (function_exists('add_theme_support')) { add_theme_support('post-thumbnails'); }
	set_post_thumbnail_size( 100, 100, true ); // Normal post thumbnails
	add_image_size( 'custom-1', 125, 125, true ); // Custom size that will crop the image to fit the proportion
	add_image_size( 'custom-2', 255, 236, false ); // Default is false, "soft proportional crop"


// add custom menu support
if ( function_exists( 'register_nav_menus' ) ) {
  	register_nav_menus(
  		array(
  		  'main_menu' => 'Main Navigation',
		  'minor_menu' => 'Minor Navigation' // Add more should you desire (Footer, for example)
  		)
  	);
}



// enable threaded comments
function enable_threaded_comments(){
	if (!is_admin()) {
		if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1))
			wp_enqueue_script('comment-reply');
		}
}
add_action('get_header', 'enable_threaded_comments');


// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);



// add feed links to header
if (function_exists('automatic_feed_links')) {
	automatic_feed_links();
} else {
	return;
}


// add google analytics to footer
function add_google_analytics() {
	echo '<script src="http://www.google-analytics.com/ga.js" type="text/javascript"></script>';
	echo '<script type="text/javascript">';
	echo 'var pageTracker = _gat._getTracker("UA-XXXXXXXX-X");';
	echo 'pageTracker._trackPageview();';
	echo '</script>';
}
add_action('wp_footer', 'add_google_analytics');


// custom excerpt length
function custom_excerpt_length($length) {
	return 20;
}
add_filter('excerpt_length', 'custom_excerpt_length');


// custom excerpt ellipses for 2.9+
function custom_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'custom_excerpt_more');


// no more jumping for read more link
function no_more_jumping($post) {
	return '<a href="'.get_permalink($post->ID).'" class="read-more">'.'Continue Reading'.'</a>';
}
add_filter('excerpt_more', 'no_more_jumping');


// add a favicon to your site
function blog_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('wpurl').'/favicon.ico" />';
}
add_action('wp_head', 'blog_favicon');

// add an apple icon to your site
function blog_appleicon() {
	echo '<link rel="apple-touch-icon" href="'.get_bloginfo('wpurl').'/apple-touch-icon.png"/>';
}
add_action('wp_head', 'blog_appleicon');


// add a favicon for your admin
function admin_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/images/favicon.png" />';
}
add_action('admin_head', 'admin_favicon');


// custom admin login logo
function custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url('.get_bloginfo('template_directory').'/images/custom-login-logo.png) !important; }
	</style>';
}
add_action('login_head', 'custom_login_logo');


// kill the admin nag
if (!current_user_can('edit_users')) {
	add_action('init', create_function('$a', "remove_action('init', 'wp_version_check');"), 2);
	add_filter('pre_option_update_core', create_function('$a', "return null;"));
}


// category id in body and post class
function category_id_class($classes) {
	global $post;
	foreach((get_the_category($post->ID)) as $category)
		$classes [] = 'cat-' . $category->cat_ID . '-id';
		return $classes;
}
add_filter('post_class', 'category_id_class');
add_filter('body_class', 'category_id_class');


// get the first category id
function get_first_category_ID() {
	$category = get_the_category();
	return $category[0]->cat_ID;
}


// allows us to target pages in a specific branch
function is_tree($pid) {
	global $post;

	$ancestors = get_post_ancestors($post->$pid);
	$root = count($ancestors) - 1;
	$parent = $ancestors[$root];

	if(is_page() && (is_page($pid) || $post->post_parent == $pid || in_array($pid, $ancestors)))
	{
		return true;
	}
	else
	{
		return false;
	}
};


// widgets!
if ( function_exists('register_sidebar') )
register_sidebar(array(
	'name'=>'Sidebar',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
));
register_sidebar(array( // You can add as many as you like
	'name'=>'Another Sidebar',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
));

// shortcodes in widgets
if ( !is_admin() ){
    add_filter('widget_text', 'do_shortcode', 11);
}


// don't ping yourself
function no_self_ping( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );


// custom editor styles -- create editor-style.css in your theme directory
add_editor_style();

// add style selector drop down 
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

// add styles - more info: http://alisothegeek.com/2011/05/tinymce-styles-dropdown-wordpress-visual-editor/
add_filter( 'tiny_mce_before_init', 'my_mce_before_init' );

function my_mce_before_init( $settings ) {

    $style_formats = array(
    	array(
    		'title' => 'Button',
    		'selector' => 'a',
    		'classes' => 'button'
    	),
        array(
        	'title' => 'Callout Box',
        	'block' => 'div',
        	'classes' => 'callout',
        	'wrapper' => true
        ),
        array(
        	'title' => 'Bold Red Text',
        	'inline' => 'span',
        	'styles' => array(
        		'color' => '#f00',
        		'fontWeight' => 'bold'
        	)
        )
    );

    $settings['style_formats'] = json_encode( $style_formats );

    return $settings;

}

?>
<?php
/*
Plugin Name: Kalin's Edit Links
Version: 0.7
Plugin URI: http://kalinbooks.com/easy-edit-links/
Description: Adds a box to your page/post edit screen with links and edit buttons for all pages, posts, tags, categories, and links for convenient linking.
Author: Kalin Ringkvist
Author URI: http://kalinbooks.com/

------Rename to Kalin's Easy Page Edit Links------------------

Kalin's Easy Edit Links by Kalin Ringkvist (email: kalin@kalinflash.com)


License:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define("KALINSLINKS_ADMIN_OPTIONS_NAME", "kalinsLinks_admin_options");

function kalinsLinks_admin_page() {//load php that builds our admin page
	require_once( WP_PLUGIN_DIR . '/kalins-easy-edit-links/kalinsLinks_admin_page.php');
}

function kalinsLinks_admin_init(){
	//add_action('contextual_help', 'kalinsLinks_contextual_help', 10, 2);
	
	register_deactivation_hook( __FILE__, 'kalinsLinks_cleanup' );
	
	add_action('wp_ajax_kalinsLinks_refresh', 'kalinsLinks_refresh');
	add_action('wp_ajax_kalinsLinks_save', 'kalinsLinks_save');
	
	//wp_register_style('kalinPDFStyle', WP_PLUGIN_URL . '/kalins-pdf-creation-station/kalinsLinks_styles.css');
	
	
	//--------------you may remove these three lines (comment them out) if you are using hard-coded PDF links in your theme. This will make your admin panels run slightly more efficiently.--------------
	//add_action('save_post', 'kalinsLinks_save_postdata');
	add_meta_box( 'kalinsLinks_sectionid', __( "Easy Edit Links", 'kalinsLinks_textdomain' ), 'kalinsLinks_inner_custom_box', 'post', 'side' );
    add_meta_box( 'kalinsLinks_sectionid', __( "Easy Edit Links", 'kalinsLinks_textdomain' ), 'kalinsLinks_inner_custom_box', 'page', 'side' );
	//--------------------------------
	
}

function kalinsLinks_save(){
	
	check_ajax_referer( "kalinsLinks_admin_save" );
	
	$outputVar = new stdClass();
	
	$kalinsLinksAdminOptions = array();//collect our passed in values so we can save them for next time
	
	$kalinsLinksAdminOptions['enableCache'] = $_POST['enableCache'];
	$kalinsLinksAdminOptions["boxHeight"] = (int) $_POST['boxHeight'];
	$kalinsLinksAdminOptions["charLength"] = (int) $_POST['charLength'];
	
	$kalinsLinksAdminOptions['includeDrafts'] = $_POST['includeDrafts'];
	$kalinsLinksAdminOptions['includeFuture'] = $_POST['includeFuture'];
	$kalinsLinksAdminOptions['includePrivate'] = $_POST['includePrivate'];
	
	
	$kalinsLinksAdminOptions['cache'] = 'none';
	
	update_option(KALINSLINKS_ADMIN_OPTIONS_NAME, $kalinsLinksAdminOptions);//save options to database
	
	//$pdfDir = WP_PLUGIN_DIR . '/kalins-pdf-creation-station/pdf/singles/';
	//$pdfDir = $pdfDirBase .'singles/';
	
	echo "success";
}

function kalinsLinks_refresh() {
	$kalinsLinksAdminOptions = get_option(KALINSLINKS_ADMIN_OPTIONS_NAME);
	$kalinsLinksAdminOptions['cache'] = 'none';
	update_option(KALINSLINKS_ADMIN_OPTIONS_NAME, $kalinsLinksAdminOptions);
	//echo "success";
}

function kalinsLinks_configure_pages() {
	
	$mypage = add_submenu_page('options-general.php', 'Easy Edit Links', 'Easy Edit Links', 'manage_options', __FILE__, 'kalinsLinks_admin_page');
	
	//$mytool = add_submenu_page('tools.php', 'Kalins PDF Creation Station', 'PDF Creation Station', 'manage_options', __FILE__, 'kalinsLinks_tool_page');
	
	//add_action( "admin_print_scripts-$mypage", 'kalinsLinks_admin_head' );
	//add_action('admin_print_styles-' . $mypage, 'kalinsLinks_admin_styles');
	
	//add_action( "admin_print_scripts-$mytool", 'kalinsLinks_admin_head' );
	//add_action('admin_print_styles-' . $mytool, 'kalinsLinks_admin_styles');
}

function kalinsLinks_admin_head() {
	//echo "My plugin admin head";
	//wp_enqueue_script("jquery");
	//wp_enqueue_script("jquery-ui-sortable");
	//wp_enqueue_script("jquery-ui-dialog");
}

/*function kalinsLinks_admin_styles(){//not sure why this didn't work if called from pdf_admin_head
	wp_enqueue_style('kalinPDFStyle');
}*/

function kalinsLinks_inner_custom_box($post) {//creates the box that goes on the post/page edit page
	require_once( WP_PLUGIN_DIR . '/kalins-easy-edit-links/kalinsLinks_custom_box.php');
}


/*function kalinsLinks_contextual_help($text, $screen) {
	if (strcmp($screen, 'settings_page_kalins-pdf-creation-station/kalins-pdf-creation-station') == 0 ) {//if we're on settings page, add setting help and return
		require_once( WP_PLUGIN_DIR . '/kalins-pdf-creation-station/kalinsLinks_admin_help.php');
		return;
	}
}*/

function kalinsLinks_get_admin_options() {
	$kalinsLinksAdminOptions = kalinsLinks_getAdminSettings();
	
	$devOptions = get_option(KALINSLINKS_ADMIN_OPTIONS_NAME);

	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option){
			$kalinsLinksAdminOptions[$key] = $option;
		}
	}

	update_option(KALINSLINKS_ADMIN_OPTIONS_NAME, $kalinsLinksAdminOptions);

	return $kalinsLinksAdminOptions;
}

function kalinsLinks_getAdminSettings(){//simply returns all our default option values
	$kalinsLinksAdminOptions = array();
	$kalinsLinksAdminOptions['enableCache'] = 'true';
	$kalinsLinksAdminOptions['cache'] = 'none';
	$kalinsLinksAdminOptions['boxHeight'] = 150;
	$kalinsLinksAdminOptions['charLength'] = 35;
	$kalinsLinksAdminOptions['includeDrafts'] = 'true';//JavSscript and HTML are so convoluted and silly compared to Flash ActionScript. I mean, using 'on' and 'off' instead of true and false... seriously?
	$kalinsLinksAdminOptions['includeFuture'] = 'true';
	$kalinsLinksAdminOptions['includePrivate'] = 'true';
	//$kalinsLinksAdminOptions['doCleanup'] = "true";
	
	return $kalinsLinksAdminOptions;
}

function kalinsLinks_cleanup() {//deactivation hook. Clear all traces of PDF Creation Station
	
	//$adminOptions = kalinsLinks_get_admin_options();
	//if($adminOptions['doCleanup'] == 'true'){//if user set cleanup to true, remove all options and post meta data
	delete_option(KALINSLINKS_ADMIN_OPTIONS_NAME);//remove all options for admin
	//}
} 

function kalinsLinks_init(){
	//setup internationalization here
	//this doesn't actually run and perhaps there's another better place to do internationalization
}

//wp actions to get everything started
add_action('admin_init', 'kalinsLinks_admin_init');
add_action('admin_menu', 'kalinsLinks_configure_pages');
//add_action( 'init', 'kalinsLinks_init' );//just keep this for whenever we do internationalization - if the function is actually needed, that is.


//content filter is called whenever a blog page is displayed. Comment this out if you aren't using links applied directly to individual posts, or if the link is set in your theme
//add_filter("the_content", "kalinsLinks_content_filter" );

?>
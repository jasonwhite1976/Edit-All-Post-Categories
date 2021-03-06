<?php

/*
   Plugin Name: Edit All Posts
   Plugin URI:
   Description: Edit All Posts plugin.
   Author: JW - From CS ABS-Hosting.nl/Walchum.net
   Version: 1
   Author URI:
*/

// Link in the admin menu
if ( ! defined( 'BATCH_PLUGIN_BASENAME' ) )
	define( 'BATCH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'BATCH_PLUGIN_NAME' ) )
	define( 'BATCH_PLUGIN_NAME', trim( dirname( BATCH_PLUGIN_BASENAME ), '/' ) );
if ( ! defined( 'BATCH_PLUGIN_DIR' ) )
	define( 'BATCH_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BATCH_PLUGIN_NAME );

if ( ! defined( 'BATCH_PLUGIN_URL' ) )
	define( 'BATCH_PLUGIN_URL', WP_PLUGIN_URL . '/' . BATCH_PLUGIN_NAME );

function batch_plugin_path( $path = '' ) {
	return path_join( BATCH_PLUGIN_DIR, trim( $path, '/' ) );
}
/**
 * Set language file
 *
 */

load_plugin_textdomain('batch-move', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * include some configuration, classes and functions
 *
 */

include_once(BATCH_PLUGIN_DIR.'/include/config.inc.php');
include_once(BATCH_PLUGIN_DIR.'/include/classes.php');
include_once(BATCH_PLUGIN_DIR.'/include/functions.php');
/**
 * This was not neccesary but now it must be done
 *
 */
create_initial_taxonomies();


/**
 * Create new batchMove class
 *
 * Structure who has all information
 *
 */

$bm = new batchMove;
/**
 * Set many language strings
 *
 */
$bm->orderbydef = $orderbysLng;//start all defined in config.inc.php and *.mo files
$bm->orderdef = $orderLng;
$bm->frmlabels = $formLabels;
$bm->frmhelp = $formHelp;
$bm->pageing = $pageing;
$bm->information = $information;
$bm->ret_head = $ret_head;
$bm->action = $actions;// end config defined

if (empty($bm->get['row_amount'])) {//empty get fields
	if ($ra = get_option('bm_row_amount')) {//get option
		$bm->per_page = $ra;//option value
	} else {//there is nothing, take default
		$bm->per_page = 15;//default
	}
} else {
	if ($ra = get_option('bm_row_amount')) {//not empty get fields
		if ($bm->get['row_amount']!=$ra) {//new value
			update_option('bm_row_amount', $bm->get['row_amount']);
		}
	} else {//there is a value, but no option set
		add_option('bm_row_amount', $bm->get['row_amount'],'','yes');
	}
	//get value from get field
	$bm->per_page = $bm->get['row_amount'];
}

function batch_plugin_url( $path = '' ) {
	return plugins_url( $path, BATCH_PLUGIN_BASENAME );
}

function batch_add_css_files(){
	wp_enqueue_style( 'batch_css', batch_plugin_url('css/batch.css'));
}
add_action('init', 'batch_add_css_files');

function wp_batch_admin(){
	include('batch_admin.php');
}

function wp_batch_actions(){
	global $application;
	/**
	 * Here you can set menustring ($application)
	 * What kind of role (editor)
	 * The internal Wordpress name (batchadmin)
	 * Function that's fired (wp_batch_admin)	 *
	 */
	add_menu_page( $application, $application, USERLEVEL, 'batchadmin', 'wp_batch_admin','' ,6 );
	
}
add_action('admin_menu', 'wp_batch_actions');//

function batch_add_javascript_files(){
	wp_enqueue_script( 'batch_js', batch_plugin_url('js/batch.js'));
}
add_action('init', 'batch_add_javascript_files');

?>
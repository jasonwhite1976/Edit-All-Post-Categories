<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2012
 */

echo '<h1>'.BM_HEADER.'</h1>';
global $bm;
global $wp_taxonomies;

/**
 * Post values
 *
 */
execute_action($bm, $_REQUEST);



/**
 * Echo form to filter data
 *
 */
//echo $html = show_bm_selector($bm);



/**
 * Get query fields and values
 *
 */

$q = get_a_query($bm);

/**
 * Get date query values, even a RANGE will work!
 *
 */

/*
if ( !empty($bm->get['iDate']) || !empty($bm->get['oDate'])) {
	if (!empty($bm->get['iDate']) && empty($bm->get['oDate'])) {
		$fdate = $bm->get['iDate'];
		$dt = split('-', $fdate);
		$q['year'] = $dt[0];
		$q['monthnum'] = $dt[1];
		$q['day'] = $dt[2];
	} elseif ( !empty($bm->get['iDate']) && !empty($bm->get['oDate'])) {
		function filter_where($where = '') {
			global $bm;
            if (isset($_GET['iDate'])&&(isset($_GET['oDate']))) {
                $where .= ' AND post_date >= "'.$_GET['iDate'].'" AND post_date <= "'.$_GET['oDate'].'" ' ;
                return $where;
            }
		}
		$filter = add_filter('posts_where', 'filter_where');
	}
}
*/

/**
 * Create WP_Wquery object
 *
 */

$query = new WP_Query;

//echo $q;

/**
 * Execute query
 *
 */
//$query->set('hide_empty' ,0);
$posts = $query->query($q);

//if ($bm->cat == "") {return;}
/**
 *  Stupid, table used to force footer down
 *
 */
$html  = '<table width="100%"><tr><td>';
/**
 * Set some page pagination
 *
 */
$html .= get_pagination($bm,$query->max_num_pages);

/**
 * Get query result information
 *
 */
//$html .= get_information($bm, $query->found_posts);

/**
 * Start form
 *
 */
$html .= '<form name="selector" id="selector" method="post" action="admin.php?page=batchadmin">';
$html .= '<input type="hidden" name="page" value="batchadmin" />';

/**
 * Get results
 *
 */
$html .= get_results($bm, $posts);
/**
 * Show action buttons
 *
 */
$html .= show_bm_actions($bm);

$html .= '</form>';

echo $html;
echo '</td></tr></table>';
echo '<div class="clear"></div>';

?>
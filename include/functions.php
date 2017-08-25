<?php

function execute_action(batchMove $bm, $apost=array()){
	
	$do = strpos( $_SERVER['HTTP_REFERER'],'page=batchadmin' ) > 0;
	
	if (!current_user_can(USERLEVEL)) {
		
		die (__($bm->information['noright']));
		
	} else {
		
		if ( !empty($apost) && $do ) {
			$cat = isset($apost['qcat']) ? intval($apost['qcat']) : 0;
			
			$num = isset($apost['ids']) ? count($apost['ids']) : 0;
			
			if (!empty($apost['qcat']))
				$query = '&cat=' . $apost['qcat'];
			
			/*if (!empty($apost['s']))
				$query = '&s=' . $apost['keywords'];
			if (!empty($apost['t']))
				$query = '&t=' . $apost['tag'];*/			
			

			if (isset($apost['submit'])&&$apost['submit'] == 'send-cat') {
				
				switch ($apost['act-cats']) {
						
					case "add":
						foreach ((array) $apost['ids'] as $id) {
							$id = intval($id);
							$cats = wp_get_post_categories($id);
							if (!in_array($cat, $cats)) {
								$cats[] = intval($cat);
								wp_set_post_categories($id, $cats);
							}
						}
						break;
						
					case 'del':
						foreach ((array) $apost['ids'] as $id) {
							$id = intval($id);
							$existing = wp_get_post_categories($id);
							$new = array();
							foreach ((array) $existing as $_cat) {
								if ($cat != $_cat)
									$new[] = intval($_cat);
							}
							wp_set_post_categories($id, (array) $new);
						}
						break;
						
					default:
					;
				} // switch	
				
			} // if
		}
	}
}

function show_bm_actions(batchMove $bm){
	$html  = '<div id="actions" class="actions">';
	$html  = '<fieldset style="border:solid black 1px;padding:5px;">';
	$html .= '<table width="100%"><tr><td width="50%">';
	$html .= '<input type="hidden" name="page" value="batchadmin" />';
	$html .= 'Category: ';
	$html .= wp_dropdown_categories('name=qcat&hide_empty=0&hierarchical=1&echo=0' );
	$html .= '<select name="act-cats" id="actions">';
	
	foreach ($bm->action as $key => $value) {
		$selected = (isset($bm->get['actions'])&& $bm->get['actions'] == $key ) ? ' selected="selected"' : '' ;
		
		$html .=  '<option value="'.$key.'"'.$selected .'>'.$value.'&nbsp;</option>\n';
	}
	
	$html .= '</select>';
	$html .= '<button class="eap-submit-button" name="submit" type="submit" value="send-cat">Go</button>';
	$html .= '</td>';
	$html .= '<td>';
	$html .= '</td>';
	$html .= '</tr></table>';
	$html .= '</fieldset>';
	$html .= '</div>';
	return $html;
}

function get_a_query(batchMove $bm){
	$qa = array( 'paged' => $bm->paged,
	 			 'posts_per_page' => $bm->per_page,
				 'orderby' => $bm->orderby,
				 'order' => $bm->order );
	
	$gets = $bm->get;
	
    /*if (isset($gets['qcat'])) {
        $cat = $gets['qcat'];
    } elseif (isset($gets['cat'])&&($gets['cat']!=='0')) {
        $cat = $gets['cat'];
    } else {
        $cat = '';
    }
	if ($cat) {
		$qa['cat'] = intval($cat);
	}*/
	
	$cat = '';

	return $qa;
}

function get_pagination(batchMove $bm, $maxpages=1){
	$pagination = '';
	if ( $maxpages > 1 ) {
		$current = preg_replace('/&?paged=[0-9]+/i', '', strip_tags($_SERVER['REQUEST_URI'])); // I'll happily take suggestions on a better way to do this, but it's 3am so

		$pagination .= "<div class='tablenav-pages nav-pages'>";

		if ( $bm->paged > 1 ) {
			$prev = $bm->paged - 1;
			$pagination .= '<a class="prev page-numbers" href="'.$current.'&amp;paged='.$prev.'">&laquo;'.$bm->pageing['prev'].'</a>';
		}

		for ( $i = 1; $i <= $maxpages; $i++ ) {
			if ( $i == $bm->paged ) {
				$pagination .= '<span class="page-numbers current">'.$i.'</span>';
			} else {
				$pagination .= '<a class="page-numbers" href="'.$current.'&amp;paged='.$i.'">'.$i.'</a>';
			}
		}

		if ( $bm->paged < $maxpages ) {
			$next = $bm->paged + 1;
			$pagination .= '<a class="next page-numbers" href="'.$current.'&amp;paged='.$next.'">'.$bm->pageing['next'].'&raquo;</a>';
		}

		$pagination .= "</div>";
		return $pagination;
	}
}
function get_information (batchMove $bm, $founded=0){
	$str  = $bm->information['lookedforpost'];
	$str .= '<strong>%s</strong>,';
	$str .= $bm->information['taggedwith'];
	$str .= '<strong>%s</strong>,';
	$str .= $bm->information['orderedby'];
	$str .= '<strong>%s</strong>,';
	$str .= $bm->information['posts'];
	$str .= '<strong>%s</strong>,';
	$str .= $bm->information['displayed'];
	$str .= '<strong>%s</strong> ';
	$str .= $bm->information['perpage'];
    $order = (isset($bm->orderbydef[$bm->orderby])) ? $bm->orderbydef[$bm->orderby] : $bm->orderbydef['date'];
	//$order = $bm->orderbydef[$bm->orderby];
	$str = sprintf($str,
					!empty($bm->cat)? $bm->cat : $bm->information['any'],
					!empty($bm->tag)? $bm->tag : $bm->information['none'],
					!empty($order)? $order : $bm->information['none'],
					!empty($founded)? $founded : $bm->information['none'],
					!empty($bm->per_page)? $bm->per_page : $bm->information['none']);
	return $str;


}

function get_results(batchMove $bm, $q_posts) {
	$html  = '<div id="posts">';

	$html .='<table class="widefat">
			<tr>
			<th class="t_left" scope="col"><input onclick="toggle_checkboxes()" type="checkbox" id="toggle" title="Select all posts" /></th>			
			<th class="t_left" scope="col">' . $bm->ret_head['title'] . '</th> 
			<th class="t_left" scope="col">' . $bm->ret_head['categories'] . '</th>			
			<th class="t_left" scope="col" colspan="2">' . $bm->ret_head['actions'] . '</th>
			</tr>';
	$html .= '<tbody>';
    $i = 0;
	
	foreach ( (array) $q_posts as $post ) {
		//$categories = get_categories('type=post&hide_empty=0');
		$categories = wp_get_post_categories($post->ID);
		$cats = '';
		$comma = false;
		foreach ( (array) $categories as $cat ) {
			$cats .= $comma ? ', ' : '';
			$cats .=  get_cat_name($cat);
			$comma = true;
		}

		$html .= '
				<tr' . ( $i++ % 2 == 0  ? ' class="alternate"' : '' ) .'>
					<td><input type="checkbox" name="ids[]" value="' . $post->ID . '" /></td>
					
		';
        $i++;

		$html .=  '
					<td>' . $post->post_title . '</td>
					<td>' . $cats . '</td>
					<td><a href="' . get_permalink($post->ID) . '" target="_blank">View</a></td>
					<td><a href="post.php?action=edit&post=' . $post->ID . '">Edit</a></td>
				</tr>
		';
	}
	
	$html .= '<tbody>';
	$html .= '</table>';
	$html .= '</div>';
	return $html;
}

function get_action(){
	$html  = '<div id="actions" class="tablenav">';
	$html .= '	<div class="action">
					<label for="cat">Category:</label>
					' . wp_dropdown_categories('name=qcat&hide_empty=0&hierarchical=1&echo=0') . '
					<input type="submit" name="add" value="Add to" title="Add the selected posts to this category." />
					<input type="submit" name="remove" value="Remove from" title="Remove the selected posts from this category." />
				</div>

				<div class="action">
					<label for="cat">Tags:</label>
					<input type="text" name="tags" title="Separate multiple tags with commas." />
					<input type="submit" name="replace_tags" value="Replace" title="Replace the selected posts\' current tags with these ones." />
					<input type="submit" name="tag" value="Add" title="Add these tags to the selected posts without altering the posts\' existing tags." />
					<input type="submit" name="untag" value="Remove" title="Remove these tags from the selected posts." />
				</div>

				' . $pagination . '
			</div>';


}
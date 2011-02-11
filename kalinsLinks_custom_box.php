<?php

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

$adminOptions = kalinsLinks_get_admin_options();//for individual pages/posts we grab all the PDF options from the options page instead of the POST

function my_excerpt($text, $excerpt, $include){
	
	if($include == "false"){
		return "";
	}
	
	if($excerpt){
		return $excerpt;
	}
	
	//$text = strip_tags(strip_shortcodes	
	
	if(strlen($text) > 250){
		return htmlspecialchars(strip_tags(strip_shortcodes(substr($text, 0, 250)))) ."...";//clean up and return excerpt
	}else{
		return htmlspecialchars(strip_tags(strip_shortcodes($text)));
	}
}


		
//$titlePage = $adminOptions["titlePage"];
//$finalPage = $adminOptions["finalPage"];

//echo $adminOptions["cache"];

if($adminOptions["cache"] != "none"){//if we've already got a cached version of this baby, just serve it up and be done with it
	//echo "is cached";
	echo $adminOptions["cache"];
	//echo $output;
	return;
}

$charLength = $adminOptions["charLength"];

if($adminOptions["charLength"] == 0){
	$charLength = 1000;//if it's unlimited, set to ridiculously high number to ensure we get the whole title
}

$filterString = "publish";

if($adminOptions["includeDrafts"] == "true"){
	$filterString = $filterString .",draft";
}

if($adminOptions["includeFuture"] == "true"){
	$filterString = $filterString .",future";
}

if($adminOptions["includePrivate"] == "true"){
	$filterString = $filterString .",private";
}

//$filterString = $filterString .",draft";


$beginOutput =   '<style type="text/css">
  	.KLScrollBox{overflow:scroll; overflow-x:hidden; height:' .$adminOptions['boxHeight'] .'px;}
	.KLScrollNav{overflow-x:auto;}
  </style>
  <script type="text/javascript">		
		function KLSetType(intType){
			var l = divArray.length;
			for(var i=0; i<l; i++){
				document.getElementById(divArray[i]).style.display = "none";
			}
			document.getElementById(divArray[intType]).style.display = "block";
		}
		
		function KLRefresh(){
			
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {// code for IE6, IE5 (why am I even attempting to support IE 6?)
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					location.reload(true);
				}
			}
			
			xmlhttp.open("POST",ajaxurl,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("action=kalinsLinks_refresh&_ajax_nonce=saveNonce");
		}';
   
	$divArrayOutput = 'var divArray = new Array(';
	$navOutput = '</script><div class="KLScrollNav"><p align="left">';
	$output = '';
	
	$typeCount = 0;
    
	$typeArr = json_decode(stripslashes($adminOptions["typeArr"]));
	$l = count($typeArr);
	$wpurl = get_blogInfo("wpurl");
	
	for ( $i = 0; $i < $l; $i++) {//loop to add a sortable item for every one in our list
		
		if(!$typeArr[$i]->enabled){
			continue;
		}
		
		$typeName = $typeArr[$i]->typeName;
		
		if($i == 0){
			$output = $output .'<div id="KL_' .$typeName .'" class="KLScrollBox">';
		}else{
			$output = $output .'<div id="KL_' .$typeName .'" class="KLScrollBox" style="display:none">';
		}
		
		$divArrayOutput = $divArrayOutput .'"KL_' .$typeName .'", ';
		
		$navOutput = $navOutput .'<a href="javascript:KLSetType(' .$typeCount .');">' .$typeArr[$i]->abbr .'</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
		
		switch($typeName){
			
			case "tag":
				$pageList = get_tags('hide_empty=0');
				$le = count($pageList);
				for($j=0; $j<$le; $j++){//build our list of tags
					$pageID = $pageList[$j]->term_id;
					//$wpurl = get_blogInfo("wpurl");
					$output = $output .'<a href="' .$wpurl .'/wp-admin/edit-tags.php?action=edit&taxonomy=post_tag&post_type=post&tag_ID=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_tag_link( $pageID ) .'" title="' .$pageList[$j]->description .'">' .$pageList[$j]->name .'</a><br />';
				}
				break;
			case "category":
				$pageList = get_categories('hide_empty=0');
				$le = count($pageList);
				for($j=0; $j<$le; $j++){//build our list of cats
					$pageID = $pageList[$j]->term_id;
					//$wpurl = get_blogInfo("wpurl");
					$output = $output .'<a href="' .$wpurl .'/wp-admin/edit-tags.php?action=edit&taxonomy=category&post_type=post&tag_ID=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_category_link( $pageID ) .'" title="' .$pageList[$j]->description .'">' .$pageList[$j]->name .'</a><br />'; 
				}
				break;
			case "link":
				$pageList = get_bookmarks('hide_invisible=0');
				$le = count($pageList);
				for($j=0; $j<$le; $j++){//build our list of links
					$pageID = $pageList[$j]->link_id;
					//$wpurl = get_blogInfo("wpurl");
					$output = $output .'<a href="' .$wpurl .'/wp-admin/link.php?action=edit&link_id=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .$pageList[$j]->link_url .'" title="' .$pageList[$j]->link_description .'">' .substr($pageList[$j]->link_name, 0, $charLength)  .'</a><br />';
				}
				break;
			
			case "attachment" :
				$pageList = get_posts('numberposts=-1&post_type=' .$typeName);
				$le = count($pageList);
				for($j=0; $j<$le; $j++){//build our list of pages with checkboxes
					$pageID = $pageList[$j]->ID;
					$output = $output .'<a href="' .$wpurl .'/wp-admin/media.php?attachment_id=' .$pageID .'&action=edit">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .wp_get_attachment_url( $pageID ) .'" title="' .$pageList[$j]->post_content .'">' .substr($pageList[$j]->post_title, 0, $charLength) .'</a><br />';
				}
				break;
				
			default :
				$pageList = get_posts('numberposts=-1&post_type=' .$typeName .'&post_status=' .$filterString);
				$le = count($pageList);
				for($j=0; $j<$le; $j++){//build our list of pages with checkboxes
					$pageID = $pageList[$j]->ID;
					$output = $output .'<a href="' .$wpurl .'/wp-admin/post.php?post=' .$pageID .'&action=edit">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_permalink( $pageID ) .'" title="' .my_excerpt($pageList[$j]->post_content , $pageList[$j]->post_excerpt, $adminOptions["includeExcerpt"]).'">' .substr($pageList[$j]->post_title, 0, $charLength) .'</a><br />';
				}
				break;
		}
		
		$output = $output ."</div>";
		
		$typeCount++;
	}
	
	$navOutput = substr($navOutput, 0, count($navOutput) - 20) .'</p></div>';//remove the final "|" divider and the &nbsp;'s that go with it and close the p
	$divArrayOutput = substr($divArrayOutput, 0, count($divArrayOutput) - 3) .');';//remove the last comma and close the array.
	$output = $beginOutput .$divArrayOutput .$navOutput .$output;
   
	if($adminOptions["enableCache"] == "true"){
		$output = $output .'<p align="right"><a href="javascript:KLRefresh();">Refresh</a></p>';
		$adminOptions["cache"] = $output;//save the whole dang thing to a simple option
	}
	 
	 update_option(KALINSLINKS_ADMIN_OPTIONS_NAME, $adminOptions);
	 
	 echo $output;
	 
    ?>
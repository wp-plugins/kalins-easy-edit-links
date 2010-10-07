<?php

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

$adminOptions = kalinsLinks_get_admin_options();//for individual pages/posts we grab all the PDF options from the options page instead of the POST
		
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


$output =   '<style type="text/css">
  	.KLScrollBox{overflow:scroll; overflow-x:hidden; height:' .$adminOptions['boxHeight'] .'px;}
  </style>
  
  <script type="text/javascript">
		var divArray = new Array("KLPage", "KLPost", "KLCategory", "KLTag", "KLLink");							
		
		function KLSetType(intType){
			for(var i=0; i<5; i++){
				document.getElementById(divArray[i]).style.display = "none";
			}
			document.getElementById(divArray[intType]).style.display = "block";
		}
		
		function KLRefresh(){
			
			/*var data = { action: "kalins_pdf_admin_save",
				_ajax_nonce : saveNonce
			}*/
			
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
		}
		
	</script>
    
   <p align="center"><a href="javascript:KLSetType(0);">Page</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:KLSetType(1);">Post</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:KLSetType(2);">Category</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:KLSetType(3);">Tag</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:KLSetType(4);">Link</a></p>
   <div id="KLPage" class="KLScrollBox">';
   
   
 
   		//$pageList = get_pages();
		$pageList = get_posts('numberposts=-1&post_type=page&post_status=' .$filterString);
        $l = count($pageList);
		for($i=0; $i<$l; $i++){//build our list of pages with checkboxes
           	$pageID = $pageList[$i]->ID;
			$wpurl = get_blogInfo("wpurl");
            $output = $output .'<a href="' .$wpurl .'/wp-admin/post.php?post=' .$pageID .'&action=edit">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_permalink( $pageID ) .'" title="' .$pageList[$i]->post_excerpt .'">' .substr($pageList[$i]->post_title, 0, $charLength) .'</a><br />';
        }
    
   
   
   	$output = $output .'</div>
   	<div id="KLPost" class="KLScrollBox" style="display:none">';
   
	
   		$pageList = get_posts('numberposts=-1&post_status=' .$filterString);
        $l = count($pageList);
		for($i=0; $i<$l; $i++){//build our list of posts
           	$pageID = $pageList[$i]->ID;
			$wpurl = get_blogInfo("wpurl");
            $output = $output .'<a href="' .$wpurl .'/wp-admin/post.php?post=' .$pageID .'&action=edit">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_permalink( $pageID ) .'" title="' .$pageList[$i]->post_excerpt .'">' .substr($pageList[$i]->post_title, 0, $charLength)  .'</a><br />';
        }
	
   
   	$output = $output .'</div>
   	<div id="KLCategory" class="KLScrollBox" style="display:none">';
    
    
		$pageList = get_categories('hide_empty=0');
        $l = count($pageList);
		for($i=0; $i<$l; $i++){//build our list of cats
           	$pageID = $pageList[$i]->term_id;
			$wpurl = get_blogInfo("wpurl");
            $output = $output .'<a href="' .$wpurl .'/wp-admin/edit-tags.php?action=edit&taxonomy=category&post_type=post&tag_ID=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_category_link( $pageID ) .'" title="' .$pageList[$i]->description .'">' .$pageList[$i]->name .'</a><br />'; 
        }
	
    
     $output = $output .'</div>
   	<div id="KLTag" class="KLScrollBox" style="display:none">';
    
    
		$pageList = get_tags('hide_empty=0');
        $l = count($pageList);
		for($i=0; $i<$l; $i++){//build our list of tags
           	$pageID = $pageList[$i]->term_id;
			$wpurl = get_blogInfo("wpurl");
            $output = $output .'<a href="' .$wpurl .'/wp-admin/edit-tags.php?action=edit&taxonomy=post_tag&post_type=post&tag_ID=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .get_tag_link( $pageID ) .'" title="' .$pageList[$i]->description .'">' .$pageList[$i]->name .'</a><br />';
        }
	
    
    $output = $output .'</div>
   	<div id="KLLink" class="KLScrollBox" style="display:none">';
    
    
		$pageList = get_bookmarks('hide_invisible=0');
        $l = count($pageList);
		for($i=0; $i<$l; $i++){//build our list of links
           	$pageID = $pageList[$i]->link_id;
			$wpurl = get_blogInfo("wpurl");
            $output = $output .'<a href="' .$wpurl .'/wp-admin/link.php?action=edit&link_id=' .$pageID .'">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=" ' .$pageList[$i]->link_url .'" title="' .$pageList[$i]->link_description .'">' .substr($pageList[$i]->link_name, 0, $charLength)  .'</a><br />';
        }
	
    
	$output = $output .'</div>';
	if($adminOptions["enableCache"] == "true"){
		$output = $output .'<p align="right"><a href="javascript:KLRefresh();">Refresh</a></p>';
		$adminOptions["cache"] = $output;//save the whole dang thing to a simple option
	}
	 
	 update_option(KALINSLINKS_ADMIN_OPTIONS_NAME, $adminOptions);
	 
	 echo $output;
	 
    ?>
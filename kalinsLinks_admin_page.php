<?php

	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$save_nonce = wp_create_nonce( 'kalinsLinks_admin_save' );
	
	$adminOptions = kalinsLinks_get_admin_options();
?>

<style type="text/css">

.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default { border: 1px solid #d3d3d3; background: #e6e6e6 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x; font-weight: normal; color: #555555; }
.ui-icon { width: 16px; height: 16px; background-image: url(images/ui-icons_222222_256x240.png); }
.ui-icon-arrowthick-2-n-s { background-position: -128px -48px; }

.formDiv{
	float:left;
	height:300px;
	margin: 10px;
	padding: 10px;
	overflow:scroll;
	overflow-x:hidden;
	width:360px;
}

.endFloat{
	clear:left;
}

</style>

<script language="javascript" type='text/javascript'>

	jQuery(document).ready(function($){
			
		$('#btnSave').click(function() {
			
			var data = { action: 'kalinsLinks_save',
				_ajax_nonce : "<?php echo $save_nonce;?>"
			}
			
			data.boxHeight = $("#txtBoxHeight").val();
			data.charLength = $("#txtCharCount").val();
			data.includeDrafts = $("#chkIncludeDrafts").is(':checked');
			data.includeFuture = $("#chkIncludeFuture").is(':checked');
			data.includePrivate = $("#chkIncludePrivate").is(':checked');
			data.enableCache = $("#chkEnableCache").is(':checked');
			data.includeExcerpt = $("#chkIncludeExcerpt").is(':checked');
			
			var sortArr = $("#sortable").sortable('toArray');
			
			var typeArr = new Array();
			var l = sortArr.length;
			for(var i = 0; i<l; i++){
				var typeName = sortArr[i].substr(9);
				
				var typeObj = new Object();	
				typeObj.typeName = typeName;
				typeObj.enabled = $("#chk_type_" + typeName).is(':checked');
				typeObj.abbr = $("#txt_type_" + typeName).val();
				typeArr.push(typeObj);
			}
			
			data.typeArr = JSON.stringify(typeArr);
	
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				location.reload(true);
			});
		});
		
		function setupSort(){
			
			<?php
			
			$sortHTML = '<p>Select types, enter abbreviation, drag/drop to reorder.</ p><ul id="sortable">';
			$typeArr = json_decode(stripslashes($adminOptions["typeArr"]));
			$l = count($typeArr);
			
			for ($i = 0; $i<$l; $i++ ) {//loop to add a sortable item for every one in our list
				
				$typeName = $typeArr[$i]->typeName;
				$checkedString = "";
				if($typeArr[$i]->enabled){
					$checkedString = ' checked="yes"';
				}
				
				$sortHTML = $sortHTML .'<li class="ui-state-default" id="typeSort_' .$typeName .'"><input type="checkbox" id="chk_type_' .$typeName .'"' .$checkedString .'/> - <input type="text" id="txt_type_' .$typeName .'" size="5" maxlength="10" value="' .$typeArr[$i]->abbr .'" /> - ' .$typeName .'</li>';
				
			}
			
			$post_types = get_post_types('','names');
			
			foreach ($post_types as $post_type ) {//loop to add a meta box to each type of post (pages, posts and custom)
				$isMatch = false;
				for ($i = 0; $i<$l; $i++ ) {
					if($post_type == $typeArr[$i]->typeName){
						$isMatch = true;
						break;
					}
				}
				
				if(!$isMatch){//if we have a post type that wasn't listed in our saved array, add it to the list
					$sortHTML = $sortHTML .'<li class="ui-state-default" id="typeSort_' .$post_type .'"><input type="checkbox" id="chk_type_' .$post_type .'" /> - <input type="text" id="txt_type_' .$post_type .'" size="5" maxlength="10" value="' .substr($post_type, 0, 4) .'" /> - ' .$post_type .'</li>';
				}
				
			}
			
			$sortHTML = $sortHTML .'</ul>';
			echo "$('#sortHolder').html('" .$sortHTML ."');"
			
			?>
			
			$("#sortable").sortable();
			$("#sortable").disableSelection();
			
		}
		
		setupSort();
		
	});
</script>


<h2>Easy Edit Links - settings</h2>

<h3>by Kalin Ringkvist - <a href="http://kalinbooks.com/">KalinBooks.com</a></h3>

<p><a href="http://kalinbooks.com/easy-edit-links-wordpress-plugin/">Plugin Page</a></p>

<br/><hr/><br/>

<div class='formDiv'>
<p><input type="checkbox" id="chkEnableCache" <?php if($adminOptions["enableCache"] == 'true'){echo "checked='yes'";} ?> /> enable cache</p>
<p>Box height: <input type="text" id="txtBoxHeight" name="txtBoxHeight" size="2" maxlength="3" value='<?php echo $adminOptions["boxHeight"]; ?>' /> pixels</p>
<p>Link character count: <input type="text" id="txtCharCount" name="txtCharCount" size="2" maxlength="3" value='<?php echo $adminOptions["charLength"]; ?>' /> 0 = no limit - (set to appropriate number if you don't want long page titles)</p>
<p><input type="checkbox" id="chkIncludeDrafts" <?php if($adminOptions["includeDrafts"] == 'true'){echo "checked='yes'";} ?> /> show drafts</p>
<p><input type="checkbox" id="chkIncludeFuture" <?php if($adminOptions["includeFuture"] == 'true'){echo "checked='yes'";} ?> /> show future posts</p>
<p><input type="checkbox" id="chkIncludePrivate" <?php if($adminOptions["includePrivate"] == 'true'){echo "checked='yes'";} ?> /> show private posts</p>
<p><input type="checkbox" id="chkIncludeExcerpt" <?php if($adminOptions["includeExcerpt"] == 'true'){echo "checked='yes'";} ?> /> include excerpts as link titles (Increases load time. May cuase PHP error.)</p>
<p><button id="btnSave">Save Settings</button></p>

</div>
   
<div id="sortHolder" class="formDiv"></div> 

<div class="endFloat">

<br/><hr/><br/>

<p>This is what you should see on the page/post edit screen:</p>
<br/>

<div style="width:300px" id="testID">

<?php
	kalinsLinks_inner_custom_box(null);
?>

</div>
</div>

<br/><hr/><br/>

<h3>Dragging Links:</h3>
<p>Safari and Google Chrome users can  drag and drop the links straight into their post. The post summary is added to the link as the title. Internet Explorer users must highlight, then copy and paste the link into their post. FIREFOX USERS BEWARE. When dragging or copying links into the post using FireFox, the link is automatically converted to a relative link, which then breaks when you publish the page. FireFox users will need to take an extra step to rewrite the URL :(</p>
<br/>
<p><b>Reset values: </b>To reset all values back to defaults, deactivate the plugin and re-activate.</p>

<br/><hr/><br/>
    
<p>Thank you for using Kalin's Easy Edit Links. To report bugs, request help or suggest features, visit the <a href="http://kalinbooks.com/easy-edit-links-wordpress-plugin/" target="_blank">plugin page</a>. If you find this plugin useful, please consider <A href="http://wordpress.org/extend/plugins/kalins-easy-edit-links/">rating this plugin on WordPress.org</A> or making a PayPal donation:</p>
       
<p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="C6KPVS6HQRZJS">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Donate to Kalin Ringkvist's WordPress plugin development.">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</p><br/>
<p>You may also like <a href="http://kalinbooks.com/pdf-creation-station/">Kalin's PDF Creation Station WordPress Plugin</a> - <br /> Create highly customizable PDF documents from any combination of pages and posts, or add a link to generate a PDF on each individual page or post.</p>
<p>Or <a href="http://kalinbooks.com/post-list-wordpress-plugin/" target="_blank">Kalin's Post List</a> - <br /> Use a shortcode in your posts to insert dynamic, highly customizable lists of posts, pages, images, or attachments based on categories and tags. Works for table-of-contents pages or as a related posts plugin.</p>
</html>
<?php

	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$save_nonce = wp_create_nonce( 'kalinsLinks_admin_save' );
	
	$adminOptions = kalinsLinks_get_admin_options();
?>

<script language="javascript" type='text/javascript'>
	
	function KLSave(){
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {// code for IE6, IE5 (why am I even attempting to support IE 6?)
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					//alert("What the hell is oging on here?");
					location.reload(true);
				}
			}
			
			xmlhttp.open("POST",ajaxurl,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send("action=kalinsLinks_save&_ajax_nonce=<?php echo $save_nonce;?>&boxHeight=" + document.getElementById('txtBoxHeight').value + "&charLength=" + document.getElementById('txtCharCount').value + "&includeDrafts=" + document.getElementById('chkIncludeDrafts').checked + "&includeFuture=" + document.getElementById('chkIncludeFuture').checked + "&includePrivate=" + document.getElementById('chkIncludePrivate').checked + "&enableCache=" + document.getElementById('chkEnableCache').checked);
		}
</script>


<h2>Easy Edit Links - settings</h2>

<h3>by Kalin Ringkvist - <a href="http://kalinbooks.com/">KalinBooks.com</a></h3>

<p><a href="http://kalinbooks.com/easy-edit-links-wordpress-plugin/">Plugin Page</a></p>

<br/><hr/><br/>
<p><input type="checkbox" id="chkEnableCache" <?php if($adminOptions["enableCache"] == 'true'){echo "checked='yes'";} ?> /> enable cache</p>
<p>Box height: <input type="text" id="txtBoxHeight" name="txtBoxHeight" size="2" maxlength="3" value='<?php echo $adminOptions["boxHeight"]; ?>' /> pixels</p>
<p>Link character count: <input type="text" id="txtCharCount" name="txtCharCount" size="2" maxlength="3" value='<?php echo $adminOptions["charLength"]; ?>' /> 0 = no limit - (set to appropriate number if you don't want long page titles)</p>
<p><input type="checkbox" id="chkIncludeDrafts" <?php if($adminOptions["includeDrafts"] == 'true'){echo "checked='yes'";} ?> /> show drafts</p>
<p><input type="checkbox" id="chkIncludeFuture" <?php if($adminOptions["includeFuture"] == 'true'){echo "checked='yes'";} ?> /> show future posts</p>
<p><input type="checkbox" id="chkIncludePrivate" <?php if($adminOptions["includePrivate"] == 'true'){echo "checked='yes'";} ?> /> show private posts</p>

<p><button id="btnSave" onClick="javascript:KLSave();">Save Settings</button></p>
    
<br/><hr/><br/>

<p>This is what you should see on the page/post edit screen:</p>
<br/>

<div style="width:300px" id="testID">

<?php
	kalinsLinks_inner_custom_box(null);
?>

</div>

<br/><hr/><br/>

<h3>Dragging Links</h3>
<p>Safari and Google Chrome users can  drag and drop the links straight into their post. The post summary is added to the link as the title. Internet Explorer users must highlight, then copy and paste the link into their post. FIREFOX USERS BEWARE. When dragging or copying links into the post using FireFox, the link is automatically converted to a relative link, which then breaks when you publish the page. FireFox users will need to take an extra step to rewrite the URL :(</p>

<br/><hr/><br/>
    
<p>Thank you for using Easy Edit Links</p>

<?php 
$versionNum = (int) substr(phpversion(), 0, 1);//check php version and possibly warn user
if($versionNum < 5){//I have no idea what this thing will do at anything below 5.2.11 :)
    echo "<p>You are running PHP version "  .phpversion() .". This plugin was built with PHP version 5.2.11 and has NOT been tested with older versions.</p>";
}
?>
<p>Kalin's Easy Edit Links was built with WordPress version 3.0. It has NOT been tested on older versions and might fail.</p>
<p>You may also like <a href="http://kalinbooks.com/pdf-creation-station/">Kalin's PDF Creation Station WordPress Plugin</a> - <br /> Create highly customizable PDF documents from any combination of pages and posts, or add a link to generate a PDF on each individual page or post.</p>
<p>Or <a href="http://kalinbooks.com/post-list-wordpress-plugin/" target="_blank">Kalin's Post List</a> - <br /> Use a shortcode in your posts to insert dynamic, highly customizable lists of posts, pages, images, or attachments based on categories and tags. Works for table-of-contents pages or as a related posts plugin.</p>
</html>
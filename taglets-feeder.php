<?php
/*
Plugin Name: Taglets Feeder
Plugin URI: http://www.taglets.org/taglets-feeder
Description: Delivers your published posts to taglets.org
Version: 0.1
Author: David Beckemeyer
Author URI: http://mrblog.org
*/
/*
Derived from code originally Copyright 2008  Andrew Jaswa  (email : ajaswa@gmail.com)

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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', '_db_tf_add_admin',1);
add_action('admin_head', '_db_tf_add_styles',13);

function _db_tlfeeder($post_ID){
	if($_POST['post_type'] != "page"){

		$tags = explode(",", $_POST['tags_input']);
            
		$options = get_option("db_tf_options");

		if ( isset($options->fixed_tag) && $options->fixed_tag !=''){
			$tags[] = $options->fixed_tag;
		}

		$postTitle = get_post($post_ID);
		$title = $postTitle->post_title;
		if ( isset($options->url) && $options->url !=''){
			$postUrl = get_bloginfo('url');
		}else {
			$postUrl = get_permalink($post_ID);
		}

		if ( isset($options->preview) && $options->preview !=''){
			$shortnameUrl = "http://shortna.me/v/";
		}else {
			$shortnameUrl = "http://shortna.me/";
		}

		if (function_exists('curl_init') && $postUrl) {
			$encodedUrl = urlencode($postUrl);
			$getUrl = "http://shortna.me/hash/?api=2&snURL=".$encodedUrl;
			$session = curl_init($getUrl);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_FAILONERROR, true);
			curl_setopt($session, CURLOPT_GET, true);
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($session, CURLOPT_TIMEOUT, 20);
			$shortnameHash = curl_exec($session);
			curl_close($session);

			if(stristr($shortnameHash, 'ERROR') === FALSE || (isset($shortnameHash) && $shortnameHash != '')) {
				$shortUrl = $shortnameUrl . $shortnameHash;
			}else {
				$postUrl = $shortUrl;
			}
		}

		$twitterLen = 120;
		$urlLen = strlen($shortUrl);
		$titleLen = strlen($title);
		$charLen = $urlLen + $titleLen;

		if ($charLen > $twitterLen){
			$subtract = $titleLen - ($charLen - $twitterLen);
			$shortTitle = substr($title, 0, $subtract);
			$title = $shortTitle . "...";
		}

		$curlPost = $title .	 " " . $shortUrl;
		$n = 0;
		foreach ($tags as $tagname) {
			$url = "http://a.taglets.org/tag/comment/" . urlencode($tagname);
			$session = curl_init($url);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_FAILONERROR, true);
			curl_setopt($session, CURLOPT_POST, true);
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($session, CURLOPT_TIMEOUT, 10);
			$post_fields = "comment=" . urlencode($curlPost);
			curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);
			$stream = curl_exec($session);
			curl_close($session);
			$n++;
                        if ($n >= 4) {
				sleep($n);
				$n = 0;
			}
		}
	}
}

function _db_tf_add_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('Taglets Feeder', 'Taglets Feeder', 8, 'taglets-feeder', '_db_tf_admin_page');
	}
}
function _db_tf_add_styles() {
	if ($_GET['page'] == 'taglets-feeder') {
		$stylePath = get_settings('siteurl');
		echo '<link rel="stylesheet" type="text/css" href="' . $stylePath . '/wp-content/plugins/taglets-feeder/taglets-feeder.css" />';
	}
}

function _db_tf_admin_page() {
?>
<div id="taglets-feeder" class="wrap"><h2>Taglets Feeder</h2>
<?php
	if (!function_exists('curl_init')) {
?>
			<div class="error">
				<p>This plugin requires that cURL be installed on your server and available via PHP. One or both of these appear to be lacking.  Sorry!</p>
			</div>
<?php
	} else {
		if (isset($_POST['update'])) {
			if ($_POST['url']) $options->url = $_POST['url'];
			if ($_POST['preview']) $options->preview = $_POST['preview'];
			if ($_POST['fixed_tag']) $options->fixed_tag = $_POST['fixed_tag'];
			update_option("db_tf_options",$options);
			echo '<p id="message" class="updated">Options updated.</p>';
			$options = get_option("db_tf_options");
			
		}
		$options = get_option("db_tf_options");
?>

<form method="post">
	<fieldset class="options">
		<legend><?php _e('Options') ?></legend>
                <label for="user"><?php _e('Fixed Tag'); ?></label>
                <input type="text" class="text" name="fixed_tag" id="fixed_tag" value="<?php echo $options->fixed_tag; ?>" />
                <small>(comment will always be posted to specified tag)</small>

		<label for="url"><?php _e('Use blog URL (rather than post URL)'); ?></label>
		<input type="checkbox" class="checkbox" id="url" name="url" value="url" <?php if ( isset($options->url) && $options->url !=''){?>checked="checked"<?php }?> />

		<label for="preview"><?php _e('Use preview url'); ?></label>
		<input type="checkbox" class="checkbox" id="preview" name="preview" value="preview" <?php if ( isset($options->preview) && $options->preview !=''){?>checked="checked"<?php }?> />

		<p class="submit">
			<input type="submit" name="update" id="update" value="<?php _e('Update') ?>" />
		</p>
	</fieldset>

</form>
</div>

<?php
	}

}


// add_action('save_post', '_db_tlfeeder');
// add_action('publish_post', '_db_tlfeeder');

add_action('new_to_publish', '_db_tlfeeder');
add_action('draft_to_publish', '_db_tlfeeder');
add_action('pending_to_publish', '_db_tlfeeder');
add_action('future_to_publish', '_db_tlfeeder');


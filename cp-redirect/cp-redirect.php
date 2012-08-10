<?
/*
Plugin Name: CP Redirect
Plugin URI: http://www.copress.org/wiki/CP_Redirect_plugin
Description: Redirects old College Publisher URLs to the new ones in WordPress, based on the article ID stored in the custom field.
Version: 0.1.1
Author: Daniel Bachhuber
Author URI: http://www.danielbachhuber.com/
*/

/**
 * 
 * Warning: version 0.1.1 is a beta release and has only been tested on a few production sites
 *
 **/

function cp_redirect_options () {
	
	$message = null;
	$message_updated = __("Your settings have been updated.", 'cp-redirect');
	
	// Get the past settings if appropriate
	$cp_redirect = $newoptions = get_option('cp_redirect_settings');
	
	if ($_POST['cp_redirect-submit']) {
		$message = $message_updated;
		$newoptions['status'] = (bool)$_POST['cp_redirect-status'];
		$newoptions['system'] = $_POST['cp_redirect-previous_system'];
		$newoptions['custom_field'] = stripslashes(strip_tags($_POST['cp_redirect-custom_field']));
		$newoptions['id_match'] = (bool)$_POST['cp_redirect-id_match'];
	}
	if ($newoptions != $cp_redirect) {
		$cp_redirect = $newoptions;
		update_option('cp_redirect_settings', $cp_redirect);
	}
	
	?>
	
	<?php if ($message) : ?>
	<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
	<?php endif; ?>
	<div id="dropmessage" class="updated" style="display:none;"></div>
	
	<div class="wrap">
	
	<h2>CP Redirect settings</h2>
	<p>Redirect your old College Publisher URLs to your new WordPress URLs by configuring this plugin. The previous article ID will need to be stored in either a custom field or match the WordPress post ID.</p>
	
	<form method="post">
		
		<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
			
			<tr valign="top">
				<th scope="row">CP Redirect is:</th>
				<td><select name="cp_redirect-status">
					<option value="0" <?php if ($cp_redirect['status'] == false) echo 'selected="selected"'; ?>>Off</option>
					<option value="1" <?php if ($cp_redirect['status'] == true) echo 'selected="selected"'; ?>>On</option>
				</select></td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Previous CMS:</th>
				<td><select name="cp_redirect-previous_system">
					<option value="cp4" <?php if ($cp_redirect['system'] == 'cp4') echo 'selected="selected"'?>>College Publisher 4</option>
					<option value="cp5" <?php if ($cp_redirect['system'] == 'cp5') echo 'selected="selected"'?>>College Publisher 5</option>
				</select></td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Custom field for CP article IDs:</th>
				<td><input type="text" id="cp_redirect-custom_field" name="cp_redirect-custom_field" size="25" value="<?php echo $cp_redirect['custom_field']; ?>"/> or my WordPress post IDs match my College Publisher article IDs
					<input type="checkbox" id="cp_redirect-id_match" name="cp_redirect-id_match" <?php if ($cp_redirect['id_match'] == true) echo ' checked="checked" '; ?>/>
				</td>
			</tr>
			
		</table>
		
		<div class="submit">
			<input type="submit" name="cp_redirect-submit" id="cp_redirect-submit" class="button-primary" value="Save settings" />
		</div>
		
	</form>
	
	</div>
	
	<?php
	
}

function cp_redirect_menu() {
	add_options_page('CP Redirect Settings', 'CP Redirect', 8, __FILE__, 'cp_redirect_options');
}

// Runs with every page load. If the page is a 404 and CP Redirect is on, then it will process the incoming URL and attempt to build a redirect
function cp_redirect () {
	
	global $wpdb;
	$cp_redirect = get_option('cp_redirect_settings');
	
	// If it's a 404 page and CP Redirect is on, then attempt a redirect
	if (is_404() && $cp_redirect['status'] == true) {
		
		$requested_uri = $_SERVER['REQUEST_URI'];
		
		// Get the College Publisher article ID based on the URL
		if ($cp_redirect['system'] == 'cp4') {
			$article_name = basename($requested_uri, '.shtml');
			$article_name = explode('-', $article_name);
			$article_id = end($article_name);
		} else if ($cp_redirect['system'] == 'cp5') {
			$article_name = basename($requested_uri);
			$article_name = explode('.', $article_name);
			$article_id = end($article_name);
		}
		
		$custom_field = $cp_redirect['custom_field'];
			
		// If post ID doesn't match article ID, then try to find it in the custom field
		if ($cp_redirect['id_match'] == true) {
			$wp_id = $article_id;
		} else {
			$wp_id = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='$custom_field' AND meta_value='$article_id'", 'ARRAY_N');
			$wp_id = $wp_id[0][0]; // Catch if there are duplicate results in the database
		}

		// If we have a WordPress ID to work with and it's legit, then make the redirect
		if ($wp_id != null && get_post($wp_id) != null) {
			$website = get_bloginfo('url');
			$new_url = rtrim($website, '/');
			$new_url .= '/?p=' . $wp_id;
			wp_redirect($new_url, 301);
		} else {
			return;
		}
		
	}
	
}

// WordPress hooks and actions
add_action('admin_menu', 'cp_redirect_menu');
add_action('template_redirect', 'cp_redirect');

?>
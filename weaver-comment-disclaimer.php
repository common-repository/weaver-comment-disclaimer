<?php
/*
Plugin Name: Weaver Comment Disclaimer
Plugin URI: http://wpweaver.info/plugins/
Description: Display a disclaimer in replies to your site's comments. Allows CSS and basic HTML formatting. Use Settings->Weaver Comment Disclaimer menu.
Author: wpweaver
Author URI: http://wpweaver.info
Version: 1.0
License: GPL

GPL License: http://www.opensource.org/licenses/gpl-license.php

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
*/ 

function wpw_request_handler() {
	$allowedtags = array('a' => array('href' => array(),'title' => array(), 'target' => array()),
			'abbr' => array('title' => array()),'acronym' => array('title' => array()),
			'code' => array(), 'pre' => array(), 'em' => array(),'strong' => array(),
			'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array());
	
	if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
		if (get_option('wpw_disclaimer') == '') {
			add_option('wpw_disclaimer', 'Please enter your own text here.');
		}
	}
	if (!empty($_POST['wpw_action'])) {
		switch($_POST['wpw_action']) {
			case 'wpw_update_settings':
				if (isset($_POST['wpw_disclaimer'])) {
					$text = wp_kses($_POST['wpw_disclaimer'], $allowedtags);
					update_option('wpw_disclaimer', $text);
					header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=comment-disclaimer.php&updated=true');
					die();
				}
				break;
		}
	}
}
add_action('init', 'wpw_request_handler');

function wpw_show_disclaimer() {
	$text = get_option('wpw_disclaimer');
	if (!empty($text)) {
		echo('<div class="wpw-comment-disclaimer">'."$text".'</div>');
	}
}
add_action('comment_form', 'wpw_show_disclaimer', 99);

function wpw_options_admin() {
	print('
	<div class="wrap">
		<h2>'.__('Comment Disclaimer Options', 'comment-disclaimer').'</h2>
		<form id="wpw_commentdisclaimer" name="wpw_commentdisclaimer" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php" method="post">
			<fieldset class="options">
			<p>
				<label for="wpw_text">'.__('Comment Disclaimer:', 'comment-disclaimer').'</label>
				<br />
				<textarea cols="100" rows="10" name="wpw_disclaimer" id="wpw_disclaimer">'.get_option('wpw_disclaimer').'</textarea>
			</p>
			<input type="hidden" name="wpw_action" value="wpw_update_settings" />
			</fieldset>
			<p class="submit">
				<input type="submit" name="submit" value="'.__('Update Comment Disclaimer', 'comment-disclaimer').'" />
				<input type="hidden" name="wpw_action" value="wpw_update_settings" />
			</p>
		</form>
		<h2>'.__('FORMATTING', 'comment-disclaimer').'</h2>
		<h3>'.__('HTML', 'comment-disclaimer').'</h3>
		<p>'.__('You may use the following HTML tags: A, ABBR, ACRONYM, BR, CODE, EM, P, PRE, STRONG, UL, OL, LI.
			If you use need to use a "<" or  a ">", you may need to use "&amp;lt;" or "&amp;gt;" for proper display.', 'comment-disclaimer').'</p>
		<h3>'.__('Styling','comment-disclaimer').'</h3>
		<p>'.__('Your disclaimer text is wrapped in a DIV with the class ".wpw-comment-disclaimer", so you can add CSS styling if you wish.', 'comment-disclaimer').'
	</div>
	');
}

function wpw_disclaimer_admin_menu() {
	if (current_user_can('manage_options')) {
		add_options_page(
		    __('Comment Disclaimer', 'comment-disclaimer'), __('Comment Disclaimer', 'comment-disclaimer'),
		    10, basename(__FILE__), 'wpw_options_admin');
	}
}
add_action('admin_menu', 'wpw_disclaimer_admin_menu');
?>
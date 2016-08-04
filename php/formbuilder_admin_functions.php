<?php
/*
Created by the TruthMedia Internet Group
(website: truthmedia.com       email : webmaster@truthmedia.com)

Plugin Programming and Design by James Warkentin
http://www.warkensoft.com/about-me/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	// Allow showing an alert to the user when necessary.
	function formbuilder_admin_alert($msg = '', $msg2 = '')
	{
		if($msg2 AND $msg) echo "<div class='updated'><p><strong>$msg</strong><br/>$msg2</p></div>";
		elseif($msg) echo "<div class='updated'><p><strong>$msg</strong></p></div>";
	}
	
	// Allow showing an alert to the user when necessary.
	function formbuilder_admin_warning($msg = '', $msg2 = '')
	{
		if($msg2 AND $msg) echo "<div class='error'><p><strong>$msg</strong><br/>$msg2</p></div>";
		elseif($msg) echo "<div class='error'><p><strong>$msg</strong></p></div>";
	}

	function formbuilder_admin_css()
	{
		?>
		<link rel='stylesheet' href='<?php echo FORMBUILDER_PLUGIN_URL; ?>css/formbuilder_styles_admin.css' type='text/css' media='all' />
		<?php
	}

	function formbuilder_options_page($action=""){
		global $wpdb, $formbuilder_admin_nav_options;
		
		$version = get_option('formbuilder_version');

		// Determine and set path to current formbuilder page.
		$path = $_SERVER['REQUEST_URI'];
		
		$path_length = strpos($path, FORMBUILDER_FILENAME) + strlen(FORMBUILDER_FILENAME);
		
		$path = substr($path, 0, strpos($path, '?')) . '?page=' . FORMBUILDER_FILENAME;

		define("FB_ADMIN_PLUGIN_PATH", $path);

		if($version != FORMBUILDER_VERSION_NUM)
		{	// FormBuilder is NOT set up correctly with the proper version number.  Rerun the activation script.
			formbuilder_activation();
		}

		$version = get_option('formbuilder_version');
		
		$formbuilder_admin_nav_options = formbuilder_get_admin_nav_options();
		?>


		<div id="icon-tools" class="icon32"><br></div>
		<div class="wrap">
			<h2><?php _e('FormBuilder Management', 'formbuilder'); ?> (v <?php echo $version; ?>)</h2>
		<?php
		
		$problemThemes = array(
			'Thesis-disabled',
		);
		$theme_name = wp_get_theme()->get('Name');
		if(array_search($theme_name, $problemThemes) !== false)
			formbuilder_admin_warning(sprintf(__("WARNING: FormBuilder has known compatibility issues with the '%s' theme.", 'formbuilder'), $theme_name));
		
		if(!isset($_GET['fbaction'])) $_GET['fbaction'] = false;
		
		// Allow for alternate systems to do something with the action.
		// If nothing is returned, proceed with the regular built-in functions.
		$result = apply_filters('formbuilder_display_options_page', htmlentities($_GET['fbaction']));
		if(!empty($result))
			return;

		$entityFbId = htmlentities($_GET['fbid']);
		
		switch($_GET['fbaction']) {

			case "newForm":
				formbuilder_options_newForm();
			break;

			case "editForm":
				formbuilder_options_editForm($entityFbId);
			break;

			case "exportForm":
				formbuilder_options_exportForm($entityFbId);
			break;

			case "importForm":
				formbuilder_options_importForm();
			break;

			case "editFormObject":
				formbuilder_options_editFormObject($entityFbId);
			break;

			case "copyForm":
				formbuilder_options_copyForm($entityFbId);
			break;

			case "removeForm":
				formbuilder_options_removeForm($entityFbId);
			break;

			case "newResponse":
				formbuilder_options_newResponse();
			break;

			case "editResponse":
				formbuilder_options_editResponse($entityFbId);
			break;

			case "copyResponse":
				formbuilder_options_copyResponse($entityFbId);
			break;

			case "removeResponse":
				formbuilder_options_removeResponse($entityFbId);
				formbuilder_options_default();
			break;

			case "formResults":
				if(!isset($results_page)) $results_page = new formbuilder_xml_db_results();
				$results_page->show_adminpage();
			break;

			case "uninstall":
				if(!isset($_GET['confirm']))
					formbuilder_cleaninstall(false);
				else
					formbuilder_cleaninstall($_GET['confirm']);
			break;
			
			case "settings":
				formbuilder_options_settings();
			break;
			
			case "strings":
				formbuilder_options_strings();
			break;

			case "forms":
			default:
				if(!formbuilder_user_can('connect') AND formbuilder_user_can('manage'))
					formbuilder_options_settings();
				else
					formbuilder_options_default();
			break;

		}
		?>

		</div>

		<?php

	}
	
	function formbuilder_get_admin_nav_options()
	{
		$formbuilder_admin_nav_options = array();
		if(formbuilder_user_can('manage')) $formbuilder_admin_nav_options['settings'] = __("Settings", 'formbuilder');
		if(formbuilder_user_can('create')) $formbuilder_admin_nav_options['forms'] = __("Forms", 'formbuilder');
		if(formbuilder_user_can('create')) $formbuilder_admin_nav_options['formResults'] = __("Stored Results", 'formbuilder');
		if(formbuilder_user_can('manage'))$formbuilder_admin_nav_options['strings'] = __("Text Translations", 'formbuilder');
		
		$formbuilder_admin_nav_options = apply_filters('formbuilder_get_admin_nav_options', $formbuilder_admin_nav_options);
		
		return($formbuilder_admin_nav_options);
	}

	function formbuilder_admin_nav($selected = 'forms')
	{
		$formbuilder_nav_options = formbuilder_get_admin_nav_options();
		?>
		<?php if(isset($_GET['fbmsg']) AND $_GET['fbmsg'] != "") formbuilder_admin_alert(htmlentities(stripslashes($_GET['fbmsg']))); ?>
<div class="formbuilder-subnav">
	<ul class="subsubsub">
		<?php foreach( $formbuilder_nav_options as $key=>$value ) { ?>
		<li><a <?php if($selected == $key) { ?>class="current"<?php } ?> href="<?php echo FB_ADMIN_PLUGIN_PATH; ?>&fbaction=<?php echo $key; ?>"><?php echo $value; ?></a> |</li>
		<?php } ?>
		<?php do_action('formbuilder_display_nav'); ?>
		<li><a href="http://www.warkensoft.com/wordpress/formbuilder/documentation"><?php _e("Documentation", 'formbuilder'); ?></a></li>
	</ul>
</div>
		<?php
	}
	
	

	
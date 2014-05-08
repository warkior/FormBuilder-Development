<?php
/*
Plugin Programming and Design by James Warkentin
http://www.warkensoft.com/

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
if(!class_exists('FormBuilder_Extensions')) {
	
	class FormBuilder_Extensions 
	{
		var $formbuilder_dir = '';
		
		public static function GetInstance()
		{
			global $formbuilder_extensions_module;
			if(!$formbuilder_extensions_module)
				$formbuilder_extensions_module = new FormBuilder_Extensions();
			return($formbuilder_extensions_module);
		}
		
		function __construct()
		{
			$this->formbuilder_dir = dirname(dirname(__FILE__));
			
			add_filter('formbuilder_get_admin_nav_options', array(&$this, 'formbuilder_get_admin_nav_options'), 9999 );
			add_filter('formbuilder_display_options_page', array(&$this, 'formbuilder_display_options_page') );
		}
	
		function formbuilder_get_admin_nav_options($formbuilder_admin_nav_options)
		{
			$formbuilder_admin_nav_options['extensions'] = __("Extensions", 'formbuilder');
			return($formbuilder_admin_nav_options);
		}
		
		function formbuilder_display_options_page($action)
		{
			if($action == 'extensions')
			{
				include($this->formbuilder_dir . '/html/options_extensions.phtml');
				return(true);
			}
		}
	}
	FormBuilder_Extensions::GetInstance();
	
}
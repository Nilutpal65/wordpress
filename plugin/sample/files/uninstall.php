<?php
/**
 * Uninstall Script for Travelgrove Searchboxes
 *
 * @package Tg-Searchboxes
 * @subpackage Uninstall
 * @author Travelgrove Tech Team (http://labs.travelgrove.com/)
 * @since 1.0
 */
// If uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN'))
	exit();
// Delete tg_searchboxes_options from options table; the same action is also mapped to deactivation w/ register_deactivation_hook
delete_option('tg_searchboxes_options');
?>
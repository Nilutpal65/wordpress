<?php
/**
 * 160x600 box view for Editorial Interface for Admin pages (only)
 *
 * @package Travel-Search
 * @subpackage Admin Controller for Editorial Interface
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
// no direct loading of this file
	if( !defined('TG_SEARCHBOXES_ABSPATH') ) exit;
?>
<?php require_once ( TG_SEARCHBOXES_ABSPATH.'classes/tgSearchboxesRenderer.class.php' ); ?>
<?php
	$atts = array( 'options' => '{"size":"160x600", "ajaxSettings":true}');
	$tgSearchboxesRenderer = new tgSearchboxesRenderer($this, $atts);
	echo $tgSearchboxesRenderer->renderSearchboxes();
?>
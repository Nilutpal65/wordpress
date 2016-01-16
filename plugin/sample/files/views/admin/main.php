<?php
/**
 * main template/view for Admin pages (only)
 *
 * @package Travel-Search
 * @subpackage Admin Controller
 * @author Travelgrove Labs (http://labs.travelgrove.com/)
 * @since 1.0
 */
/*	no direct loading of this file	*/
if( !defined('TG_SEARCHBOXES_ABSPATH') )
	exit();
?>
<div class="wrap">
	<?php if(!empty($tg_sb_header))		include_once $tg_sb_header; ?>
	<?php if(!empty($tg_sb_content))	include_once $tg_sb_content; ?>
	<?php if(!empty($tg_sb_footer))		include_once $tg_sb_footer; ?>
</div>
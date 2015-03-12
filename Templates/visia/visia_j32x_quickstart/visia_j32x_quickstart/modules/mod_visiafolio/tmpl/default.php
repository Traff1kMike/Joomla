<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_visiafolio default layout as portfolio
 * based on Visia Portfolio AetherThemes from http://www.aetherthemes.com/
 * Joomlified by Erwin Schro from joomla-labs.com
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$doc 	= JFactory::getDocument();

$script = "
jQuery(document).ready(function(){
	//PORTFOLIO FILTER
	jQuery(function(){
		jQuery('#portfolio-grid').mixitup({
			effects: ['fade','scale','rotateX'],
			easing: 'snap'
		});
	});
	// Portfolio detail slider

});
";
$doc->addScriptDeclaration($script);

?>
<div class="content container">
	<div class="title grid-full">
		<h2><?php echo $module->title; ?></h2>
		<span class="border"></span>
	</div>

	<?php if ($show_filter) { ?>
	<ul class="filtering grid-full">
		<li class="filter active" data-filter="all">All</li>
		<?php foreach($cats as $cat) { ?>
		<li class="filter" data-filter="<?php echo modVisiaFolioHelper::slug($cat); ?>">
			<?php echo ucfirst(trim($cat)); ?>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
</div>

<!-- Ajax Section -->
<div class="ajax-section container">
	<div class="loader"></div>
	<div class="project-navigation">
		<ul>
			<li class="nextProject"><a href="#"></a></li>
			<li class="prevProject"><a href="#"></a></li>
		</ul>
	</div>
	<div class="closeProject">
		<a href="#"><i class="icon-remove"></i></a>               
	</div>
	<div class="ajax-content clearfix"></div>
</div>
<!-- End Ajax Section -->

<!-- Thumbnails -->
<ul id="portfolio-grid" class="projectlist clearfix">
			
	<?php 
	foreach ($items as $index=>$item)  {  
	?>

	<li class="project mix <?php echo modVisiaFolioHelper::slug($item->tag_alias); ?> mix_all">

		<a href="<?php echo '#!'.$item->link; ?>">
			
			<?php echo $item->image; ?>
			
			<div class="projectinfo">
				<div class="meta">
				<?php if ($show_title) { ?><h4><?php echo $item->title; ?></h4><?php } ?>
				<?php if ($show_category) { ?><h6><em><?php echo $item->tag; ?></em></h6><?php } ?>
				<?php if ($show_introtext) { ?>
					<!--<p class="intro"><?php echo $item->displayIntrotext; ?></p>-->
					<!--<p><a href="<?php echo $item->link; ?>"><?php echo JText::_('Read more...'); ?></a></p>-->
				<?php } ?>
				</div>
			</div>
		</a>
	</li>
	<?php } /* end foreach */ ?>

</ul>

<?php /*if ($show_tagline) { ?><h3><?php echo $tagline; ?></h3><?php /*}*/ ?>
<?php /*if ($show_desc) { ?><p><?php echo $desc; ?></p><?php /*}*/ ?>


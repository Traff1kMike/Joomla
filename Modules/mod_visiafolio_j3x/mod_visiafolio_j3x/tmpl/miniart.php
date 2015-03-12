<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_visiafolio miniarticle layout
 * based on Visia Portfolio AetherThemes from http://www.aetherthemes.com/
 * Joomlified by Erwin Schro from joomla-labs.com
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$doc 	= JFactory::getDocument();

$show_author = (bool) $params->get('show_author', 0);
$show_readmore = false;
$show_thumb = (bool) $params->get('show_thumb', 1);
$show_introtext = (bool) $params->get('show_introtext', 0);

$isFlickrStyle = (!$show_title && !$show_category && !$show_introtext && !$show_date && !$show_author);
if ( $isFlickrStyle ) {
	$articlesection_cls = ' class="col"';
	$doc->addStyleDeclaration(".ac > article .col { float: left; padding: 0; width: '".$thumb_width."'; }");
} else {
	$articlesection_cls = '';
}

?>

<?php if ( $isFlickrStyle ) { ?>
<div class="artgallery">
	<ul>
<?php } else { ?>
<div class="ac image-left" typeof="Article">
<?php } ?>

	<?php foreach ($items as $index=>$item) { ?>
	
	<?php if ( $isFlickrStyle ) { ?>
	<?php } else { ?>
		<article<?php echo $articlesection_cls; ?>>
	<?php } ?>

		<?php if ($show_thumb) { ?>
		<?php if ( $isFlickrStyle ) { ?><li><?php } else { ?>
	    <div class="image-feat" property="image" style="width:<?php echo $thumb_width; ?>;"> 
	    <?php } ?>

			<a class="screen-roll" href="<?php echo $item->link; ?>">
				<span></span>

				<?php echo $item->image; ?>
				
			</a> 
			<!--<p class="caption">This is a small image caption, 25 words or less is best.</p>-->
		<?php if ( $isFlickrStyle ) {
			?></li><?php } else { ?>
			</div>
		<?php } /* end non flickr style check */?>

		<?php } /* end image-feat class */?>

		<?php if ( !$isFlickrStyle ) { ?>
			<header> 
				<?php if ($show_title) { ?><a href="<?php echo $item->link; ?>"><h4 property="headline"><?php echo $item->title; ?></h4></a><?php } ?>
				<?php if ($show_category) { ?><h6 property="keywords"><a href="<?php echo $item->CategoryBlogLink; ?>"><?php echo $item->tag; ?></a></h6><?php } ?>
			</header>

			<?php if ($show_introtext) { ?>
			<div class="content" property="articleBody"> 
				<p><?php echo $item->displayIntrotext; ?></p>
			</div>
			<?php } ?>

			<?php if ($show_date || $show_author /*|| $show_readmore*/) { ?>
			<footer>
				<!--<a class="rm" href="<?php echo $item->link; ?>"><?php echo JText::_('Read more...'); ?></a>-->
				<?php if ($show_date || $show_author) { ?>
				<ul class="meta">
					<?php if ($show_date) { ?><li property="datePublished"><?php echo $item->created; ?></li><?php } ?>
					<?php if ($show_author) { ?><li property="author">By <?php echo $item->author; ?></li><?php } ?>
				</ul>
				<?php } ?>
			</footer>
			<?php } ?>
		<?php } ?>

	<?php if ( $isFlickrStyle ) { ?>
	<?php } else { ?>
	</article>
	<?php } ?>

	<?php } /* end foreach */ ?>

<?php if ( $isFlickrStyle ) { ?>
	</ul>
	<div class="clearfix"></div>
</div>
<?php } else { ?>
</div>
<?php } ?>


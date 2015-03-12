<?php
/**
 * @version		$Id: default.php 2013-08-11 10:48:48Z schro $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$app = JFactory::getApplication();

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.caption');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
$pageClass = $this->params->get('pageclass_sfx');

?>

<div class="blog featured <?php echo $this->pageclass_sfx;?>">

	<?php if ( $this->params->get('show_page_heading')!=0) { ?>
	<div id="blog-title">
		<div class="container">
	
			<!-- Title -->
			<div class="grid-6">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			</div>
			<!-- End Title -->
		</div>
	</div>
	<?php } ?>

	<?php $leadingcount=0; ?>

	<?php if (!empty($this->lead_items)) { ?>
		
		<?php foreach ($this->lead_items as &$item) { ?>
			
			<div class="post<?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			
			<?php $leadingcount++; ?>
		
		<?php } ?>
	
	<?php } ?>

	<?php
		$introcount=(count($this->intro_items));
		$counter=0;

	if (!empty($this->intro_items)) { ?>

		<?php foreach ($this->intro_items as $key => &$item) { ?>

		<?php
		/*
			$key = ($key-$leadingcount)+1;
			$rowcount =( ((int)$key-1) % (int) $this->columns) +1;
			$row = $counter / $this->columns;
		*/
		?>
			<div class="post<?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
			
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
			
			</div>
			<?php /*$counter++;*/ ?>
			
		<?php } ?>
	<?php } ?>

	<?php if (!empty($this->link_items)) : ?>
	<div class="post">
		<?php echo $this->loadTemplate('links'); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<div class="grid-4">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php  if ($this->params->def('show_pagination_results', 1)) : ?>
		<div class="counter grid-2">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</div>
		<?php endif; ?>
		
	</div>
	<?php endif; ?>

</div><!-- end blog featured -->


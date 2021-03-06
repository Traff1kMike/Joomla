<?php
/**
 * @version		$Id: default_item.php 20196 2013-08-11 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
$canEdit	= $this->item->params->get('access-edit');
?>

<?php if ($this->item->state == 0) { ?>
<div class="system-unpublished">
<?php } ?>

<?php if ( $params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_publish_date') ) { ?>
<span class="date">
	<?php 
	if ($params->get('show_create_date')) {
		echo JHTML::_('date', $this->item->created, JText::_('d')) . '<br/>'; 
		echo '<small>'.JHTML::_('date', $this->item->created, JText::_('M')).'</small>';
		//echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC3'))); 
	}
	 
	if ($params->get('show_publish_date')) {
		//echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); 
		echo JHTML::_('date', $this->item->publish_up, JText::_('d')) . '<br/>'; 
		echo '<small>'.JHTML::_('date', $this->item->publish_up, JText::_('M')).'</small>';
	} 
	
	?>
</span>
<?php } ?>

<?php  if (isset($images->image_intro) and !empty($images->image_intro)) { ?>
<div class="post-media">
				
	<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>

	<img
		<?php if ($images->image_intro_caption) {
			echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
		} ?>
		src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"
	/>
		
</div>
<?php } ?>


<div class="post-title">
	<?php if ($params->get('show_title')) { ?>
	<h2>
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
		<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
			<?php echo $this->escape($this->item->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->item->title); ?>
		<?php endif; ?>
	</h2>
	<?php } ?>

	<?php if (!$params->get('show_intro')) { ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php } ?>

	
	<?php // to do not that elegant would be nice to group the params ?>

	<?php 
	if ( ($params->get('show_author')) or ($params->get('show_modify_date')) or ($params->get('show_hits'))
		 or ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) ) { ?>

	<div class="post-meta">
		

		<?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) { ?>
		<ul class="actions">
			<?php if ($params->get('show_print_icon')) : ?>
			<li><?php echo JHtml::_('icon.print_popup', $this->item, $params); ?></li>
			<?php endif; ?>
			<?php if ($params->get('show_email_icon')) : ?>
			<li><?php echo JHtml::_('icon.email', $this->item, $params); ?></li>
			<?php endif; ?>
			<?php if ($canEdit) : ?>
			<li><?php echo JHtml::_('icon.edit', $this->item, $params); ?></li>
			<?php endif; ?>
		</ul>
		<?php } ?>

		<h6>
		<?php 
		if ($params->get('show_author') && !empty($this->item->author )) {
			
			$author =  $this->item->author;
			$author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);
		
			if (!empty($this->item->contactid ) &&  $params->get('link_author') == true) {
				echo JText::sprintf('TPL_VISIA_COM_CONTENT_WRITTEN_BY' , 
				JHTML::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid),$author));
			} else {
				echo JText::sprintf('TPL_VISIA_COM_CONTENT_WRITTEN_BY', $author);
			}
	
		}
		
		if ($params->get('show_category')) {
		
			if ($params->get('show_parent_category') && $this->item->parent_id != 1 ) {
				$title = $this->escape($this->item->parent_title);
				$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';
				if ($params->get('link_parent_category') AND $this->item->parent_slug) {
					//echo JText::sprintf('COM_CONTENT_PARENT', $url);
					echo ' / '.$url;
				} else {
					echo ' / '. JText::sprintf('COM_CONTENT_PARENT', $title);
				}
			}
			
			$title = $this->escape($this->item->category_title);
			$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
			if ($params->get('link_category') AND $this->item->catslug) {
				//echo JText::sprintf('COM_CONTENT_CATEGORY', $url);
				echo ' / ' . $url;
			} else {
				echo ' / ' . JText::sprintf('COM_CONTENT_CATEGORY', $title);
			}
			
		} 
		?>

		<?php
		if ($params->get('show_modify_date')) {
			echo ' / '; 
			echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC3')));
			//echo JHTML::_('date', $this->item->modified, JText::_('M d')); 
		}
		?>

		<?php if ($params->get('show_hits')) { 
			echo ' / '; 
			echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits);
		} ?>
		</h6>


		<?php echo $this->item->event->beforeDisplayContent; ?>
	</div>
	<?php } ?>
</div>

<!-- article content start -->
<div class="post-body">

	<?php echo $this->item->introtext; ?>

	<?php if ($params->get('show_readmore') && $this->item->readmore) {
		if ($params->get('access-view')) {
			$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		} else {
			$menu = JFactory::getApplication()->getMenu();
			$active = $menu->getActive();
			$itemId = $active->id;
			$link1 = JRoute::_('index.php?option=com_users&view=login&&Itemid=' . $itemId);
			$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
			$link = new JURI($link1);
			$link->setVar('return', base64_encode($returnURL));
		}
	?>
			
		<a href="<?php echo $link; ?>" class="button readmore">
			<?php if (!$params->get('access-view')) :
			echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
			elseif ($readmore = $this->item->alternative_readmore) :
				echo $readmore;
				echo JHTML::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			elseif ($params->get('show_readmore_title', 0) == 0) :
				echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
			else :
				echo JText::_('COM_CONTENT_READ_MORE');
				echo JHTML::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif; ?>
		</a>
		
	<?php } ?>
	
	<?php echo $this->item->event->afterDisplayContent; ?>
	
</div><!-- end post-body -->

	
<?php if ($this->item->state == 0) { ?>
</div>
<?php } ?>



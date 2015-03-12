<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.caption');

$params = $this->params;

?>

<div class="rsdir">
	<div class="row-fluid">
		<div class="<?php echo htmlspecialchars($this->pageclass_sfx); ?>">
			<?php if ( $params->get('show_page_heading') ) { ?>
				<div class="page-header">
					<h1><?php echo $this->escape( $params->get('page_heading') ); ?></h1>
				</div>
			<?php } ?>
				 
			<?php if ( !JFactory::getApplication()->getMenu()->getActive() ) { ?>
				<div class="page-header">
					<h1><?php echo JText::_('COM_RSDIRECTORY_FAVORITES'); ?></h1>
				</div>
			<?php } ?>
				 
			<form id="adminForm" class="form-inline" action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post">
					
				<?php if ( $params->get('show_ordering') ) { ?>
				<fieldset class="control-group rsdir-filter-bar">
					<?php echo $this->sort_dir_field; ?>
					<?php echo $this->sort_field; ?>
				</fieldset>
				<?php } ?>
					
				<?php if (!$this->items) { ?>
				<div class="alert alert-info"><?php echo JText::_('COM_RSDIRECTORY_NO_FAVORITE_ENTRIES'); ?></div>	
				<?php } ?>
					
				<?php
				$this->addTemplatePath(JPATH_COMPONENT_SITE . '/views/myentries/tmpl/');
				echo $this->loadTemplate('items');
				?>
			</form>
		</div>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->
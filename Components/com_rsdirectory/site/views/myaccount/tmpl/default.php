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
		<div class="<?php echo $this->pageclass_sfx;?>">
			<?php if ( $this->params->get('show_page_heading') ) { ?>
			<div class="page-header">
				<h1><?php echo $this->escape( $params->get('page_heading') ); ?></h1>
			</div>
			<?php } ?>
				
			<?php if ( !JFactory::getApplication()->getMenu()->getActive() ) { ?>
			<div class="page-header">
				<h1><?php echo JText::_('COM_RSDIRECTORY_YOUR_ACCOUNT'); ?></h1>
			</div>
			<?php } ?>
				
			<?php
				
			// GENERAL.
			$this->rstabs->addTitle('COM_RSDIRECTORY_GENERAL', 'general');
			$this->rstabs->addContent( $this->loadTemplate('general') );
				
			// TRANSACTIONS.
			$this->rstabs->addTitle('COM_RSDIRECTORY_TRANSACTIONS', 'transactions');
			$this->rstabs->addContent( $this->loadTemplate('transactions') );
				
			// CREDITS HISTORY.
			$this->rstabs->addTitle('COM_RSDIRECTORY_CREDITS_HISTORY', 'credits-history');
			$this->rstabs->addContent( $this->loadTemplate('credits_history') );
				
			// Render the tabs.
			$this->rstabs->render();
				
			?>
		</div>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->
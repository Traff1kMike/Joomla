<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$params = $this->params;
$field = $this->field;

?>

<div class="rsdir">
	<div class="row-fluid">
		<div class="<?php echo $this->pageclass_sfx;?>">
				
			<?php if ( $params->get('show_page_heading') ) { ?>
				<div class="page-header">
					<h1><?php echo $this->escape( $params->get('page_heading') ); ?></h1>
				</div>
			<?php } ?>
				
			<?php
				
			if ( $params->get('show_description') )
			{
				echo $params->get('description');
			}
				
			if ($this->items)
			{
				$span = 'span' . ( 12 / $params->get('num_columns', 3) );
					
				?>
					
				<div class="rsdir-categories-list">
					
				<?php foreach ($this->items as $row) { ?>
						
					<div class="row-fluid">
						
					<?php foreach ($row as $item) { ?>
							
						<div class="<?php echo $span; ?>">
								
							<div class="media">
									
								<div class="media-body">
									<h5 class="media-heading">
										<?php
										$uri = new JUri( JRoute::_( "index.php?option=com_rsdirectory&view=entries" . ($this->Itemid ? "&Itemid=$this->Itemid" : '') ) );
										$uri->setVar( "f[$field->form_field_name]", urlencode($item->value) );
										?>
										<a href="<?php echo htmlspecialchars( $uri->toString() ); ?>"><?php echo $this->escape($item->text); ?></a>
										<?php echo $params->get('num_entries') ? ' (' . $item->entries_count . ')' : ''; ?>
									</h5>
								</div>
							</div>
								
						</div>
							
					<?php } ?>
						
					</div>
						
				<?php } ?>
					
				</div><!-- .rsdir-categories-list -->
					
			<?php } ?>
				
		</div>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->
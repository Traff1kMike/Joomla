<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

if ( $app->isSite() )
{
    JSession::checkToken('get') or die( JText::_('JINVALID_TOKEN') );
}

if ( RSDirectoryHelper::isJ30() )
{
	JHtml::_('bootstrap.tooltip');	
}

$function = $app->input->getCmd('function', 'jSelectEntry');
$listOrder = $this->escape( $this->state->get('list.ordering') );
$listDirn = $this->escape( $this->state->get('list.direction') );

?>

<div class="rsdir">
	<form id="adminForm" class="form-inline" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=entries&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" method="post">
			
		<fieldset class="filter clearfix">
				
			<div class="btn-toolbar">
					
				<div class="btn-group pull-left">
					<label for="filter_search">
						<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
					</label>
				</div>
					
				<div class="btn-group pull-left">
					<input id="filter_search" type="text" name="filter_search" value="<?php echo $this->escape( $this->state->get('filter.search') ); ?>" size="30" title="<?php echo JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'); ?>" />
				</div>
					
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?>
					</button>
					<button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
						<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?>
					</button>
				</div>
					
				<?php if ( $app->isAdmin() ) { ?>
					<button class="btn" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('0', '<?php echo $this->escape( addslashes( JText::_('COM_RSDIRECTORY_SELECT_AN_ENTRY') ) ); ?>', null, null);" type="button"><?php echo JText::_('JNONE'); ?></button>
				<?php } ?>
			</div>
				
			<hr class="hr-condensed" />
				
			<div class="filters pull-left">
				<select name="filter_published" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
					<?php echo JHtml::_( 'select.options', $this->status_options, 'value', 'text', $this->state->get('filter.published'), true ); ?>
				</select>
					
				<select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_( 'select.options', JHtml::_('category.options', 'com_rsdirectory', array( 'filter.language' => array( '*', $this->state->get('filter.forcedLanguage') ) ) ), 'value', 'text', $this->state->get('filter.category_id') );?>
				</select>
			</div>
		</fieldset>
			
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'e.title', $listDirn, $listOrder); ?>
					</th>
					<th width="20%" class="center nowrap">
						<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'e.category_id', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="center nowrap">
						<?php echo JHtml::_('grid.sort', 'JDATE', 'e.created_time', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'e.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape( addslashes($item->title) ); ?>', '<?php echo $this->escape( $item->category_id ); ?>', null);">
							<?php echo $this->escape($item->title); ?>
						</a>
					</td>
					<td class="center">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="center nowrap">
						<?php echo JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?>
					</td>
					<td class="center">
						<?php echo $item->id; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
			
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
			
	</form>
</div><!-- .rsdir -->
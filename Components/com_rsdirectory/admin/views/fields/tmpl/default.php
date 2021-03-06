<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape( $this->state->get('list.ordering') );
$listDirn = $this->escape( $this->state->get('list.direction') );
$saveOrder = $listOrder == 'ff.ordering';
$ordering = $listOrder == 'ff.ordering';
$form_id = $this->state->get('filter.form');

if ($saveOrder && $this->isJ30)
{
    $saveOrderingUrl = "index.php?option=com_rsdirectory&task=fields.saveOrderAjax&tmpl=component&form_id=$form_id";
    JHtml::_( 'sortablelist.sortable', 'fields-list', 'adminForm', strtolower($listDirn), $saveOrderingUrl );
}

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'field.add')
        {
            <?php RSDirectoryHelper::openModalWindow( JRoute::_('index.php?option=com_rsdirectory&view=fieldtypes&tmpl=component', false) ); ?>
                
            return false;
        }
        else if (task == 'fields.assign2form')
        {
            <?php RSDirectoryHelper::openModalWindow( JRoute::_('index.php?option=com_rsdirectory&view=forms&layout=modal&tmpl=component', false) ); ?>
                
            return false;
        }
            
        Joomla.submitform( task, document.getElementById('adminForm') );
    }
        
    function newField(field_id)
    {
        document.getElementById('field_type_id').value = field_id;
            
        Joomla.submitform( 'field.add', document.getElementById('adminForm') );
    }
        
    function assign2Form(form_id)
    {
        cid = jQuery( document.getElementById('adminForm') ).find('input[name="cid[]"]:checked');
            
        if (cid.length == 0)
            return;
            
        var field_ids = [];
            
        cid.each(function(index, element)
        {
            field_ids.push(element.value);
        });
            
        data = {
            field_ids: field_ids,
            form_id: form_id,
        };
        data[rsdir.token] = 1;
            
        jQuery.ajax(
        {
            type: 'POST',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=fields.assign2FormAjax&tmpl=component&random=' + Math.random(),
            data: data,
            success: function(response)
            {
                jQuery( document.getElementById('system-message-container') ).replaceWith(response);
                SqueezeBox.close();
            },
        });
    }
</script>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=fields'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table id="fields-list" class="adminlist table table-striped">
            <thead>
                <tr>
                    <?php if ($this->isJ30) { ?>
                    <th width="1%" class="nowrap center hidden-phone">
                    <?php } else { ?>
                    <th width="100">
                    <?php } ?>
                    <?php if ($form_id) { ?>
                        <?php if ($this->isJ30) { ?>
                            <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ff.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                        <?php } else { ?>
                            <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ff.ordering', $listDirn, $listOrder); ?>
                            <?php if ($saveOrder) { ?>
                                <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'fields.saveorder'); ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
                    <?php } ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_NAME', 'f.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TYPE', 'ft.type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_SEARCHABLE_SIMPLE', 'searchable_simple', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_SEARCHABLE_ADVANCED', 'searchable_advanced', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CORE_FIELD', 'ft.core', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REQUIRED', 'f.required', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CREDITS', 'credits', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'f.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="order nowrap center hidden-phone">
                            <?php if ($this->isJ30) { ?>
                                <?php
                                $disableClassName = '';
                                $disabledLabel = '';
                                    
                                if (!$saveOrder)
                                {
                                    $disabledLabel = JText::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                }
                                ?>
                                <span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                                    <i class="icon-menu"></i>
                                </span>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo isset($item->ordering) ? $item->ordering : 0; ?>" class="width-20 text-area-order " />
                            <?php } else { ?>
                                <?php if ($saveOrder) { ?>
                                    <?php if ($listDirn == 'asc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'fields.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'fields.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } elseif ($listDirn == 'desc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'fields.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'fields.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } ?>
                                <?php } ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo isset($item->ordering) ? $item->ordering : 0;?>" <?php echo $disabled ?> class="text-area-order" />
                            <?php } ?>
                        </td>
                        <td class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="nowrap center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'fields.'); ?></td>
                        <td class="nowrap"><a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=field.edit&id=$item->id"); ?>"><?php echo $this->escape($item->name); ?></a></td>
                        <td class="center nowrap">
                        <?php
                        if ($item->type == 'section_break')
                        {
                            $break_type = $this->config->get('break_type');
                            
                            echo JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($break_type) );
                        }
                        else
                        {
                            echo JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($item->type) );
                        }
                        ?>
                        </td>
                        <td class="center small nowrap hidden-phone hidden-tablet">
                        <?php
                        if ( isset($item->searchable_simple) )
                        {
                            echo JText::_($item->searchable_simple ? 'JYES' : 'JNO');
                        }
                        else
                        {
                            echo '-';
                        }
                        ?>
                        </td>
                        <td class="center small nowrap hidden-phone hidden-tablet">
                        <?php
                        if ( isset($item->searchable_advanced) )
                        {
                            if ($item->searchable_advanced)
                            {
                                if ($item->searchable_advanced == 1)
                                {
                                    echo JText::_('JYES');
                                }
                                else
                                {
                                    echo JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($item->searchable_advanced) );
                                }
                            }
                            else
                            {
                                echo JText::_('JNO');
                            }
                        }
                        else
                        {
                            echo '-';
                        }
                        ?>
                        </td>
                        <td class="center small nowrap hidden-phone"><?php echo JText::_( empty($item->core) ? 'JNO' : 'JYES' ); ?></td>
                        <td class="center small nowrap hidden-phone"><?php echo JText::_( empty($item->required) ? 'JNO' : 'JYES' ); ?></td>
                        <td class="center small nowrap hidden-phone"><?php echo is_numeric($item->credits) ? $item->credits : '-'; ?></td>
                        <td class="nowrap center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
            
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <input id="field_type_id" type="hidden" name="field_type_id" value="" /><!-- Used when creating a new field. -->
        <?php if (!$this->isJ30) { ?>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
        <?php } ?>
            
    </div><!-- .span10 -->
</form>
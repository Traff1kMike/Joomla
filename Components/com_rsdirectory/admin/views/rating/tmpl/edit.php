<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if ( task == 'review.cancel' || document.formvalidator.isValid( document.id('review') ) )
        {
            Joomla.submitform( task, document.getElementById('review') );
        }
    }
</script>

<div class="rsdir">
    <form id="review" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=rating&layout=edit&id=' . (int)$this->id ); ?>" method="post">
            
        <?php
            
        if ( !empty($this->item) )
        {
            $item = $this->item;
                
            echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_REVIEW_DETAILS_LABEL') );
                
            ?>
                
            <table class="table table-striped">
                    
                <tbody>
                        
                    <tr>
                        <th width="100"><?php echo JText::_('COM_RSDIRECTORY_ENTRY'); ?></th>
                        <td>
                            <a href="<?php echo JText::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                        </td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_ENTRY_AUTHOR'); ?></th>
                        <td>
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->entry_author_id"); ?>">
                                <?php echo $this->escape($item->entry_author); ?>        
                            </a>
                        </td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_IP'); ?></th>
                        <td><?php echo $this->escape($item->ip); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_DATE_CREATED'); ?></th>
                        <td><?php echo $item->created_time; ?></td>
                    </tr>
                        
                </tbody>
                    
            </table>
                
            <?php
                
            echo $this->rsfieldset->getFieldsetEnd();
        }
            
        echo $this->rsfieldset->getFieldsetStart( JText::_(empty($this->item) ? 'COM_RSDIRECTORY_ADD_REVIEW_LABEL' : 'COM_RSDIRECTORY_EDIT_REVIEW_LABEL') );
            
        foreach ($this->form->getFieldset('general') as $field)
        {
            echo $this->rsfieldset->getField($field->label, $field->input);
        }
            
        echo $this->rsfieldset->getFieldsetEnd();
            
        ?>
            
        <input type="hidden" name="task" value="" />
        <?php echo JHTML::_('form.token') . "\n"; ?>
            
    </form><!-- #review -->
</div><!-- .rsdir -->
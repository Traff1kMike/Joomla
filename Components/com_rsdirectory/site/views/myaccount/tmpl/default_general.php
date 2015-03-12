<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$itemid = $this->params->get('itemid');

?>

<table class="table table-striped">
    <tbody>
        <tr>
            <th width="160"><?php echo JText::_('COM_RSDIRECTORY_CURRENT_CREDITS'); ?></th>
            <td><?php echo $this->current_credits === 'unlimited' ? JText::_('COM_RSDIRECTORY_UNLIMITED') : $this->escape($this->current_credits); ?></td>
        </tr>
            
        <tr>
            <th><?php echo JText::_('COM_RSDIRECTORY_SPENT_CREDITS'); ?></th>
            <td><?php echo $this->escape($this->spent_credits); ?></td>
        </tr>
            
        <tr>
            <th><?php echo JText::_('COM_RSDIRECTORY_POSTED_ENTRIES'); ?></th>
            <td><a href="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&' . ($itemid ? "Itemid=$itemid" : 'view=myentries') ); ?>"><?php echo $this->escape($this->posted_entries); ?></a></td>
        </tr>
    </tbody>
</table>

<form id="adminForm" class="form-horizontal" action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post" enctype="multipart/form-data">
    <?php
        
    echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_ACCOUNT_SETTINGS') );
        
    foreach ($this->form->getFieldset('general') as $field)
    {
        echo $this->rsfieldset->getField($field->label, $field->input);
    }
        
    echo $this->rsfieldset->getFieldsetEnd();
        
    ?>
        
    <button class="btn btn-primary" type="submit"><?php echo JText::_('JSAVE'); ?></button>
        
    <div>
        <input type="hidden" name="task" value="myaccount.save" />
        <?php echo JHTML::_('form.token') . "\n"; ?>
    </div>
        
</form>
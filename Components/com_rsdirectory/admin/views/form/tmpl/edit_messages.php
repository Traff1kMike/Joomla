<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

// The Listing Row Layout fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_ENTRY_SUBMIT') );

foreach ($this->form->getFieldset('entry_submit') as $field)
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

$placeholders = RSDirectoryHelper::getGlobalPlaceholdersHTML() . 
                RSDirectoryHelper::getUserPlaceholdersHTML() .
                RSDirectoryHelper::getCreditsPlaceholdersHTML() .
                RSDirectoryHelper::getEntryGeneralPlacehodlersHTML() .
                    
                '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CUSTOM_FIELDS_TITLE') . '</h4>' .
                RSDirectoryHelper::getCustomFieldsPlaceholdersHTML(0, $this->id);
                
echo $this->rsfieldset->getField( JText::_('COM_RSDIRECTORY_PLACEHOLDERS'), $placeholders );

echo $this->rsfieldset->getFieldsetEnd();
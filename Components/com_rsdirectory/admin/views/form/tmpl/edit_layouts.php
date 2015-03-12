<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


// The Listing General Layout fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_LISTING_GENERAL_LAYOUT') );

foreach ( $this->form->getFieldset('listing_general_layout') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

$placeholders = RSDirectoryHelper::getGlobalPlaceholdersHTML() .
                RSDirectoryHelper::getUserPlaceholdersHTML() .
                RSDirectoryHelper::getEntryGeneralPlacehodlersHTML() . 
                '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CUSTOM_FIELDS_TITLE') . '</h4>' .
                RSDirectoryHelper::getCustomFieldsPlaceholdersHTML(0, $this->id);
                
echo $this->rsfieldset->getField( JText::_('COM_RSDIRECTORY_PLACEHOLDERS'), '<div id="entry-titles-placeholders">' . $placeholders . '</div>' );

echo $this->rsfieldset->getFieldsetEnd();


// The Listing Row Layout fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_LISTING_ROW_LAYOUT') );

$table = RSDirectoryHelper::getTableStructure( array_values( $this->form->getFieldset('listing_row_layout') ), 2, 'cols' );

echo '<div class="row-fluid">';

foreach ($table as $cells)
{
    echo '<div class="span6">';
        
    foreach ($cells as $field)
    {
        if ($field)
        {
            echo $this->rsfieldset->getField($field->label, $field->input);    
        }
    }
        
    echo '</div>';
}
    
echo '</div>';

echo $this->rsfieldset->getFieldsetEnd();


// The Listing Detail Layout fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_LISTING_DETAIL_LAYOUT') );

$table = RSDirectoryHelper::getTableStructure( array_values( $this->form->getFieldset('listing_detail_layout') ), 2, 'cols' );

echo '<div class="row-fluid">';

foreach ($table as $cells)
{
    echo '<div class="span6">';
        
    foreach ($cells as $field)
    {
        if ($field)
        {
            echo $this->rsfieldset->getField($field->label, $field->input);    
        }
    }
        
    echo '</div>';
}
    
echo '</div>';

echo $this->rsfieldset->getFieldsetEnd();


// The Listing Detail Custom Fields fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_LISTING_DETAIL_CUSTOM_FIELDS') );

foreach ( $this->form->getFieldset('listing_detail_custom_fields') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();
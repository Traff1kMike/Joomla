<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


// The GENERAL fieldset.
echo $this->rsfieldset->getFieldsetStart();

foreach ( $this->form->getFieldset('general') as $field )
{
    // Skip the JomSocial activities field if JomSocial is not found.
    if ( $field->fieldname == 'jomsocial_activities' && !file_exists(JPATH_SITE . '/components/com_community/libraries/core.php') )
        continue;
        
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();

// The WATERMARK fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_CONTACT') );

foreach ( $this->form->getFieldset('contact') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();
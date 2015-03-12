<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


// The WATERMARK fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_WATERMARK') );

foreach ( $this->form->getFieldset('watermark') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();


// The IMAGES LAYOUT fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_IMAGES_LAYOUT') );

foreach ( $this->form->getFieldset('images_layout') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();


// The THUMBNAILS fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_THUMBNAILS') );

foreach ( $this->form->getFieldset('thumbnails') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


// The CAPTCHA fieldset.
echo $this->rsfieldset->getFieldsetStart();

foreach ( $this->form->getFieldset('captcha') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();


// The BUILT-IN CAPTCHA fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_BUILT_IN_CAPTCHA_SETTINGS') );

foreach ( $this->form->getFieldset('built_in_captcha') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();


// The RECAPTCHA fieldset.
echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_RECAPTCHA_SETTINGS') );

foreach ( $this->form->getFieldset('recaptcha') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();
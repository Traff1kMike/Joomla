<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if (!$this->form_fields)
    return;

    
$entry = isset($this->entry) ? $this->entry : null;
$entry_credits = $this->entry_credits;

foreach ($this->form_fields as $form_field)
{
    if ( $form_field->field_type == 'section_break' && $form_field->properties->get('form_caption') )
    {
        echo '<h3>' . $this->escape( $form_field->properties->get('form_caption') ) . '</h3>';
    }
        
    echo RSDirectoryFormField::getInstance($form_field, $entry, $entry_credits)->generate();
}
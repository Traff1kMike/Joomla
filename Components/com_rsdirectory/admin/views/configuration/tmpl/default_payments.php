<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


echo $this->rsfieldset->getFieldsetStart();

foreach ( $this->form->getFieldset('payments') as $field )
{
    echo $this->rsfieldset->getField($field->label, $field->input);
}

echo $this->rsfieldset->getFieldsetEnd();
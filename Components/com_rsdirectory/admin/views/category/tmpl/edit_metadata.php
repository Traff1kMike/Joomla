<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$rsfieldset = $this->rsfieldset;
$form = $this->form;

echo $rsfieldset->getField( $form->getLabel('metadesc'), $form->getInput('metadesc') );
echo $rsfieldset->getField( $form->getLabel('metakey'), $form->getInput('metakey') );

foreach ( $this->form->getFieldset('metadata') as $field )
{
    echo $field->hidden ? $field->input : $this->rsfieldset->getField($field->label, $field->input);
}
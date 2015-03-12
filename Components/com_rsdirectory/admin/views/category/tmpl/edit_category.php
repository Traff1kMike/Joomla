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

?>

<div class="row-fluid">
	<div class="span9">
		<?php echo $this->form->getLabel('description'); ?>
		<?php echo $this->form->getInput('description'); ?>
	</div>
	<div class="span3">
		<fieldset class="adminform form-vertical">
		<?php echo $rsfieldset->getField( $form->getLabel('parent_id'),  $form->getInput('parent_id') ); ?>
		<?php
		foreach ( $this->form->getFieldset('params') as $field )
		{
			echo $this->rsfieldset->getField($field->label, $field->input);
		}
		?>
		<?php echo $rsfieldset->getField( $form->getLabel('published'),  $form->getInput('published') ); ?>
		<?php echo $rsfieldset->getField( $form->getLabel('access'),  $form->getInput('access') ); ?>
		<?php echo $rsfieldset->getField( $form->getLabel('language'),  $form->getInput('language') ); ?>
		<?php echo $rsfieldset->getField( $form->getLabel('note'),  $form->getInput('note') ); ?>
		</fieldset>
	</div>
</div>
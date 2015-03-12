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


require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php';

$rstabs = new RSTabs('com-rsdirectory-entry');


// Initialize the tabs array.
$tabs = array();

// Initialize the index.
$index = -1;

foreach ($this->form_fields as $form_field)
{
    if ($form_field->field_type == 'section_break')
    {
		$index++;
			
		$tabs[$index] = (object)array(
			'title' => $form_field->properties->get('form_caption'),
			'id' => "tab$form_field->id",
			'content' => '',
		);
			
		continue;
    }
        
    if ($index > -1)
    {
        $tabs[$index]->content .= RSDirectoryFormField::getInstance($form_field, $entry, $entry_credits)->generate();
    }
    else
    {
        echo RSDirectoryFormField::getInstance($form_field, $entry, $entry_credits)->generate();
    }
}

if ($tabs)
{
    echo '<div class="rsdir-tabs">';
        
    foreach ($tabs as $tab)
    {
		$rstabs->addTitle($tab->title, $tab->id);
		$rstabs->addContent($tab->content);
    }
        
    // Render the tabs.
    $rstabs->render();
       
    echo '</div><!-- .rsdir-tabs -->';
}
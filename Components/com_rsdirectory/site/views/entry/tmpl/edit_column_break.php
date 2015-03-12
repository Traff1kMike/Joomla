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

// Initialize the breaks count.
$breaks_count = 1;

foreach ($this->form_fields as $form_field)
{
    if ($form_field->field_type == 'section_break')
    {
        $breaks_count++;
    }
}

$span = floor(12 / $breaks_count);

?>

<div class="row-fluid">
    <div class="span<?php echo $this->escape($span); ?>">
        
    <?php
    foreach ($this->form_fields as $form_field)
    {
		if ($form_field->field_type == 'section_break')
		{
			?>
				
			</div>
			<div class="span<?php echo $this->escape($span); ?>">
				
			<?php
		}
			
		echo RSDirectoryFormField::getInstance($form_field, $entry, $entry_credits)->generate();
    }
    ?>
        
    </div>
</div>
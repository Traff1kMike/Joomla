<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<?php if ($this->buttons) { ?>
        
    <?php if ( isset($this->buttons->manage_directory_activity) ) { ?>
            
        <h2><?php echo JText::_('COM_RSDIRECTORY_MANAGE_DIRECTORY_ACTIVITY'); ?></h2>
            
        <div class="rsdir-dashboard-container">
            <?php foreach ($this->buttons->manage_directory_activity as $button) { ?>
                <?php if ($button->access) { ?>
                    <div class="rsdir-dashboard-info rsdir-dashboard-button">
                        <a href="<?php echo $button->link; ?>"> 
                            <img src="<?php echo $button->image; ?>" alt="<?php echo $button->text; ?>" width="<?php echo $button->width; ?>" height="<?php echo $button->height; ?>" />
                            <span class="rsdir-dashboard-title"><?php echo $button->text; ?></span> 
                        </a> 
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
            
    <?php } ?>
        
    <?php if ( isset($this->buttons->manage_directory_activity) ) { ?>
            
        <h2><?php echo JText::_('COM_RSDIRECTORY_SET_UP_DIRECTORY'); ?></h2>
            
        <div class="rsdir-dashboard-container">
            <?php foreach ($this->buttons->set_up_directory as $button) { ?>
                <?php if ($button->access) { ?>
                    <div class="rsdir-dashboard-info rsdir-dashboard-button">
                        <a href="<?php echo $button->link; ?>"> 
                            <img src="<?php echo $button->image; ?>" alt="<?php echo $button->text; ?>" width="<?php echo $button->width; ?>" height="<?php echo $button->height; ?>" />
                            <span class="rsdir-dashboard-title"><?php echo $button->text; ?></span> 
                        </a> 
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
            
    <?php } ?>
        
<?php }
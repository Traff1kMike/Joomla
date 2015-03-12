<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$form_class = '';

if ( RSDirectoryHelper::isJ30() )
{
    $form_class = ' rsdir-j30';
}
else if ( RSDirectoryHelper::isJ25() )
{
    $form_class = ' rsdir-j25';
}

?>

<div class="rsdir">
    <form class="rsdir-mod-simple-search<?php echo $form_class; ?>" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&task=filters.process'); ?>" method="post">
        <?php if ( $params->get('show_categories') && $categories_list ) { ?>
        <div class="input-prepend input-append">
            <div class="btn-group pull-left">
                <div class="btn dropdown-toggle" data-toggle="dropdown"><?php echo $selected_category_text; ?> <span class="caret"></span></div>
                <input type="hidden" name="categories[]" value="<?php echo $selected_category_value; ?>" />
                <ul class="dropdown-menu">
                    <li><a href="javascript: void(0);" data-value="0" data-text="<?php echo JText::_('MOD_RSDIRECTORY_SIMPLE_SEARCH_ALL_CATEGORIES'); ?>"><?php echo JText::_('MOD_RSDIRECTORY_SIMPLE_SEARCH_ALL_CATEGORIES'); ?></a></li>
                    <?php RSDirectorySimpleSearchHelper::outputCategoriesOptions($categories_list); ?>
                </ul>
            </div>
            <input class="input-large pull-left" type="text" name="q" placeholder="<?php echo JText::_('MOD_RSDIRECTORY_SIMPLE_SEARCH_PLACEHOLDER'); ?>" value="<?php echo $q; ?>" />
            <button class="btn pull-left" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
        </div>
        <?php } else { ?>
        <div class="input-prepend input-append">
            <input class="input-large" type="text" name="q" placeholder="<?php echo JText::_('MOD_RSDIRECTORY_SIMPLE_SEARCH_PLACEHOLDER'); ?>" value="<?php echo $q; ?>" />
            <button class="btn" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
        </div>
        <?php } ?>
            
        <div>
            <?php echo JHTML::_('form.token') . "\n"; ?>
            <?php echo $itemid ? '<input type="hidden" name="Itemid" value="' . $itemid . '" />' : ''; ?>
        </div>
    </form>
</div><!-- .rsdir -->
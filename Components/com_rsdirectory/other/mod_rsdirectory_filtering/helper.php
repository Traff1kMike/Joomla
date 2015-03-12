<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_FILTERING_VERSION', '1.0.2');

/**
 * RSDirectory! Filtering Module Helper.
 */
abstract class RSDirectoryFilteringHelper
{
    /**
     * Get categories options.
     *
     * @access public
     *
     * @static
     *
     * @param array $categories
     * @param int $level
     *
     * @return array
     */
    public static function outputCategoriesOptions($categories, $level = 0)
    {
		$selected_categories = JFactory::getApplication()->input->get( 'categories', array(), 'array' );
			
		if ( empty($categories) )
			return;
			
		foreach ($categories as $category)
		{
			if ( !$category->published )
				continue;
				
			?>
				
			<option value="<?php echo $category->id; ?>"<?php echo in_array($category->id, $selected_categories) ? ' selected="selected"' : ''; ?>><?php echo str_repeat('- ', $level) . ' ' . RSDirectoryHelper::escapeHTML($category->title); ?></option>
				
			<?php
				
			if ( $subcategories = $category->getChildren() )
			{
				self::outputCategoriesOptions($subcategories, $level + 1);
			}
		}
    }
}
 
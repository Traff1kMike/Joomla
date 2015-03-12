<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_SIMPLE_SEARCH_VERSION', '1.0.2');

/**
 * RSDirectory! Simple Search Module Helper.
 */
abstract class RSDirectorySimpleSearchHelper
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
		if ( empty($categories) )
			return;
			
		foreach ($categories as $category)
		{
			if ( !$category->published )
				continue;
				
			?>
				
			<li><a href="javascript: void(0);" data-value="<?php echo $category->id; ?>" data-text="<?php echo RSDirectoryHelper::escapeHTML($category->title); ?>"><?php echo str_repeat('- ', $level) . ' ' . RSDirectoryHelper::escapeHTML($category->title); ?></a></li>
				
			<?php
				
			if ( $subcategories = $category->getChildren() )
			{
				self::outputCategoriesOptions($subcategories, $level + 1);
			}
		}
    }
}
 
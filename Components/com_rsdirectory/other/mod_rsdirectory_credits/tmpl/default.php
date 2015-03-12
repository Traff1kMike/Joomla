<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="rsdir">
<?php
if ($credits === 'unlimited')
{
	echo JText::_('MOD_RSDIRECTORY_UNLIMITED_CREDITS_TEXT');
}
else
{
	echo JText::sprintf('MOD_RSDIRECTORY_CREDITS_TEXT', $credits, $url);
}
?>
</div>
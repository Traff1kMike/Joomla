<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>
<form action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=updates'); ?>" method="post" name="adminForm" id="adminForm">	
	<div class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div class="span10">
		<iframe src="http://www.rsjoomla.com/index.php?option=com_rsmembership&amp;task=checkupdate&amp;sess=<?php echo $this->hash; ?>&amp;revision=<?php echo RSDirectoryVersion::$version; ?>&amp;version=<?php echo urlencode($this->jversion); ?>&amp;tmpl=component" style="border:0px solid;width:100%;height:22px;" scrolling="no" frameborder="no"></iframe>
		<iframe src="http://www.rsjoomla.com/latest.html?tmpl=component" style="border:0px solid;width:100%;height:380px;" scrolling="no" frameborder="no"></iframe>
	</div>
</form>
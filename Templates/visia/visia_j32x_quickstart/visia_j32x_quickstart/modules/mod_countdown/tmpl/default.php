<?php
/**
	 * @package   CSS3 Animation module for Joomla 2.5
	 * @version   2.5
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Convert to Joomla from Codrops's tutorial on CSS3 Menu animation
	 * @copyright Copyright (C) 2011 Codrops and J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * CSS3 Box module has been developed and distributed under the terms of the GPL 
	 * @copyright Joomla is Copyright (C) 2005 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
	 */
	 

defined('_JEXEC') or die('Restricted access');

//$doc 	= JFactory::getDocument();

?>

<?php if ( $show_pretext ) { ?>
<div class="introtext grid-full"><h2><?php echo $pretext; ?></h2></div>
<?php } ?>
<div id="vcountdown<?php echo $module->id; ?>"></div>
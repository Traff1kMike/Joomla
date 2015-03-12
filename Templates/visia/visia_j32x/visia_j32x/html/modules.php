<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
function modChrome_container($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<div class="container">
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}
*/


function modChrome_raw($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
	
}


function modChrome_headsubhead($module, &$params, &$attribs) {

$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;

    if ( !empty($module->content) || $module->showtitle ) { ?>
        
	<div class="title <?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?> grid-full">
        <?php if ($module->showtitle != 0) { ?>
			<h<?php echo $headerLevel; ?>><?php echo $module->title . "\n"; ?></h<?php echo $headerLevel; ?>>
	    	<span class="border"></span>
	    <?php } ?>

		<?php echo $module->content . "\n"; ?>

	</div>
	<?php
    }
}

function modChrome_bottom($module, &$params, &$attribs)
{
	
	if ($module->showtitle) { ?>
		<div class="title<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?> grid-full">
			<h3><?php echo $module->title; ?></h3>
			<span class="border"></span>
		</div>
	<?php } ?>
	
	<?php
	if (!empty ($module->content)) { 
		echo $module->content; 
	}
}

function modChrome_footer($module, &$params, &$attribs)
{
	if (!empty ($module->content)) { 
		if ($module->showtitle) { 
			echo '<h3 class="module-title">'.$module->title.'</h3>';
		}
		
		echo $module->content; 
	}
}

function modChrome_sidebar($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;

	if (!empty ($module->content)) { ?>

		<div class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
		<?php if ($module->showtitle != 0) { ?>
			<h3><?php echo $module->title; ?></h3>
		<?php } 
		echo $module->content; 
		?>
		</div>

	<?php }

}



<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$config = $this->config;

?>

<div class="rsdir">
	<div class="row-fluid">
			
		<form action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
				
			<?php
			foreach ( $this->form->getFieldset('general') as $field )
			{
				if ( in_array($field->fieldname, $this->skipped_fields) )
					continue;
					
				?>
				<div class="control-group">
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</div>
				<?php
			}
			?>
				
			<?php if ($this->contact_captcha) { ?>
				
			<div class="rsdir-field-wrapper control-group clearfix">
					
				<label class="rsdir-label"><?php echo JText::_('COM_RSDIRECTORY_CAPTCHA_LABEL'); ?></label>
					
				<?php
					
				if ( $config->get('captcha_type') == 'built_in' )
				{
					$width = 30 * $config->get('captcha_characters_number') + 50;
						
					?>
						
					<img id="rsdir-captcha" src="<?php echo JRoute::_( "index.php?option=com_rsdirectory&task=field.captcha&random=" . mt_rand() ); ?>" width="<?php echo $width; ?>" height="80" alt="CAPTCHA" />
						
					<i id="rsdir-captcha-refresh" class="icon-refresh" title="<?php echo JText::_('COM_RSDIRECTORY_REFRESH_CAPTCHA_DESC'); ?>"></i>
						
					<img id="rsdir-captcha-loader" class="rsdir-hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />
						
					<br />
						
					<input id="rsdir-captcha-input" type="text" name="jform[captcha]" required="true" aria-required="true" />
				<?php
					
				}
				else
				{
					require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/recaptcha/recaptchalib.php';
						
					echo RSDirectoryReCAPTCHA::getHTML( $config->get('recaptcha_public_key'), $config->get('recaptcha_theme') );
				}
					
				?>
				
			</div>
				
			<?php } ?>		
				
			<div>
				<?php echo JHTML::_('form.token') . "\n"; ?>
				<input type="hidden" name="task" value="contact.save" />
			</div>
		</form>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->
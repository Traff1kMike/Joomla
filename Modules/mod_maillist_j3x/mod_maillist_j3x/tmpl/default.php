<?php
/**
	 * @package   Visia Mail list Subscribe Form
	 * @version   1.0
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Based on Subscribe Visia Template
	 * @copyright Copyright (C) 2013 J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * Simple Mailing List Subscribe module has been developed under the terms of the GPL 
	 * @copyright Joomla is Copyright (C) 2005-2013 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
	 */

defined('_JEXEC') or die('Restricted access');

$doc 	= JFactory::getDocument();

$success_msg	= $params->get('success_msg');
$error_msg	= $params->get('error_msg');

$path = 'index.php?option=com_ajax&amp;module=maillist&amp;format=raw&amp;method=get';
/* will not added slash at the path end */
$modbase 	= JURI::base(true) .'/modules/mod_maillist';

//$doc->addStyleSheet($modbase . '/assets/css/style.css');
$doc->addScript($modbase . '/assets/jquery.countto.js');
$doc->addScriptDeclaration("
jQuery(document).ready(function(){
    //TIMER
    jQuery('.timer').appear();
    jQuery(document.body).on('appear', '.timer', function() {
        jQuery(this).countTo();
    });

    jQuery(document.body).on('disappear', '.timer', function() {
        jQuery(this).removeClass('timer');
    });

    //SUBSCRIBTION FORM
    jQuery(function(jQuery) {
	jQuery('body').on('click','#subscribe',function(){
	    var email = jQuery('#email').val();
	    var subscribed = jQuery('#subscribed').val();
	    var errcount = 0;

	    var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
	    var valid = emailRegex.test(email);
	    if (!valid) {
		
		jQuery('.notification.error').css('opacity', 0);
                jQuery('.notification.error').slideDown(300);
                jQuery('.notification.error').animate({
		    opacity : 1
		    }, 300);
		jQuery('.notification.success').hide();
		errcount = 1;
	    } else {
		/*
                jQuery('.notification.success').css('opacity', 0);
		jQuery('.notification.success').slideDown(300);
		jQuery('.notification.success').animate({
			opacity : 1
		    }, 300);
		jQuery('.notification.error').hide();
                */
	    }

	    request = {
		'option'    : 'com_ajax',
                'module'    : 'maillist',
                'email'     : email,
                'subscribed' : subscribed,
                'format'    : 'raw'
            	};

	    if (errcount === 0) {
		jQuery.ajax({
		    'type':'POST',
		    //'url': 'index.php?option=com_ajax&module=maillist&format=raw&method=get',
		    'cache':false,
		    'data':  request, //jQuery(this).parents('form').serialize(),
		    'success': function(data) {
					
			//console.log('data='+data);

			var error = jQuery('.notification.error');
			var success = jQuery('.notification.success');
			if(data == 1) {
			    success.css('opacity', 0);
			    success.slideDown(300);
			    success.animate({
				opacity : 1
			    }, 300);
			    error.hide();
			} else {
			    error.css('opacity', 0);
			    error.slideDown(300);
			    error.animate({
			    opacity : 1
			    }, 300);
			    success.hide();
			} 
		    }
		});
	    } // end if no error

	    return false;
	});
		
    });
});
");

	
?>

<?php if ($show_numthings) { ?>
	<?php foreach ($counts as $count) { ?>
	<!-- Milestones -->
	<div class="milestone grid-2">
		<span class="timer value" data-from="0" data-to="<?php echo $count->num; ?>" data-speed="2000" data-refresh-interval="100"></span>
		<h4><?php echo $count->label; ?></h4>
	</div>
	<?php } ?>
<?php } ?>

	<!-- Subscribe -->
	<div class="subscribe grid-full">
		
		<?php if ($show_pretext) { ?><p><?php echo $pretext; ?></p><?php } ?>

		<form action="<?php echo $path; ?>" method="post" class="clearfix">
			<input type="text" id="email" name="email" value="" class="text">
			<input type="submit" value="Subscribe now" name="subscribe" class="submit" id="subscribe">
			<input type="hidden" name="subscribed" id="subscribed" value="true" />
			
			<div class="notification success closeable">
				<p><?php echo $success_msg; ?></p>
			</div>
			<div class="notification error closeable">
				<p><?php echo $error_msg; ?></p>
			</div>
		</form>
	</div>

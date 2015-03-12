<?php
// no direct access
defined('_JEXEC') or die ('Restricted access'); 

$doc = JFactory::getDocument();
$path = 'index.php?option=com_ajax&amp;module=visiacontact';
$loader_path = $modbase.'images/ajax-loader.gif';

session_name("visiacontact");
session_start();

if ($enable_captcha) {
    $_SESSION['n1'] = rand(1,15);
    $_SESSION['n2'] = rand(1,15);
    $_SESSION['expect'] = $_SESSION['n1']+$_SESSION['n2'];
} else {
    $_SESSION['expect'] = 0;
}

$doc->addStylesheet($modbase.'assets/style.css');

$js = "

jQuery(document).ready(function() {

    jQuery('#contactform').submit(function(e){
        e.preventDefault();

        //var dataString = jQuery('#contactform').serialize();
        //var action = jQuery(this).attr('action');

        jQuery('#alert').slideUp(750,function() {
            jQuery('#alert').hide();

            jQuery('#submit')
                .after('<img src=".$loader_path." class=\"loader\" />')
                .attr('disabled','disabled');

                
                var name = jQuery('#name').val();
                    email = jQuery.trim(jQuery('#vcemail').val());
                    enable_captcha = ".$enable_captcha.";
                    captcha = jQuery('#captcha').val();
                    expect = ".$_SESSION['expect'].";
                    message = jQuery('#message').val();
                    submitted = jQuery('#submitted').val();
                    errcount = 0;
                      
                    
                if (email  === '') {
                    document.getElementById('alert').innerHTML = '<div class=\"notification error clearfix\"><p>Please enter an e-mail address.</p></div>';
                    jQuery('#alert').slideDown('slow');
                    jQuery('#submit').removeAttr('disabled');
                    jQuery('#contactform img.loader').fadeOut('slow',function(){jQuery(this).remove()});
                    errcount = 1;
                } else {

                    var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
                    var valid = emailRegex.test(email);
                    if (!valid) {
                        document.getElementById('alert').innerHTML = '<div class=\"notification error clearfix\"><p>You have entered an invalid e-mail address, try again.</p></div>';
                        jQuery('#alert').slideDown('slow');
                        jQuery('#submit').removeAttr('disabled');
                        jQuery('#contactform img.loader').fadeOut('slow',function(){jQuery(this).remove()});
                        errcount = 1;
                    } else {
                        
                        errcount = 0;
                    }
                }

                request = {
                            'option'    : 'com_ajax',
                            'module'    : 'visiacontact',
                            'name'      : name,
                            'email'     : email,
                            'enable_captcha' : enable_captcha,
                            'captcha'   : captcha,
                            'message'   : message,
                            'expect'    : expect,
                            'submitted' : submitted,
                            'format'    : '{$format}'
                };

                if (errcount === 0) {

                    jQuery.ajax({
                        type   : 'POST',
                        data   : request,
                        //data: dataString,
                        success: function (response) {
                            //console.log(response);

                            if(response){
                                document.getElementById('alert').innerHTML = response;
                            }
                            jQuery('#alert').slideDown('slow');
                            jQuery('#contactform img.loader').fadeOut('slow',function(){jQuery(this).remove()});
                            jQuery('#submit').removeAttr('disabled');

                        },
                        error: function(response) {
                            if(response){
                                document.getElementById('alert').innerHTML = response;
                            }
                        }
                    });
                }

            });

    });
        
});
";
$doc->addScriptDeclaration($js);
?>

<div id="contact-form" class="dark clearfix">

    <div class="container">
        <div class="contact-heading grid-full">
            <h2><?php echo JText::_('Get in Touch'); ?></h2>
            <span class="border"></span>
        </div>
    </div>

    
    <form id="contactform" class="container" name="contactform" action="<?php echo $path; ?>" method="post" enctype="multipart/form-data">
        <fieldset>

            <div class="form-field grid-half">
                <label for="name"><?php echo $name_label; ?></label>
                <span>
                <input type="text" name="name" id="name" 
                    value="<?php if(isset($_POST['name'])) echo $_POST['name'];?>" />
                </span>
            </div>

                       
            <div class="form-field grid-half">
                <label for="email"><?php echo $email_label; ?></label>
                <span><input type="email" name="email" id="vcemail" value="<?php if (isset($_POST['vcemail'])) echo ($_POST['vcemail']); ?>" /></span>
            </div>
                        
            <div class="form-field grid-full">
                <label for="message"><?php echo $message_label; ?></label>
                <span>
                    <textarea name="message" id="message"><?php if(isset($_POST['message'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['message']); } else { echo $_POST['message']; } } ?></textarea>
                </span>
            </div>

            <?php if ($enable_captcha) { ?>
            <div class="form-field grid-full">
                <label for="captcha"><?php echo stripslashes($captcha_label); ?> <?php echo $_SESSION['n1']; ?> + <?php echo $_SESSION['n2']; ?> = </label>
                <span>
                    <input type="text" name="captcha" id="captcha" value="<?php if (isset($_POST['captcha'])) echo ($_POST['captcha']); ?>" />
                </span>
            </div>
            <?php } ?>

        </fieldset>

        <div class="form-click grid-full">
            <span><input id="submit" name="submit" type="submit" class="" value="<?php echo $submit_label; ?>" /></span>
            <input type="hidden" name="submitted" id="submitted" value="true" />
            <?php /*echo JHtml::_('form.token');*/ ?>
        </div>

            
        <div id="alert" class="grid-full"></div>

    </form> 
</div>



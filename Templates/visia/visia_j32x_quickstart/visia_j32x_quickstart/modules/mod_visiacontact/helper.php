<?php 
/**
 * File       helper.php
 * Created    6/7/13 1:51 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2013 betweenbrain llc. All Rights Reserved.
 * License    GNU General Public License version 2, or later.
 */
defined('_JEXEC') or die;

class modVisiaContactHelper {

	public static function getAjax() {

		// Check for request forgeries.
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get module parameters
		jimport('joomla.application.module.helper');
		$input  = JFactory::getApplication()->input;
		$module = JModuleHelper::getModule('visiacontact');
		$params = new JRegistry();
		$params->loadString($module->params);
		$node        = $params->get('node', 'data');
		$format     = $params->get('format', 'raw');
		$session     = JFactory::getSession();
		$sessionData = $session->get($node);

		$subject = $params->get('subject','Request a quote');
		$recipient = $params->get("recipient","");

		if (is_null($sessionData)) {
			$sessionData = array();
			$session->set($node, $sessionData);
		}

		
		if ( $input->post->getBool('submitted') ) 
		{
			$name = $input->post->getUsername('name');
			//$email = $input->get('email');
			$enable_captcha = $input->post->getBool('enable_captcha');
			$captcha = $input->post->getInt('captcha');
			$expect = $input->post->getInt('expect');
			$message = $input->post->getHTML('message');

					
			// require a name from user
			//if (!isset($sessionData[$name]) && $name != '') {
			//if ( !empty( $_POST['name'] ) ) {
			if ( !empty( $name ) ) {
				$name =  trim($name); // $_POST['name'];
				$hasError = false;
				//$sessionData[$name] = trim($name);
				//$session->set($node, $sessionData);
			} else {
				$nameError =  'You must enter your name.'; 
				$hasError = true;
			}

			
			// need valid email
			//if (!isset($sessionData[$email]) && $email != '') {
			if (!empty($_POST['email'])) {
			
				$email =  htmlspecialchars(strip_tags($_POST['email'])); // 
    		
				//if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
				if ( !self::is_email($email) ) {
					$emailError = 'You have entered an invalid e-mail address, try again.';
					$hasError = true;
				} else {
					//$email = trim($email);
					$hasError = false;
					//$sessionData[$email] = trim($email);
					//$session->set($node, $sessionData);
				}
			} else {
				$emailError = 'Please enter an e-mail address.';
				$hasError = true;
			}

			// we need at least some content
			//if (!isset($sessionData[$message]) && $message != '') {
			if ( !empty( $message ) ) {

				$message = trim($message);
				/*
				if(function_exists('stripslashes')) {
					$sessionData[$message] = stripslashes($message);
					$message = stripslashes($message);
				} else {
					$sessionData[$message] = $message;
				}
				$session->set($node, $sessionData);
				*/

				$message =  htmlspecialchars(strip_tags($message));
				$hasError = false;
				
			} else {
				$messageError = 'Please enter your Message!';
				$hasError = true;
			}

			// require a valid captcha
			if ( $enable_captcha ) {

				//if (!isset($sessionData[$captcha]) && $captcha != '') {
				if ( $captcha != '') {
					$captcha = trim($captcha);

					if( $captcha == $expect ) {
						
						//unset ($_SESSION['n1']);
						//unset ($_SESSION['n2']);
						//unset ($_SESSION['expect']);
						//$sessionData[$captcha] = $captcha;
						$hasError = false;
					} else {
						$captchaError =  'You entered captcha: '.$captcha.'. Please enter the correct Captcha!'; 
						$hasError = true;
					}
				} else {
					$captchaError = 'Please enter the Captcha!'; 
					$hasError = true;
				} 

			}


			if ( isset($nameError) ) { 
				return '<div class="notification error clearfix"><p>'.$nameError.'</p></div>';
				exit();
			}
			if ( isset($emailError) ) {
				return '<div class="notification error clearfix"><p>'.$emailError.'</p></div>';
				exit();
			}
			if( isset($messageError)) { 
				return '<div class="notification error clearfix"><p>'.$messageError.'</p></div>';
				exit();
			}
			if( isset($captchaError) ) { 
				return '<div class="notification error clearfix"><p>'.$captchaError.'</p></div>';
				exit();
			}

			if($recipient == "") {

				return '<div class="notification info clearfix">
						<p>Email recipient not define for contact recieving contact email, Please contact to site admin report to this problem .</p>
						</div>';
				exit();
			}

			// upon no failure errors let's email now!
			if( !$hasError ) 
			{
				
				$mail = JFactory::getMailer();		
				
				//$config = JFactory::getConfig();
				//$config->getValue( 'config.mailfrom' ),
    			//$config->getValue( 'config.fromname' ) );

				$sender = array( $email, $name );
				$mail->setSender($sender);
				$mail->setSubject($subject);
				$mail->addRecipient($recipient);

				$e_body = "<p>Subject: ".$subject."</p>";
				//$e_body .= "<p>You have been contacted by $name , their message is as follows.</p>";
				$e_content = "<p>".$message."</p>";
				$e_reply = "<p>You can contact ".$name." via email: ".$email."</p>";
				
				$body = wordwrap( $e_body . $e_content . $e_reply, 70 );
												
				$mail->setBody($body);
				$mail->IsHTML(true);
				$mail->Encoding = 'base64';
				$send = $mail->Send();
					
				if ($send == true) $emailSent = true;
				else $emailSent = false;
				

			}

			if(isset($emailSent) && $emailSent == true) {
				return "<fieldset><div id='success_page'>
					<div class='notification success clearfix'><p>Thank you <strong>$name</strong>, your message has been submitted to us.</p></div>
					</div></fieldset>";
				exit();
			}

			

			
			/* -------------- end ---------------------- */
			/*
			if ($sessionData) {
				return $sessionData;
			}
			*/

			return FALSE;
		}
	}

	// from wordpress code: wp-includes/formatting.php
	protected static function is_email($user_email)
	{
	    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";

	    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
	    {
	        if (preg_match($chars, $user_email)) {
	            return true;
	        } else {
	            return false;
	        }
	    } else {
	        return false;
	    }
	}

	private static function isEmail($email) 
	{
				
		return ( (strlen($email) > 8) && 
			preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email) );
		
		
	}
	
	
}
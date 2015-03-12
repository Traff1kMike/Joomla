<?php
/* Subscrib module for Visia template */

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modMaillistHelper
{
	public static function getItems( &$params )
	{
		$numcountitems = 3;
		$counts = array();

		for( $n=1; $n <= $numcountitems; $n++ )
		{
			
			$countlabel	= $params->get( 'countlabel_'.$n );
			$countnum	= $params->get( 'countnum_'.$n );
			
			if( !empty( $countlabel ) && !empty( $countnum ) )
			{
				$count = new JObject;
				$count->label = $countlabel;
				$count->num = $countnum;
				
				$counts[] = $count;
			}
		}
		
		return $counts;
	}

	public static function getAjax() {

		// Check for request forgeries.
		//JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get module parameters
		jimport('joomla.application.module.helper');
		$input  = JFactory::getApplication()->input;
		$module = JModuleHelper::getModule('maillist');
		$params = new JRegistry();
		$params->loadString($module->params);
		$node 	= 'data';
		$format	= 'raw';

		$session     = JFactory::getSession();
		$sessionData = $session->get($node);

		$subject = $params->get('subject','New Mailing List Subscriber');
		$name = 'eMaillist';
		$recipient = $params->get('maillist_email');

		
		if ( $input->post->getBool('subscribed') ) 
		{
			if (!empty($_POST['email']))
			{
				$email =  htmlspecialchars(strip_tags($_POST['email']));

				if ( !self::is_email($email) ) {
					$emailError = 'You have entered an invalid e-mail address, try again.';
					$hasError = true;
				} else {
					//$hasError = false;
					//$sessionData[$email] = trim($email);
					//$session->set($node, $sessionData);
				}
			} else {
				$emailError = 'Please enter an e-mail address.';
				$hasError = true;
			}
			

			if ($emailError != '') {
				return 0; 
				exit();
			}

			// upon no failure errors let's email now!
			if ( !isset($hasError) ) {
				$mail = JFactory::getMailer();		
				$config = JFactory::getConfig();
				$sender = array( $email, $name );
				$mail->setSender($sender);
				$mail->setSubject($subject);
				$mail->addRecipient($recipient);
					
				$e_body = "You have new subscriber!<br/>";
				$e_content = "User email:<br/><br/>";
				$e_content .= $email;
				//$body = wordwrap( $e_body . $e_content . $e_reply, 70 );
				$body = $e_body . $e_content;
				
				//$body.= $msg."<br/>";
					
				$mail->setBody($body);
				$mail->IsHTML(true);
				$send = $mail->Send();
				$emailSent = true;
			}

			if ( isset($emailSent) && $emailSent == true ) {
				return 1;
				exit();
			}
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
}

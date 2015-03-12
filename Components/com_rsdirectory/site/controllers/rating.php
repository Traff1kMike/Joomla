<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Rating controller.
 */
class RSDirectoryControllerRating extends JControllerForm
{
    /**
     * Post review ajax task.
     *
     * @access public
     */
    public function postReviewAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		// Initialize the response array.
		$response = array();
			
		// Get the mainframe.
		$app = JFactory::getApplication();
			
		// Get the JInput object.
		$jinput = $app->input;
			
		// Get the data.
		$data = $jinput->get( 'jform', array(), 'array' );
			
		// Get the Rating model.
		$model = $this->getModel();
			
		// Validate the posted data.
		$return = $model->validate(false, $data);
			
		if ($return !== false)
		{
			// Save the review.
			if ( $model->save($return) )
			{
				// Get the RSDirectory Config object.
				$config = RSDirectoryConfig::getInstance();
					
				// Are reviews enabled?
				$enable_reviews = $config->get('enable_reviews');
					
				// Are ratings enabled?
				$enable_ratings = $config->get('enable_ratings');
					
				// Can the user post reviews?
				$can_post_reviews = RSDirectoryHelper::checkUserPermission('can_post_reviews');
					
				// Can the user cast votes?
				$can_cast_votes = RSDirectoryHelper::checkUserPermission('can_cast_votes');
					
				if ( RSDirectoryHelper::checkUserPermission('auto_publish_reviews') )
				{
					if ($enable_reviews && $can_post_reviews)
					{
						$msg = JText::_('COM_RSDIRECTORY_REVIEW_POSTED');
					}
					else if ($enable_ratings && $can_post_reviews)
					{
						$msg = JText::_('COM_RSDIRECTORY_VOTE_POSTED');
					}
						
					if ($enable_reviews && $can_post_reviews)
					{
						$response['review'] = RSDirectoryHelper::getReviewHTML( RSDirectoryHelper::getReview( $model->getState('rating.id') ) );	
					}
						
					if ($enable_ratings && $can_cast_votes)
					{
						$entry = RSDirectoryHelper::getEntry($data['entry_id']);
							
						$response['rating'] = RSDirectoryHelper::getRatingHTML($entry->avg_rating, $entry->ratings_count);	
					}
				}
				else
				{
					if ($enable_reviews && $can_post_reviews)
					{
						$msg = JText::_('COM_RSDIRECTORY_REVIEW_SUBMITTED');
					}
					else if ($enable_ratings && $can_post_reviews)
					{
						$msg = JText::_('COM_RSDIRECTORY_VOTE_CAST');
					}
				}
					
				$response['ok'] = $msg;
			}
			else
			{
				$response['error_messages'] = $model->getErrors();
			} 
		}
		else
		{
			$response['error_messages'] = $model->getErrorMessages();
			$response['error_fields'] = $model->getErrorFields();
		}
			
		echo json_encode($response);
			
		$app->close();
    }
}
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Ratings controller.
 */
class RSDirectoryControllerRatings extends JControllerLegacy
{
	/**
     * Load more reviews.
     *
     * @access public
     */
    public function showMoreAjax()
    {
		// Stop the script if the token is invalid.
        JSession::checkToken('get') or jexit('Invalid Token');
			
		header('Content-Type: application/json');
			
		$app = JFactory::getApplication();
			
		$model = $this->getModel('Ratings');
		$pagination = $model->getPagination();
			
		$items = $model->getItems();
			
		$reviews = '';
			
		if ($items)
		{
			foreach ($items as $item)
			{
				$reviews .= RSDirectoryHelper::getReviewHTML($item);
			}
		}
			
		$response = array(
			'reviews' => $reviews,
		);
			
		if ( $pagination->get('limitstart') + $pagination->get('limit') >= $pagination->get('total') )
		{
			$response['hide'] = 1;
		}
			
		echo json_encode($response);
			
		$app->close();
	}
}
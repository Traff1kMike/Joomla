<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Radius Search controller.
 */
class RSDirectoryControllerRadius extends JControllerForm
{
    /**
     * The class constructor.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
    }
		
	/**
     * Get the necessery data. 
     *
     * @access public
     */
    public function getDataAjax()
    {
		// Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
			
		//header('Content-Type: application/json');
            
		// Get the Radius Search model.
		$model = $this->getModel('Radius');
			
		echo json_encode( $model->getItems() );
			
		JFactory::getApplication()->close();
	}
		
	/**
	 * Get the info window for a certain entry.
	 *
	 * @access public
	 */
	public function getInfoWindow()
	{
		// Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
			
		// Get the Radius Search model.
		$model = $this->getModel('Radius');
			
		echo $model->getInfoWindow();
			
		JFactory::getApplication()->close();
	}
}
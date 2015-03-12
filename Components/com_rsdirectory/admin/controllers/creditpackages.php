<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credit Packages controller.
 */
class RSDirectoryControllerCreditPackages extends JControllerAdmin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * 
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     *
     * @return object
     */
    public function getModel( $name = 'CreditPackage', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
        
    /**
     * Save order ajax.
     *
     * @access public
     */
    public function saveOrderAjax()
    {
        $app = JFactory::getApplication();
            
        $pks = $app->input->post->get( 'cid', array(), 'array' );
        $order = $app->input->post->get( 'order', array(), 'array' );
            
        // Sanitize the input.
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);
            
        // Get the model.
        $model = $this->getModel();
            
        // Save the ordering.
        if ( $model->saveorder($pks, $order) )
        {
            echo 1;
        }
            
        // Close the application.
        $app->close();
    }
}
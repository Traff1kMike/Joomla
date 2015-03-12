<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * My Account view.
 */
class RSDirectoryViewMyAccount extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Only logged in users can access this page.
        if ( !JFactory::getUser()->id )
        {
            return $app->enqueueMessage( JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST') );
        }
            
        if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = $app->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_YOUR_ACCOUNT') );
		}
            
        if ( $tab = $app->input->get('tab') )
        {
            $script = 'jQuery(function($){ $( document.getElementById("com-rsdirectory-configuration") ).find("a[href=\'#' . $tab . '\']").click(); });';
                
            JFactory::getDocument()->addScriptDeclaration($script);
        }
            
        $state = $this->get('State');
            
        // Get the page/component configuration.
        $params = $state->params;
            
        // Get the credits history model.
        $creditshistory_model = RSDirectoryModel::getInstance('CreditsHistory');
            
        $this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
        $this->params = $params;
        $this->rstabs = $this->get('RSTabs');
		$this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
        $this->current_credits = RSDirectoryCredits::getUserCredits();
        $this->spent_credits = RSDirectoryCredits::getUserSpentCredits();
        $this->posted_entries = $this->get('PostedEntriesCount');
        $this->transactions = $this->get('Transactions');
        $this->credits_history = $creditshistory_model->getItems();
            
        $pagination = $creditshistory_model->getPagination();
        $pagination->setAdditionalUrlParam('view', 'myaccount');
        $pagination->setAdditionalUrlParam('tab', 'credits-history');
            
        $this->pagination = $pagination;
            
        parent::display($tpl);
    }
}
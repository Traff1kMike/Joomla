<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The EmailMessage view.
 */
class RSDirectoryViewEmailMessage extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the email message id.
        $id = JFactory::getApplication()->input->getInt('id');
            
        if ($id)
        {
            // Get the email message.
            $email_message = JTable::getInstance('EmailMessage', 'RSDirectoryTable');
            $email_message->load($id);
                
            if (!$email_message->id)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
        }
            
        JText::script('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_NO_CUSTOM_FIELDS');
            
        $this->addToolBar();
        $this->id = $id;
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
        $this->fieldsets = $this->form->getFieldsets();
            
        parent::display($tpl);
    }
        
    /**
     * Add toolbar.
     *
     * @access protected
     */
    protected function addToolBar()
    {
        // Set title.
        if ( JFactory::getApplication()->input->getInt('id') )
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_EMAIL_MESSAGE'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_EMAIL_MESSAGE'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('emailmessages');
            
        JToolBarHelper::apply('emailmessage.apply');
        JToolBarHelper::save('emailmessage.save');
        JToolBarHelper::save2new('emailmessage.save2new');
        JToolBarHelper::cancel('emailmessage.cancel');
    }
}
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry view.
 */
class RSDirectoryViewEntry extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $jinput = $app->input;
            
        // Get stored data.
        $data = $app->getUserState('com_rsdirectory.edit.entry.data');
            
        // Get the entry id.
        $id = $jinput->getInt('id');
            
        // Get the category id.
        $category_id = empty($data['category_id']) ? $jinput->getInt('category_id') : $data['category_id'];
            
        if ($id)
        {
            // Get entry.
            $entry = $this->get('Item');
                
            if (!$entry)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
                
            if (!$category_id)
            {
                $category_id = $entry->category_id;    
            }
                
            $this->entry = $entry;
        }
            
        // Get the form associated to the category.
        $form = RSDirectoryHelper::getCategoryInheritedForm($category_id);
            
        $this->form = $form;
            
        if ($form->id)
        {
            $form_fields = RSDirectoryHelper::getFormFields($form->id, 1);
                
            if ($form->use_title_template || $form->use_big_subtitle_template || $form->use_small_subtitle_template || $form->use_description_template)
            {
                foreach ($form_fields as $i => $form_field)
                {
                    if (
                        ($form->use_title_template && $form_field->field_type == 'title') ||
                        ($form->use_big_subtitle_template && $form_field->field_type == 'big_subtitle') ||
                        ($form->use_small_subtitle_template && $form_field->field_type == 'small_subtitle') ||
                        ($form->use_description_template && $form_field->field_type == 'description')
                    )
                    {
                        unset($form_fields[$i]);
                    }
                }
            }
                
            if ( RSDirectoryHelper::findFormField('map', $form_fields, true) )
            {
                $doc = JFactory::getDocument();
                $doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.rsmap.js?v=' . RSDirectoryVersion::$version );
            }
                
            $this->form_fields = $form_fields;
               
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/formfield.php';
        }
        else
        {
            $url = JRoute::_("index.php?option=com_rsdirectory&task=category.edit&id=$category_id");
                
            JError::raiseError( 500, JText::sprintf('COM_RSDIRECTORY_SELECTED_CATEGORY_NO_FORM_ASSIGNED', $url) );
        }
            
        $this->addToolBar();
        $this->id = $id;
        $this->category_id = $category_id;
        $this->rsfieldset = $this->get('RSFieldset');
        $this->jform = $this->get('Form');
            
        parent::display($tpl);
            
        // Clear the stored data.
        $app->setUserState('com_rsdirectory.edit.entry.data', null);
            
        // Clear errors.
        $app->setUserState('com_rsdirectory.edit.entry.error_field_ids', null);
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
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_ENTRY'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_ENTRY'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('entries');
            
        JToolBarHelper::apply('entry.apply');
        JToolBarHelper::save('entry.save');
        JToolBarHelper::save2new('entry.save2new');
        JToolBarHelper::save2copy('entry.save2copy');
        JToolBarHelper::cancel('entry.cancel');
    }
}
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Field view.
 */
class RSDirectoryViewField extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        // Get JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the field id.
        $id = $jinput->getInt('id');
            
        // Get the field type id.
        $field_type_id = $jinput->getInt('field_type_id');
            
            
        if (!$id && !$field_type_id)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
            
        if (!$field_type_id)
        {
            $field = $this->get('Item');
            $field_type_id = $field->field_type_id;
        }
            
        // Get the field type.
        $field_type = JTable::getInstance('FieldType', 'RSDirectoryTable');
        $field_type->load($field_type_id);
            
        if (!$field_type->id)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
            
        $doc = JFactory::getDocument();
            
        if ($field_type->type == 'map')
        {
            $doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.rsmap.js?v=' . RSDirectoryVersion::$version );
                
            $script = 'jQuery(function($)
            {
                jQuery( document.getElementById("rsdir-field-map") ).rsMap(
                {
                    zoom: parseInt( $( document.getElementById("jform_default_zoom") ).val() ),
                    inputAddress: document.getElementById("jform_default_address"),
                    inputLat: document.getElementById("jform_default_lat"),
                    inputLng: document.getElementById("jform_default_lng"),
                    markerDraggable: true,
                });
            });';
                
            // Add the script declaration.
            $doc->addScriptDeclaration($script);
        }
            
        $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/field.edit.js?v=' . RSDirectoryVersion::$version );
            
        $this->addToolBar($field_type->core);
        $this->id = $id;
        $this->is_dependency_parent = RSDirectoryHelper::isDependencyParent($id);
        $this->dependency_compatible = $this->get('DependencyCompatible');
        $this->field_type = $field_type;
        $this->rstabs = $this->get('RSTabs');
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
        $this->fieldsets = $this->form->getFieldsets();
            
        if ( in_array($field_type->type, $this->dependency_compatible) && $id )
        {
            $this->dependencies = RSDirectoryHelper::getDependencies($id);
        }
            
        parent::display($tpl);
    }
        
    /**
     * Add toolbar.
     *
     * @access protected
     * 
     * @param bool $core
     */
    protected function addToolBar($core = false)
    {
        // Set title.
        if ( JFactory::getApplication()->input->getInt('id') )
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_FIELD'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_FIELD'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('fields');
            
        JToolBarHelper::apply('field.apply');
        JToolBarHelper::save('field.save');
            
        // Show these buttons only for the custom fields.
        if (!$core)
        {
            JToolBarHelper::save2new('field.save2new');
            JToolBarHelper::save2copy('field.save2copy');
        }
            
        JToolBarHelper::cancel('field.cancel');
    }
}
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * File controller.
 */
class RSDirectoryControllerFile extends JControllerForm
{
   /**
    * The class constructor.
    *
    * @access public
    */
   public function __construct( $config = array() )
   {
      // Call the parent constructor.
      parent::__construct($config);
   }
      
   /**
    * Download file.
    *
    * @access public
    */
   public function download()
   {
      $app = JFactory::getApplication();
         
      // Get the file hash.
      $hash = $app->input->get('hash');
         
      // Get the file object.
      $file = RSDirectoryHelper::getFileObject(0, $hash);
         
      if (!$file)
      {
         JError::raiseError( 404, JText::_('COM_RSDIRECTORY_FILE_NOT_FOUND') );
      }
         
      // Set the source file path.
      if ($file->entry_id)
      {
         $file_path = JPATH_COMPONENT_SITE . "/files/entries/$file->entry_id/$file->file_name";    
      }
      else if ($file->category_id)
      {
         $file_path = JPATH_COMPONENT_SITE . "/files/images/categories/$file->file_name";    
      }
      else
      {
         JError::raiseError( 404, JText::_('COM_RSDIRECTORY_FILE_NOT_FOUND') );
      }
         
      if ( file_exists($file_path) )
      {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header( 'Content-Disposition: attachment; filename= "' . $file->original_file_name . '"' );
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         header( 'Content-Length: ' . filesize($file_path) );
         ob_clean();
         flush();
         readfile($file_path);
            
         $app->close();
      }
         
      JError::raiseError( 404, JText::_('COM_RSDIRECTORY_FILE_NOT_FOUND') );
   }
}
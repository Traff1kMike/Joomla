<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Image controller.
 */
class RSDirectoryControllerImage extends JControllerForm
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
     * View image.
     *
     * @access public
     */
    public function view()
    {
        $app = JFactory::getApplication();
        $size = $app->input->get('size');
        $hash = $app->input->get('hash');
            
        // Check the size and hash.
        if ( !in_array( $size, array('small', 'big', 'normal') ) || !$hash )
        {
            $app->close();
        }
         
        // Get the file.   
        $file = RSDirectoryHelper::getFileObject(0, $hash);
            
        if (!$file)
        {
            $app->close();
        }
            
        // Get an instance of the RSDirectory Config.
        $config = RSDirectoryConfig::getInstance();
            
        // Force Aspect Ratio .
        $far = 1;
            
        // Get file extension.
        $ext = JFile::getExt($file->file_name);
            
        if ($size == 'small')
        {
            $w = $config->get('small_thumbnail_width');
            $h = $config->get('small_thumbnail_height');
        }
        else if ($size == 'big')
        {
            $w = $config->get('big_thumbnail_width');
            $h = $config->get('big_thumbnail_height');
        }
        else
        {
            $w = $config->get('normal_thumbnail_width');
            $h = $config->get('normal_thumbnail_height');
            $far = 0;
        }
            
        $watermark_images = $config->get('watermark_images');
            
        if ($watermark_images && $size == 'normal')
        {
            $watermark_file = JPATH_BASE . '/' . $config->get('watermark');
            $watermark_position = $config->get('watermark_position');
            $watermark_opacity = $config->get('watermark_opacity');
            $watermark_size = $config->get('watermark_size');
                
            $file_name = hash('md5', $file->file_name . $watermark_file . $watermark_position . $watermark_opacity . $watermark_size) . ($ext ? ".$ext" : '');
        }
        else
        {
            $file_name = $file->file_name;
        }
            
        // Set the cache dir path. 
        $cache_dir = JPATH_ROOT   . "/components/com_rsdirectory/files/cache/{$w}x{$h}/";
            
        // Set the cache file path.
        $cache_file = $cache_dir . $file_name;
            
        if ( file_exists($cache_file) )
        {
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/phpthumb/phpthumb.functions.php';
                
            header( 'Content-Type: ' . phpthumb_functions::ImageTypeToMIMEtype($ext) );
            header('Content-Disposition: inline; filename="' . $file->file_name . '"');
                
            readfile($cache_file);
        }
        else
        {
            if ($file->entry_id)
            {
                $src = JPATH_COMPONENT_SITE  . "/files/entries/$file->entry_id/$file->file_name";
            }
            else if ($file->category_id)
            {
                $src = JPATH_COMPONENT_SITE  . "/files/images/categories/$file->file_name";
            }
                
                
            if ( empty($src) || !file_exists($src) )
            {
                $app->close();
            }
                
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/phpthumb/phpthumb.class.php';
                
            // Create a new phpThumb object.
            $phpThumb = new phpThumb();
            $phpThumb->src = $src;
            $phpThumb->w = $w;
            $phpThumb->h = $h;
            $phpThumb->far = $far;
                
            if ( in_array( $ext, array('png', 'gif') ) )
            {
                $phpThumb->fltr[] = 'stc';
                $phpThumb->f = $ext;
            }
                
            if ($size == 'normal' && $watermark_images)
            {  
                $phpThumb->fltr[] = "wmi|$watermark_file|$watermark_position|$watermark_opacity|$watermark_size";
            }
                
            // Generate the thumbnail.
            if ( $phpThumb->GenerateThumbnail() )
            {
                // Create the destination cache dir.
                if ( !file_exists($cache_dir) )
                {
                    jimport('joomla.filesystem.file');
                    jimport('joomla.filesystem.folder');
                        
                    JFolder::create($cache_dir);
                        
                    // Create a index file to prevent snooping around.
                    $buffer = '<html><body bgcolor="#FFFFFF"></body></html>';
                    JFile::write($cache_dir . 'index.html', $buffer);
                }
                   
                // Save the thumbnail to disk.
                if ( $phpThumb->RenderToFile($cache_file) )
                {
                    @chmod($cache_file, 0644);
                        
                    // Output the thumbnail.
                    $phpThumb->OutputThumbnail();
                }
            }
        }
            
        // Close the application.
        $app->close();
    }
}
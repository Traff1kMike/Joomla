<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Tools model.
 */
class RSDirectoryModelTools extends JModelForm
{
	/**
	 * An array of errors for the different sections of the tools page.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $errors = array(
		'backup' => array(),
		'restore' => array(),
	);
		
	/**
	 * The array of tables involved in the backup/restore process.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @var private
	 */
	private static $backup_tables = array(
		'#__categories',
		'#__rsdirectory_config',
		'#__rsdirectory_countries',
		'#__rsdirectory_credit_packages',
		'#__rsdirectory_email_messages',
		'#__rsdirectory_entries',
		'#__rsdirectory_entries_credits',
		'#__rsdirectory_entries_custom',
		'#__rsdirectory_entries_reported',
		'#__rsdirectory_favorites',
		'#__rsdirectory_fields',
		'#__rsdirectory_fields_dependencies',
		'#__rsdirectory_fields_properties',
		'#__rsdirectory_field_types',
		'#__rsdirectory_forms',
		'#__rsdirectory_forms_custom_fields',
		'#__rsdirectory_forms_fields',
		'#__rsdirectory_groups',
		'#__rsdirectory_groups_relations',
		'#__rsdirectory_reviews',
		'#__rsdirectory_uploaded_files',
		'#__rsdirectory_uploaded_files_categories_relations',
		'#__rsdirectory_uploaded_files_fields_relations',
		'#__rsdirectory_users',
		'#__rsdirectory_users_transactions',
	);
		
	/**
	 * Constructor
	 *
	 * @param array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @since 12.2
	 * @throws Exception
	 */
	public function __construct( $config = array() )
	{
		// Initialize backup paths.
		$this->backup_path = JPATH_ROOT . '/components/com_rsdirectory/files/backup/';
		$this->backup_tmp_path = $this->backup_path . 'tmp/';
		$this->backup_cache_path = $this->backup_path . 'cache/';
			
		// Initialize backup URLs.
		$this->backup_url = JURI::root(true) . '/components/com_rsdirectory/files/backup/';
		$this->backup_tmp_url = $this->backup_url . 'tmp/';
		$this->backup_cache_url = $this->backup_url . 'cache/';
			
		// Initialize restore paths.
		$this->restore_path = JPATH_ROOT . '/components/com_rsdirectory/files/restore/';
		$this->restore_tmp_path = $this->restore_path . 'tmp/';
			
		jimport('joomla.filesystem.folder');
			
		// Check paths.
		$paths = array(
			'backup' => array(
				$this->backup_path,
				$this->backup_tmp_path,
				$this->backup_cache_path,
			),
			'restore' => array(
				$this->restore_path,
				$this->restore_tmp_path,
			),
		);
			
		foreach ($paths as $section => $section_paths)
		{
			foreach ($section_paths as $path)
			{
				// Check if the required paths exists.
				if ( file_exists($path) )
				{
					// Check if the path is readable and writable.
					if ( !is_readable($path) || !is_writable($path) )
					{
						$this->errors[$section][] = JText::sprintf('COM_RSDIRECTORY_READABLE_WRITABLE_ERROR', $path);
					}
				}
				else
				{
					// Attempt to create directory.
					if ( JFolder::create($path) )
					{
						// Put an index.html file to prevent snooping around.
						file_put_contents("$path/index.html", '<html><body bgcolor="#FFFFFF"></body></html>');
					}
					else
					{
						$this->errors[$section][] = JText::sprintf('COM_RSDIRECTORY_REQUIRED_DIRECTORY_MISSING_ERROR', $path);	
					}
				}
			}
		}
			
		parent::__construct($config);
	}
		
    /**
     * Method to get a form object.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure.
     *
     * @since 1.6
     */
    public function getForm( $data = array(), $loadData = true )
    {
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.tools', 'tools', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
		
	/**
     * Regenerate titles.
     *
     * @access public
     *
     * @param array $forms_ids
     * @param array $elements
     * @param int $offset
     */
    public function regenerateTitles($forms_ids, $elements, $offset)
    {
        // Set the row limit.
        $limit = 100;
            
        if (!$forms_ids || !$elements)
            return;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Remove duplicates.
        $forms_ids = array_unique($forms_ids);
            
        // Quote values.
        foreach ($forms_ids as &$form_id)
        {
            $form_id = $db->q($form_id);
        }
            
        // Get count.
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
               ->where( $db->qn('form_id') . ' IN (' . implode(',', $forms_ids) . ')' );
               
        $db->setQuery($query);
        $total = $db->loadResult();
            
        $this->titles_regeneration_total = $total;
            
        if (!$total)
        {
            $this->titles_regeneration_completition = 100;
            return;
        }
            
        $select = array(
            $db->qn('e') . '.*',
            $db->qn('ec') . '.*',
            $db->qn('c.title', 'category_title'),
            $db->qn('c.path', 'category_path'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
               ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
               ->where( $db->qn('form_id') . ' IN (' . implode(',', $forms_ids) . ')' );
               
        $db->setQuery($query, $offset, $limit);
            
        $entries = $db->loadObjectList();
            
        $numrows = count($entries);
            
        $progress = $offset + $numrows;
        $progress = $progress > $total ? $total : $progress;
            
        $this->titles_regeneration_progress = $progress;
        $this->titles_regeneration_completition = number_format( ($progress * 100) / $total, 1 );
            
        if ($entries)
        {
            RSDirectoryHelper::regenerateEntriesTitles($entries, $elements);
        }
    }
        
    /**
     * Return titles regeneration progress data (completition, progress, total).
     *
     * @access public
     *
     * @return array
     */
    public function getTitlesRegenerationProgress()
    {
        return array(
            'completition' => isset($this->titles_regeneration_completition) ? $this->titles_regeneration_completition : 0,
            'progress' => isset($this->titles_regeneration_progress) ? $this->titles_regeneration_progress : 0,
            'total' => isset($this->titles_regeneration_total) ? $this->titles_regeneration_total : 0,
        );
    }
		
	/**
     * Backup data.
     *
     * @access public
     *
     * @param int $offset
     */
    public function backup($offset)
    {
		$app = JFactory::getApplication();
			
		$hash = $app->getUserState('com_rsdirectory.backup.hash');
		$backup_cache_path = $this->backup_cache_path . "$hash/";
			
		$total = count(self::$backup_tables);
		$this->backup_total = $total;
			
		if ( !empty(self::$backup_tables[$offset]) )
		{
			$table = self::$backup_tables[$offset];
				
			$db = JFactory::getDbo();
				
			jimport('joomla.filesystem.file');
				
			// Backup just started.
			if (!$offset)
			{
				// Empty the tmp folder.	
				$files = array_diff( scandir($this->backup_tmp_path), array('..', '.', 'index.html') );
					
				if ($files)
				{
					foreach ($files as $file)
					{
						JFile::delete($this->backup_tmp_path . $file);
					}
				}
					
				$hash = RSDirectoryHelper::getHash();
				$backup_cache_path = $this->backup_cache_path . "$hash/";
					
				$app->setUserState('com_rsdirectory.backup.hash', $hash);
					
				// Attempt to create directory.
				if ( JFolder::create($backup_cache_path) )
				{
					// Put an index.html file to prevent snooping around.
					file_put_contents("$backup_cache_path/index.html", '<html><body bgcolor="#FFFFFF"></body></html>');
				}
			}
				
			$fp = fopen($this->backup_tmp_path . "$hash-$table.csv", 'w');
				
			// Put the column names on the 1st line of the CSV file.
			$columns = array_keys( $db->getTableColumns($table) );
			fputcsv($fp, $columns);
				
			$query = $db->getQuery(true)
				   ->select('*')
				   ->from( $db->qn($table) );
					
			if ($table == '#__categories')
			{
				$query->where( $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') );
			}
				
			$db->setQuery($query);
				
			$rows = $db->loadObjectList();
				
			if ($rows)
			{
				foreach ($rows as $row)
				{
					fputcsv( $fp, (array)$row );
				}
			}
				
			fclose($fp);
		}
			
		// Archive the files at the end.
		if ($offset == $total - 1)
		{
			jimport('joomla.filesystem.archive');
			$zip = JArchive::getAdapter('zip');
				
			// Get the files.
			$files_list = array_diff( scandir($this->backup_tmp_path), array('..', '.', 'index.html') );
				
			$files = array();
				
			// Build the files array required by the zip adapter.
			foreach ($files_list as $file)
			{
				list(,$file_name) = explode('-', $file);
					
				$files[] = array(
					'data' => file_get_contents($this->backup_tmp_path . $file),
					'name' => $file_name,
				);
			}
				
			$date = JFactory::getDate();
			$file_name = $date->format('Y-m-d_H-i-s') . '.zip';
				
			// Archive the files.
			$zip->create($backup_cache_path . $file_name, $files);
				
			$file = (object)array(
				'path' => $backup_cache_path . $file_name,
				'url' => $this->backup_cache_url . "$hash/" . $file_name,
				'name' => $file_name,
				'date' => $date->toSql(),
				'hash' => $hash,
			);
				
			$this->backup_archive_html = RSDirectoryHelper::getBackupCachedFileRowHTML($file);
				
			// Clear the backup hash.
			$app->setUserState('com_rsdirectory.backup.hash', null);
		}
			
		$progress = $offset + 1;
        $progress = $progress > $total ? $total : $progress;
            
        $this->backup_progress = $progress;
        $this->backup_completition = number_format( ($progress * 100) / $total, 1 );
	}
		
	/**
     * Return backup progress data (completition, progress, total, archive_html).
     *
     * @access public
     *
     * @return array
     */
    public function getBackupProgress()
    {
        return array(
            'completition' => isset($this->backup_completition) ? $this->backup_completition : 0,
            'progress' => isset($this->backup_progress) ? $this->backup_progress : 0,
            'total' => isset($this->backup_total) ? $this->backup_total : 0,
			'archive_html' => isset($this->backup_archive_html) ? $this->backup_archive_html : null,
        );
    }
		
	/**
	 * Return an array of backup errors.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getBackupErrors()
	{
		return $this->errors['backup'];
	}
		
	/**
	 * Return an array containing the names and paths to the backup cached files.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getBackupCachedFiles()
	{
		if ( !file_exists($this->backup_cache_path) )
			return;
			
		// Get the backup cached directories.
		$directories_list = array_diff( scandir($this->backup_cache_path), array('..', '.', 'index.html') );
			
		$files = array();
			
		if ($directories_list)
		{			
			foreach ($directories_list as $directory)
			{
				if ( !is_dir($this->backup_cache_path . $directory) )
					continue;
					
				// Get the backup cached directories.
				$files_list = array_diff( scandir($this->backup_cache_path . $directory), array('..', '.', 'index.html') );
					
				// Reset keys.
				$files_list = array_values($files_list);
					
				// Sort by file name.
				usort($files_list, 'strcmp');
					
				// Sort desc.
				$files_list = array_reverse($files_list);
					
				foreach ($files_list as $file)
				{
					$backup_cache_path = $this->backup_cache_path . "$directory/";
						
					$files[] = (object)array(
						'path' => $backup_cache_path . $file,
						'url' => $this->backup_cache_url . $directory . "/$file",
						'name' => $file,
						'date' => JFactory::getDate( filemtime($backup_cache_path . $file) )->toSql(),
						'hash' => $directory,
					);
				}
			}
				
			uasort( $files, array($this, 'sortCachedBackupFiles') );
				
			// Reset keys.
			$files = array_values($files);
		}
			
		return $files;
	}
		
	/**
	 * Delete the selected cached backup files.
	 *
	 * @access public
	 *
	 * @param array $hashes
	 *
	 * @return bool
	 */
	public function deleteBackupFiles($hashes)
	{
		if ( !is_array($hashes) || empty($hashes) )
			return false;
			
		jimport('joomla.filesystem.folder');
			
		foreach ($hashes as $hash)
		{
			if ( file_exists($this->backup_cache_path . $hash) )
			{
				JFolder::delete($this->backup_cache_path . $hash);
			}
		}
			
		return true;
	}
		
	/**
	 * Sort the cached backup files descending by timestamp.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param array $a
	 * $param array $b
	 *
	 * @return int
	 */	
	public function sortCachedBackupFiles($a, $b)
	{
		$a_time = JFactory::getDate($a->date)->toUnix();
		$b_time = JFactory::getDate($b->date)->toUnix();
			
		if ($a_time == $b_time)
			return 0;
			
		return $a_time > $b_time ? -1 : 1;
	}
		
	/**
	 * Get the names of the restore hidden fields.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getRestoreHiddenFields()
	{
		return array('restore_local_archive', 'restore_url');	
	}
		
	/**
	 * Return an array of backup errors.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getRestoreErrors()
	{
		return $this->errors['restore'];
	}
		
	/**
	 * Initialize the restore process for an uploaded archive.
	 *
	 * @access public
	 *
	 * @param array $files
	 * 
	 * @return bool
	 */
	public function restoreInitUploadedArchive($files)
	{
		if ( empty($files['restore_uploaded_archive']['name']) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_UPLOAD_FAILED') );
		}
		else
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/zip.php';
				
			$file = $files['restore_uploaded_archive'];
				
			// Delete old tmp files.
			$tmp_files = array_diff( scandir($this->restore_tmp_path), array('..', '.', 'index.html') );
				
			if ($tmp_files)
			{
				foreach ($tmp_files as $tmp_file)
				{
					if ( is_dir($this->restore_tmp_path . $tmp_file) )
					{
						JFolder::delete($this->restore_tmp_path . $tmp_file);
					}
					else
					{
						JFile::delete($this->restore_tmp_path . $tmp_file);
					}
				}
			}
				
			$zip = new RSDirectoryZip;
				
			$hash = RSDirectoryHelper::getHash();
			JFactory::getApplication()->setUserState('com_rsdirectory.restore.hash', $hash);
				
			// Allow only csv files.
			$options = array(
				'allowedFileTypes' => array(
					'csv',
				),
				'fileNamePrefix' =>  "$hash-",
			);
				
			// Extract files.
			if ( $zip->extract($file['tmp_name'], $this->restore_tmp_path, $options) === true )
			{
				$files = array_diff( scandir($this->restore_tmp_path), array('..', '.', 'index.html') );
					
				if ( count($files) )
				{
					return true;
				}
				else
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EMPTY_EXTRACTION') );
				}
			}
			// Extraction failed.
			else
			{
				$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EXTRACTION_FAILED') );
			}
		}
			
		return false;
	}
		
	/**
	 * Initialize the restore process for a local or remote CSV archive.
	 *
	 * @access public
	 *
	 * @param array $data
	 * 
	 * @return bool
	 */
	public function restoreInit($data)
	{
		if ( empty($data['restore_from']) || !in_array( $data['restore_from'], array('local_archive', 'url') ) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
		}
		else
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/zip.php';
				
			// Delete old tmp files.
			$tmp_files = array_diff( scandir($this->restore_tmp_path), array('..', '.', 'index.html') );
				
			if ($tmp_files)
			{
				foreach ($tmp_files as $tmp_file)
				{
					if ( is_dir($this->restore_tmp_path . $tmp_file) )
					{
						JFolder::delete($this->restore_tmp_path . $tmp_file);
					}
					else
					{
						JFile::delete($this->restore_tmp_path . $tmp_file);
					}
				}
			}
				
			$zip = new RSDirectoryZip;
				
			$hash = RSDirectoryHelper::getHash();
			JFactory::getApplication()->setUserState('com_rsdirectory.restore.hash', $hash);
				
			// Allow only csv files.
			$options = array(
				'allowedFileTypes' => array(
					'csv',
				),
				'fileNamePrefix' =>  "$hash-",
			);
				
			// Process a local archive.
			if ($data['restore_from'] == 'local_archive')
			{
				// Path not provided.
				if ( empty($data['restore_local_archive']) || !trim($data['restore_local_archive']) )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_NO_PATH_PROVIDED') );
				}
				// File does not exist at the specified path.
				else if ( !file_exists($data['restore_local_archive']) )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_INVALID_PATH') );
				}
				// The file is unreadable.
				else if ( !is_readable($data['restore_local_archive']) )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_FILE_UNREADABLE') );
				}
				// Extraction failed.
				else if ( $zip->extract($data['restore_local_archive'], $this->restore_tmp_path, $options) !== true )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EXTRACTION_FAILED') );
				}
				else
				{
					$files = array_diff( scandir($this->restore_tmp_path), array('..', '.', 'index.html') );
						
					if ( count($files) )
					{
						return true;
					}
					// No valid files extracted.
					else
					{
						$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EMPTY_EXTRACTION') );
					}
				}
			}
			// Process a remote URL.
			else
			{
				// cURL is not installed or it's disabled.
				if ( !function_exists('curl_init') )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_CURL_ERROR') );
				}
				// URL not provided.
				else if ( empty($data['restore_url']) || !trim($data['restore_url']) )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_NO_URL_PROVIDED') );
				}
				else
				{
					$curl = curl_init($data['restore_url']); 
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					$output = curl_exec($curl);
					curl_close($curl);
						
					if ($output)
					{
						$tmp_archive = tempnam( sys_get_temp_dir(), 'csv' );
							
						if ( file_put_contents($tmp_archive, $output) )	
						{
							if ( $zip->extract($tmp_archive, $this->restore_tmp_path, $options) === true )
							{
								$files = array_diff( scandir($this->restore_tmp_path), array('..', '.', 'index.html') );
									
								if ( count($files) )
								{
									return true;
								}
								// No valid files extracted.
								else
								{
									$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EMPTY_EXTRACTION') );
								}
							}
							// Extraction failed.
							else
							{
								$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_EXTRACTION_FAILED') );
							}
						}
						// Tmp archive could not be written.
						else
						{
							$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_TMP_ARCHIVE_WRITE_ERROR') );
						}
					}
					else
					{
						$this->setError( JText::_('COM_RSDIRECTORY_RESTORE_FETCH_ERROR') );
					}
				}
			}
		}
			
		return false;
	}
		
	/**
	 * Verify the restore backup data.
	 *
	 * @access public
	 *
	 * @param int $offset
	 *
	 * @return bool
	 */	
	public function restoreVerify($offset)
	{
		// Check if there's a valid table for the provided offset.
		if ( isset(self::$backup_tables[$offset]) )
		{
			$hash = JFactory::getApplication()->getUserState('com_rsdirectory.restore.hash');
				
			$table = self::$backup_tables[$offset];
			$file = "$table.csv";
				
			$path = "$this->restore_tmp_path/$hash-$file";
				
			// Check if the file exisists.
			if ( file_exists($path) )
			{
				$fp = fopen($path, 'r');
					
				// Check table structure.
				if ($fp !== false)
				{
					$db = JFactory::getDbo();
						
					// Get the table columns from the CSV file.
					$csv_columns = fgetcsv($fp);
						
					// Get the table columns from the database.
					$columns = array_keys( $db->getTableColumns($table) );
						
					$check = true;
						
					// Check table structure.
					foreach ($columns as $column)
					{
						// Skip the custom fields columns.
						if ( $table == '#__rsdirectory_entries_custom' && substr($column, 0, 2) == 'f_' )
							continue;
							
						if ( !in_array($column, $csv_columns) )
						{
							$check = false;
							break;
						}
					}
						
					fclose($fp);
						
					if ($check)
					{
						return true;
					}
					else
					{
						$this->setError( JText::sprintf('COM_RSDIRECTORY_RESTORE_INVALID_STRUCTURE', $file) );
					}
				}
				else
				{
					$this->setError( JText::sprintf('COM_RSDIRECTORY_RESTORE_ERROR_OPENING_FILE', $file) );
				}
			}
			else
			{
				$this->setError( JText::sprintf('COM_RSDIRECTORY_RESTORE_MISSING_FILE', $file) );
			}	
		}
		else
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
		}
			
		return false;
	}
		
	/**
	 * Measure the backup restore data.
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function restoreMeasure()
	{
		$app = JFactory::getApplication();
		$hash = $app->getUserState('com_rsdirectory.restore.hash');
			
		$totals = array();
			
		$total = 0;
			
		foreach (self::$backup_tables as $table)
		{
			$fp = fopen("$this->restore_tmp_path/$hash-$table.csv", 'r');
				
			// Get the table columns from the CSV file.
			$columns = fgetcsv($fp);
				
			// Reset count.
			$count = 0;
				
			// Count table rows.
			while ( $row = fgetcsv($fp) )
			{
				$count++;
			}
				
			$totals[$table] = $count;
			$total += $count;
				
			fclose($fp);
		}
			
		$totals['total'] = $total;
			
		$app->setUserState('com_rsdirectory.measure.restore.totals', $totals);
			
		return true;
	}
		
	/**
	 * Restore backup data.
	 *
	 * @access public
	 *
	 * @param string $table
	 * @param int $table_offset
	 * @param int $offset
	 *
	 * @return bool
	 */
	public function restore($table, $table_offset, $offset)
	{
		$db = JFactory::getDbo();
			
		// Empty the backup restore temporary table.
		if (!$offset)
		{
			$db->setQuery( 'TRUNCATE TABLE ' . $db->qn('#__rsdirectory_restore_tmp') );
			$db->execute();
		}
			
		// Check if the provided table is valid.
		if ( in_array($table, self::$backup_tables) )
		{
			$app = JFactory::getApplication();
			$hash = $app->getUserState('com_rsdirectory.restore.hash');
				
			$file = "$table.csv";
				
			$path = "$this->restore_tmp_path/$hash-$file";
				
			$fp = fopen($path, 'r');
				
			// Get the table columns from the CSV file.
			$columns = fgetcsv($fp);
				
			// Initialize the values list.
			$list = array();
				
			while ( $row = fgetcsv($fp) )
			{
				$values = array();
					
				foreach ($row as $i => $value)
				{
					$values[$columns[$i]] = $value;
				}
					
				$list[] = $values;
			}
				
			fclose($fp);
				
			if ($table == '#__categories')
			{
				$limit = 50;
					
				// Cut a piece of the array.	
				$list = array_slice($list, $table_offset, $limit);
					
				// Get an instance of the RSDirectory! model.
				$category_model = RSDirectoryModel::getInstance('Category');
					
				if (!$table_offset)
				{
					// Delete all RSDirectory! categories.
					$category_model->deleteAll();
						
					// Rebuild
					$category_model->rebuild();
				}
					
				if ($list)
				{
					// Sort the categories ascending by the lft column.
					uasort( $list, array('self', 'sortCategories') );
						
					$parents = array();
						
					$query = $db->getQuery(true)
						   ->select('*')
					       ->from( $db->qn('#__rsdirectory_restore_tmp') )
						   ->where( $db->qn('table') . ' = ' . $db->q($table) );
						
					$db->setQuery($query);
						
					if ( $results = $db->loadObjectList() )
					{
						foreach ($results as $result)
						{
							$parents[$result->id_old] = $result->id_new;
						}
					}
						
					foreach ($list as $category)
					{
						$tmp = (object)array(
							'id_old' => $category['id'],
							'table' => $table,
						);
						
						$category['metadata'] = (array)json_decode($category['metadata']);
						$category['params'] = (array)json_decode($category['params']);	
						$params = $category['params'];
							
						unset($category['id']);
						unset($category['asset_id']);
						unset($category['lft']);
						unset($category['rgt']);
						unset($category['params']['thumbnail']);
							
						if ( isset($parents[$category['parent_id']]) )
						{
							$category['parent_id'] = $parents[$category['parent_id']];
						}
							
						// Insert category.
						if ( $category_model->save($category) )
						{
							// Get the id of the newly inserted category.
							$tmp->id_new = $category_model->getState( $category_model->getName() . '.id' );
								
							if ( !empty($params['thumbnail']) )
							{
								$query = $db->getQuery(true)
									   ->update( $db->qn($table) )
									   ->set( $db->qn('params') . ' = ' . $db->q( json_encode($params) ) )
									   ->where( $db->qn('id') . ' = ' . $db->q($tmp->id_new) );
										
								$db->setQuery($query);
								$db->execute();
							}
								
							$parents[$tmp->id_old] = $tmp->id_new;
								
							// Clear the id, so it inserts a new category.
							$category_model->setState( $category_model->getName() . '.id', null );
								
							$db->insertObject('#__rsdirectory_restore_tmp', $tmp);
						}
					}
				}
			}
			else
			{
				$limit = 1000;
					
				// Cut a piece of the array.	
				$list = array_slice($list, $table_offset, $limit);
					
				// Delete all data from the table.
				if (!$table_offset)
				{
					$db->setQuery( 'TRUNCATE TABLE ' . $db->qn($table) );
					$db->execute();	
				}
					
				if ( $list && in_array('category_id', $columns) )
				{
					// Update the categories ids for all entries.
					$query = $db->getQuery(true)
					       ->select('*')
						   ->from( $db->qn('#__rsdirectory_restore_tmp') )
						   ->where( $db->qn('table') . ' = ' . $db->q('#__categories') );
						   
					$db->setQuery($query);
						
					$categories = $db->loadObjectList();
						
					foreach ($list as &$object)
					{
						foreach ($categories as $category)
						{
							if ($object['category_id'] == $category->id_old)
							{
								$object['category_id'] = $category->id_new;
								break;
							}
						}
					}
				}
					
				if ($table == '#__rsdirectory_entries_custom')
				{
					// Get the table columns from the database.
					$table_columns = array_keys( $db->getTableColumns($table) );
						
					$deleted_columns = array();
						
					foreach ($table_columns as $column)
					{
						if ( substr($column, 0, 2) == 'f_' )
						{
							$deleted_columns[] = $db->qn($column);
						}
					}
						
					// Drop custom fields columns.
					if ($deleted_columns)
					{
						$db->setQuery( 'ALTER TABLE ' . $db->qn($table) . ' DROP ' . implode( ', DROP', $deleted_columns) );
						$db->execute();
					}
						
					$new_columns = array();
						
					foreach ($columns as $column)
					{
						if ( substr($column, 0, 2) != 'f_' )
							continue;
							
						if ( substr($column, -4) == '_lat' || substr($column, -4) == '_lng' )
						{
							$new_columns[] = $db->qn($column) . " FLOAT(10, 6) NOT NULL";
						}
						else
						{
							$new_columns[] = $db->qn($column) . " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
						}
					}
						
					if ($new_columns)
					{
						$db->setQuery( 'ALTER TABLE ' . $db->qn($table) . ' ADD ' . implode( ', ADD', $new_columns) );
						$db->execute();
					}
				}
					
				// Insert values.
				if ($list)
				{
					$values_list = array();
						
					foreach ($list as $values)
					{
						$insert_values = array();
							
						foreach ($columns as $column)
						{
							$insert_values[] = $db->q($values[$column]);
						}
							
						$values_list[] = '(' . implode(',', $insert_values) . ')';
					}
						
					$query = ' INSERT INTO ' . $db->qn($table) . '(' . implode( ',', $db->qn($columns) ) . ') VALUES ' . implode(',', $values_list);
					$db->setQuery($query);
					$db->execute();
				}
			}
				
			$count = count($list);
			$totals = $app->getUserState('com_rsdirectory.measure.restore.totals');
				
			$this->restore_total = $totals['total'];
				
			$progress = $offset + $count;
			$progress = $progress > $totals['total'] ? $totals['total'] : $progress;
				
			$this->restore_progress = $progress;
			$this->restore_completition = $totals['total'] ? number_format( ($progress * 100) / $totals['total'], 1 ) : 100.0;
				
			$table_progress = $table_offset + $count;
			$table_progress = $table_progress > $totals[$table] ? $totals[$table] : $table_progress;
				
			$this->restore_table_progress = $table_progress;
			$this->restore_table_completition = $totals[$table] ? number_format( ($table_progress * 100) / $totals[$table], 1 ) : 100.0;
				
			return true;
		}
		else
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
		}
			
		return false;
	}
		
	/**
     * Return restore progress data (completition, progress, total).
     *
     * @access public
     *
     * @return array
     */
    public function getRestoreProgress()
    {
        return array(
            'completition' => isset($this->restore_completition) ? $this->restore_completition : 0.0,
            'progress' => isset($this->restore_progress) ? $this->restore_progress : 0,
            'total' => isset($this->restore_total) ? $this->restore_total : 0,
			'table_completition' => isset($this->restore_table_completition) ? $this->restore_table_completition : 0.0,
			'table_progress' => isset($this->restore_table_progress) ? $this->restore_table_progress : 0,
        );
    }
		
	/**
	 * Returns the backup/restore tables array.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getBackupTables()
	{
		return self::$backup_tables;	
	}
		
	/**
	 * Sort categories ascending by the lft column.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param array $a
	 * $param array $b
	 *
	 * @return int
	 */	
	public static function sortCategories($a, $b)
	{
		if ($a['lft'] == $b['lft'])
			return 0;
			
		return $a['lft'] < $b['lft'] ? -1 : 1;
	}
		
	/**
	 * Get import options.
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getImportOptions()
	{
		// Retrieve the import options from the import plugins.
        $import_options = JFactory::getApplication()->triggerEvent('rsdirectory_addImportOptions');
            
        if ($import_options)
        {
            foreach ($import_options as $i => $item)
            {
                if ( empty($item->value) )
                {
                    unset($import_options[$i]);
                }
            }
        }
            
        return $import_options;
	}
		
	/**
     * Get RSFieldset.
     *
     * @access public
     * 
     * @return RSFieldset
     */
    public function getRSFieldset()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/fieldset.php');
            
        return new RSFieldset();
    }
        
    /**
     * Get RSTabs.
     *
     * @access public
     * 
     * @return RSTabs
     */
    public function getRSTabs()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php');
            
        return new RSTabs('com-rsdirectory-configuration');
    }
        
    /**
     * Get sidebar.
     *
     * @access public
     * 
     * @return string
     */
    public function getSideBar()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php');
            
        return RSDirectoryToolbarHelper::render();
    }
}
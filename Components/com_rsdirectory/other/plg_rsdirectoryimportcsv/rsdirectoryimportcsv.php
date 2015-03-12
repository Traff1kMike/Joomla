<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * RSDirectory! CSV Import Plugin.
 */
class plgSystemRSDirectoryImportCSV extends JPlugin
{
    const IMPORT_OPTION = 'csv';
    const PLUGIN = 'rsdirectoryimportcsv';
    const EXTENSION = 'plg_system_rsdirectoryimportcsv';
    const OPTION = 'PLG_SYSTEM_RSDIRECTORYIMPORTCSV_NAME';
	const VERSION = '1.0.0';
		
	/**
	 * An errors array.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $errors = array();
		
	/**
	 * Automatically load language files.
	 *
	 * @access protected
	 *
	 * @var boolean
	 */
	protected $autoloadLanguage = true;
		
	/**
	 * An array of columns to skip.
	 *
	 * @access protected
	 *
	 * @static
	 *
	 * @var array
	 */
	protected static $skippedColumns = array(
		'category_id',
		'form_id'
	);
        
    /**
     * Class constructor.
     * 
     * @access public
     * 
     * @param object &$subject
     * @param array $config
     */
    public function __construct(&$subject, $config)
    {
		if ( $this->canRun() )
		{
			$lang = JFactory::getLanguage();
			$lang->load('com_rsdirectory', JPATH_ADMINISTRATOR);
			$lang->load(self::EXTENSION, JPATH_ADMINISTRATOR);
				
			// Initialize import path.
			$this->import_path = JPATH_ROOT . '/components/com_rsdirectory/files/import/';
		}
			
		parent::__construct($subject, $config);
    }
        
    /**
     * Can the plugin run?
     *
     * @access public
     * 
     * @return bool
     */
    public function canRun()
    {
		$app = JFactory::getApplication();
			
        return file_exists(JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php') &&
			   $app->isAdmin() && $app->input->get('option') == 'com_rsdirectory' &&
		       JPluginHelper::isEnabled('system', self::PLUGIN);
    }
     
    /**
     * Add the current import option to the import options list.
     *
     * @access public
     * 
     * @return string
     */
    public function rsdirectory_addImportOptions()
    {
		$lang = JFactory::getLanguage();
		$lang->load(self::EXTENSION, JPATH_ADMINISTRATOR);
			
		if ( $this->canRun() )
			return JHTML::_( 'select.option', self::IMPORT_OPTION, JText::_(self::OPTION) );
    }
		
	/**
	 * Display import fieldset.
	 *
	 * @access public
	 */	
	public function rsdirectory_displayImportFieldset()
	{
		if ( !$this->canRun() )
			return;
			
		$doc = JFactory::getDocument();
			
		$style = '#csvSelectColumns {
					position: relative;
					background: #fff;
					padding: 20px;
					width: auto;
					margin: 20px auto;
				}
					
				#csvSelectColumns .control-group {
					margin: 0;
				}';
					
		$doc->addStyleDeclaration($style);
		$doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/magnific-popup.css?v=' . RSDirectoryVersion::$version );	
		$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.magnific-popup.min.js?v=' . RSDirectoryVersion::$version );	
		$doc->addScript( JURI::root(true) . '/media/plg_rsdirectoryimportcsv/js/script.js?v=' . self::VERSION );
			
		$categories = self::getCategoriesOptions();
			
		?>
			
		<fieldset id="csv" class="import-fieldset hide">
				
			<div class="control-group">
					
				<div class="control-label">
					<label for="csv_from"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_FROM_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<select id="csv_from" name="jform[csv][from]">
						<option value="uploaded_file"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_UPLOADED_FILE'); ?></option>
						<option value="local_file"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_LOCAL_FILE'); ?></option>
						<option value="url"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_URL'); ?></option>
					</select>
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label class="" for="csv_uploaded_file"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_FILE_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_uploaded_file" type="file" name="jform[csv][uploaded_file]" />
				</div>
					
			</div>
				
			<div class="control-group hide">
					
				<div class="control-label">
					<label for="csv_local_file"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_LOCAL_FILE_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_local_file" type="text" name="jform[csv][local_file]" />
				</div>
					
			</div>
				
			<div class="control-group hide">
					
				<div class="control-label">
					<label for="csv_url"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IMPORT_URL_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_url" type="url" name="jform[csv][url]" />
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" for="csv_category_id" title="<?php echo RSDirectoryHelper::getTooltipText( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CATEGORY_DESC') ); ?>">
						<?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CATEGORY_LABEL'); ?>
					</label>
				</div>
					
				<div class="controls">
					<select id="csv_category_id" name="jform[csv][category_id]">
						<option value=""><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CATEGORY_OPTION'); ?></option>
						<?php echo JHtml::_('select.options', $categories); ?>
					</select>
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label>
						<?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_IGNORE_FIRST_LINE'); ?>
					</label>
				</div>
					
				<div class="controls">
					<label class="radio">
						<?php echo JText::_('JNO'); ?>
						<input type="radio" name="jform[csv][ignore_first_line]" value="0" />
					</label>
						
					<label class="radio">
						<?php echo JText::_('JYES'); ?>
						<input type="radio" name="jform[csv][ignore_first_line]" value="1" />
					</label>
				</div>
					
			</div>
			
			<div class="control-group">
					
				<div class="control-label">
					<label class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_REGENERATE_TITLES_DESC') ); ?>">
						<?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_REGENERATE_TITLES_LABEL'); ?>
					</label>
				</div>
					
				<div class="controls">
					<label class="radio">
						<?php echo JText::_('JNO'); ?>
						<input type="radio" name="jform[csv][regenerate_titles]" value="0" checked="checked" />
					</label>
						
					<label class="radio">
						<?php echo JText::_('JYES'); ?>
						<input type="radio" name="jform[csv][regenerate_titles]" value="1" />
					</label>
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label for="csv_delimiter"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_DELIMITER_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_delimiter" type="text" name="jform[csv][delimiter]" value="," />
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label for="csv_enclosure"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_ENCLOSURE_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_enclosure" type="text" name="jform[csv][enclosure]" value='"' />
				</div>
					
			</div>
				
			<div class="control-group">
					
				<div class="control-label">
					<label for="csv_escape"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_ESCAPE_LABEL'); ?></label>
				</div>
					
				<div class="controls">
					<input id="csv_escape" type="text" name="jform[csv][escape]" value="\" />
				</div>
					
			</div>
				
			<iframe id="csv_upload_target" name="csv_upload_target" src="#" style="width: 0; height: 0; border: none;"></iframe>
				
			<div id="csvSelectColumns" class="mfp-hide"></div>
				
		</fieldset>
			
		<?php
			
		JText::script('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_REQUIRED_FIELDS_ERROR');
		JText::script('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_SELECT_COLUMNS');
		JText::script('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_DUPLICATES_COLUMNS_ERROR');
		JText::script('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMNS_SELECTION_ERROR');
		JText::script('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMNS_SELECTED');
		JText::script('COM_RSDIRECTORY_IMPORTING_DATA');
	}
		
	/**
	 * Import.
	 *
	 * @access public
	 *
	 * @param object $vars
	 */
	public function rsdirectory_import($vars)
	{
		// Do a few checks.
		if ( !$this->canRun() || empty($vars->import_from) || $vars->import_from != self::IMPORT_OPTION )
			return;
			
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get form data.
		$jform_data = $app->input->get( 'jform', array(), 'array' );
			
		// Get plugin form data.
		$data = empty($jform_data[self::IMPORT_OPTION]) ? array() : $jform_data[self::IMPORT_OPTION];
			
		switch ($vars->import_action)
		{
			case 'measure':
					
				$this->measure($vars, $data);
				break;
					
			case 'selectColumns':
					
				$this->selectColumns($vars, $data);
				break;
					
			case 'import':
					
				$this->import($vars, $data);
				break;
					
			default:
					
				$this->init($vars, $data);
				break;
		}
	}
		
	/**
	 * Method to initialize the import.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 */
	protected function init($vars, $data)
	{
		if ( empty($data['from']) || !in_array( $data['from'], array('uploaded_file', 'local_file', 'url') ) )
			return;
			
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
			
		// Delete old tmp files.
		$tmp_files = array_diff( scandir($this->import_path), array('..', '.', 'index.html') );
			
		if ($tmp_files)
		{
			foreach ($tmp_files as $tmp_file)
			{
				if ( is_dir($this->import_path . $tmp_file) )
				{
					JFolder::delete($this->import_path . $tmp_file);
				}
				else
				{
					JFile::delete($this->import_path . $tmp_file);
				}
			}
		}
			
		// Init uploaded file.
		if ($data['from'] == 'uploaded_file')
		{
			echo '<script type="text/javascript">';
				
			if ( $this->initUploadedFile() )
			{
				echo 'window.top.window.addLog( window.top.document.getElementById("import-log"), "' . RSDirectoryHelper::escapeHTML( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_FILE_UPLOADED') ) . '", "success" );';
				echo 'window.top.window.measureCSVImport();';
			}
			else
			{
				$errors = $this->getErrors();
					
				foreach ($errors as $error)
				{
					echo 'window.top.window.addLog( window.top.document.getElementById("import-log"), "' . RSDirectoryHelper::escapeHTML($error) . '", "error" );';
				}
					
				echo 'window.top.window.abortImport("' . RSDirectoryHelper::escapeHTML( JText::_('COM_RSDIRECTORY_IMPORT_ABORTED') ) . '");';
			}
				
			echo '</script>';
		}
		// Init local file.
		else if ($data['from'] == 'local_file')
		{
			$messages = $vars->response['messages'];
				
			if ( $this->initLocalFile($vars, $data) )
			{
				$vars->response['action'] = 'measure';
			}
			else
			{
				$errors = $this->getErrors();
					
				foreach ($errors as $error)
				{
					$messages[] = array(
						'message' => $error,
						'type' => 'error',
						'id' => null,
					);
				}
			}
				
			$vars->response['messages'] = $messages;
		}
		// Init remote file.
		else
		{
			$messages = $vars->response['messages'];
				
			if ( $this->initRemoteFile($vars, $data) )
			{
				$messages[] = array(
					'message' => JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_FILE_FETCHED'),
					'type' => 'success',
					'id' => null,
				);
					
				$vars->response['action'] = 'measure';
			}
			else
			{
				$errors = $this->getErrors();
					
				foreach ($errors as $error)
				{
					$messages[] = array(
						'message' => $error,
						'type' => 'error',
						'id' => null,
					);
				}
			}
				
			$vars->response['messages'] = $messages;
		}
	}
		
	/**
	 * Method to initialize the import of an uploaded file.
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function initUploadedFile()
	{
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get files.
		$jform_files = $app->input->files->get( 'jform', array(), 'array' );
			
		// Get plugin files.
		$files = empty($jform_files[self::IMPORT_OPTION]) ? array() : $jform_files[self::IMPORT_OPTION];
			
		if ( empty($files['uploaded_file']['name']) )
		{
			$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_UPLOAD_FAILED') );
		}
		else
		{
			$file = $files['uploaded_file'];
				
			// Generate random hash.
			$hash = RSDirectoryHelper::getHash();
				
			// Set new file name.
			$file_name = "$hash.csv";
				
			// Set the file path.
			$dest = $this->import_path . $file_name;
				
			if ( JFile::upload($file['tmp_name'], $dest) )
			{
				// Remember the file name.
				$app->setUserState('com_rsdirectory.import.csv.file_path', $dest);
					
				return true;
			}
			else
			{
				$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_UPLOAD_FAILED') );
			}
		}
			
		return false;
	}
		
	/**
	 * Method to initialize the import of a remote file.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function initRemoteFile($vars, $data)
	{
		// URL not provided.
		if ( empty($data['url']) || !trim($data['url']) )
		{
			$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_NO_URL_PROVIDED') );
		}
		// cURL is not installed or it's disabled.
		else if ( !function_exists('curl_init') )
		{
			$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CURL_ERROR') );
		}
		else
		{
			$curl = curl_init($data['url']); 
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($curl);
			curl_close($curl);
				
			if ($output)
			{
				// Generate random hash.
				$hash = RSDirectoryHelper::getHash();
					
				// Set new file name.
				$file_name = "$hash.csv";
					
				// Set the file path.
				$dest = $this->import_path . $file_name;
					
				if ( file_put_contents($dest, $output) )	
				{
					// Remember the file name.
					JFactory::getApplication()->setUserState('com_rsdirectory.import.csv.file_path', $dest);
						
					return true;
				}
				// Tmp file could not be written.
				else
				{
					$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_TMP_ARCHIVE_WRITE_ERROR') );
				}
			}
			else
			{
				$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_FETCH_ERROR') );
			}
		}
			
		return false;
	}
		
	/**
	 * Method to initialize the import of a local file.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function initLocalFile($vars, $data)
	{
		// URL not provided.
		if ( empty($data['local_file']) || !trim($data['local_file']) )
		{
			$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_NO_PATH_PROVIDED') );
		}
		else if ( !file_exists($data['local_file']) || !is_file($data['local_file']) )
		{
			$this->setError( JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_FILE_NOT_FOUND') );
		}
		else
		{
			// Remember the file name.
			JFactory::getApplication()->setUserState('com_rsdirectory.import.csv.file_path', $data['local_file']);
				
			return true;
		}
			
		return false;
	}
		
	/**
	 * Method to measure import data.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 */
	protected function measure($vars, $data)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
			
		$messages = $vars->response['messages'];
			
		// Get the CSV file path.
		$file_path = $app->getUserState('com_rsdirectory.import.csv.file_path');
			
		@$fh = fopen($file_path, 'r');
			
		if ($fh !== false)
		{
			$delimiter = isset($data['delimiter']) ? $data['delimiter'] : ',';
			$enclosure = isset($data['enclosure']) ? $data['enclosure'] : '"';
			$escape = isset($data['escape']) ? $data['escape'] : '\\';
				
			$total = 0;
			$first = false;
				
			while ( $line = self::fgetcsv($fh, 0, $delimiter, $enclosure, $escape) )
			{
				// Skip the 1st line.
				if ( !empty($data['ignore_first_line']) && !$first )
				{
					$first = true;
					continue;
				}
					
				$total++;
			}
				
			$messages[] = array(
				'message' => JText::_('COM_RSDIRECTORY_IMPORT_MEASURING_DATA_DONE'),
				'type' => 'success',
				'id' => 'import-msg-data-measuring',
			);
				
			if ($total)
			{
				$totals = array(
					'total' => $total,
				);
					
				$app->setUserState('com_rsdirectory.measure.import.totals', $totals);
					
				$vars->response['action'] = 'selectColumns';
			}
			else
			{
				$messages[] = array(
					'message' => JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_EMPTY'),
					'type' => 'warning',
					'id' => null,
				);
			}
		}
		else
		{
			$messages[] = array(
				'message' => JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COULD_NOT_OPEN_FILE'),
				'type' => 'error',
				'id' => null,
			);
		}
			
		$vars->response['messages'] = $messages;
	}
		
	/**
	 * Display columns assignment table.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 */
	protected function selectColumns($vars, $data)
	{
		?>
			
		<div id="csvSelectColumns" class="form-horizontal">
				
			<h3><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_SELECT_COLUMNS_TITLE'); ?></h3>
				
			<div class="alert alert-info"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_PREVIEW_NOTE'); ?></div>
				
			<div class="alert alert-error hide"></div>
				
			<?php
				
			// Get mainframe.
			$app = JFactory::getApplication();
				
			// Get the CSV file path.
			$file_path = $app->getUserState('com_rsdirectory.import.csv.file_path');
				
			@$fh = fopen($file_path, 'r');
				
			if ($fh !== false)
			{
				$delimiter = isset($data['delimiter']) ? $data['delimiter'] : ',';
				$enclosure = isset($data['enclosure']) ? $data['enclosure'] : '"';
				$escape = isset($data['escape']) ? $data['escape'] : '\\';
					
				$rows = array();
				$first = false;
				$count = 0;
					
				while ( $line = self::fgetcsv($fh, 0, $delimiter, $enclosure, $escape) )
				{
					$count++;
						
					// Skip the 1st line.
					/*if ( !empty($data['ignore_first_line']) && !$first )
					{
						$first = true;
						continue;
					}*/
						
					foreach ($line as $j => $value)
					{
						$rows[$j][$count] = $value;
					}
						
					// Get just the 1st 5 lines.
					if ($count > 4)
						break;
				}
					
				if ($rows)
				{
					// Get DBO.
					$db = JFactory::getDbo();
						
					// Get the columns of the entries table.
					$columns = $db->getTableColumns('#__rsdirectory_entries');
						
					// Initialize the columns options.
					$options = array(
						array(
							'items' => array(
								JHTML::_( 'select.option', '', JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMN_OPTION') )
							),
						),
					);
						
					// Add the core columns group.
					$group = array(
						'text' => JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CORE_COLUMNS_OPTION'),
						'items' => array(),
					);
						
					foreach ($columns as $column => $type)
					{
						if ( !in_array($column, self::$skippedColumns) )
						{
							$group['items'][] = JHtml::_( 'select.option', $column, JText::_( 'PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMNS_' . strtoupper($column) ) );	
						}
					}
						
					$options[] = $group;
						
						
					// Get the category id.	
					$category_id = empty($data['category_id']) ? 0 : $data['category_id'];
						
					// Get the form id.
					$form_id = RSDirectoryHelper::getCategoryInheritedFormId($category_id);
						
					if ( $form_fields = RSDirectoryHelper::getFormFields($form_id, null, 1) )
					{
						// Add the custom fields columns group.
						$group = array(
							'text' => JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CUSTOM_FIELDS_COLUMNS_OPTION'),
							'items' => array(),
						);
							
						// Get the columns of the custom fields table.
						$columns = $db->getTableColumns('#__rsdirectory_entries_custom');
							
						foreach ($columns as $column => $type)
						{
							preg_match('/f_(\d+)/', $column, $matches);
								
							// Skip the column if the field id was not found.
							if ( !isset($matches[1]) )
								continue;
								
							// Get the form field.
							$field = RSDirectoryHelper::findElements( array('id' => $matches[1]), $form_fields );
								
							// Skip the column if the field was not found.
							if (!$field)
								continue;
								
							if ( trim( $field->properties->get('form_caption') ) )
							{
								$text = $field->properties->get('form_caption');
							}
							else
							{
								$arr = explode('-', $field->name);
								$arr = array_map('strtolower', $arr);
								$arr = array_map('ucfirst', $arr);
									
								$text = implode(' ', $arr);
							}
								
							// E.g.: f_10_address -> Address
							$col_substr = str_replace( array($matches[0], '_'), array('', ' '), $column);
							$col_substr = ucfirst( strtolower($col_substr) );
								
							$text .= $col_substr;
								
							$group['items'][] = JHtml::_('select.option', $column, $text);
						}
							
						$options[] = $group;
					}
						
					?>
						
					<table class="table table-striped">
						<thead>
							<tr>
								<th><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_RSDIRECTORY_COLUMNS'); ?></th>
								<?php for ( $i = 0; $i < $count; $i++ ) { ?>
								<th>
									<?php
									if ( !empty($data['ignore_first_line']) && !$i )
									{
										echo JText::sprintf('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_CSV_COLUMNS');
									}
									else
									{
										echo JText::sprintf( 'PLG_SYSTEM_RSDIRECTORYIMPORTCSV_ENTRY_NUMBER', $i + ( empty($data['ignore_first_line']) ? 1 : 0 ) );
									}
									?>
								</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($rows as $i=> $cols) { ?>
							<tr>
								<td>
									<div class="control-group">
									<?php
									echo JHtml::_(
										'select.groupedlist',
										$options,
										'jform[csv][columns][]',
										array(
											'id' => "csv_columns_$i",
										)
									); ?>
									</div>
								</td>
								<?php foreach ($cols as $col) { ?>
								<td><?php echo RSDirectoryHelper::cut( RSDirectoryHelper::escapeHTML($col), 500 ); ?></td>
								<?php } ?>
							</tr>
							<?php } ?>
						</tbody>
					</table>
						
					<button id="csv-select-columns" class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
						
					<?php
				}
			}
			else
			{
				?>
					
				<div class="alert alert-error"><?php echo JText::_('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COULD_NOT_OPEN_FILE'); ?></div>
					
				<?php
			}
				
			?>
				
		</div>
			
		<?php
	}
		
	/**
	 * Method to import data.
	 *
	 * @access protected
	 *
	 * @param object $vars
	 * @param array $data
	 */
	protected function import($vars, $data)
	{
		jimport('joomla.filesystem.file');
			
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
			
		$messages = $vars->response['messages'];
			
		$offset = empty($data['offset']) ? 0 : $data['offset'];
			
		// Set the import limit.
		$limit = 50;
			
		// Get the CSV file path.
		$file_path = $app->getUserState('com_rsdirectory.import.csv.file_path');
			
		@$fh = fopen($file_path, 'r');
			
		$list = array();
			
		if ($fh !== false)
		{
			$delimiter = isset($data['delimiter']) ? $data['delimiter'] : ',';
			$enclosure = isset($data['enclosure']) ? $data['enclosure'] : '"';
			$escape = isset($data['escape']) ? $data['escape'] : '\\';
				
			$rows = array();
			$first = false;
			$i = 0;
				
			while ( $line = self::fgetcsv($fh, 0, $delimiter, $enclosure, $escape) )
			{
				// Skip the 1st line.
				if ( !empty($data['ignore_first_line']) && !$first )
				{
					$first = true;
					continue;
				}
					
				$i++;
					
				$list[] = $line;
				
				// A little optimisation.. Exit the loop if we exceeded the step limit.
				if ($limit + $offset < $i)
					break;
			}
				
			// Cut a piece of the array.	
			$list = array_slice($list, $offset, $limit);
		}
			
		$columns = explode(',', $data['columns']);
			
		if ($list)
		{
			// Get the category id.	
			$category_id = empty($data['category_id']) ? 0 : $data['category_id'];
				
			// Get the form id.
			$form_id = RSDirectoryHelper::getCategoryInheritedFormId($category_id);
			
			// Remember the entries ids.
			$entries_ids = array();
				
			foreach ($list as $line)
			{
				$entry = (object)array(
					'category_id' => $category_id,
					'form_id' => $form_id,
				);
				$entry_custom = new stdClass;
				$update = false;
					
				foreach ($line as $i => $value)
				{
					if ( empty( $columns[$i] ) )
						continue;
						
					$column = $columns[$i];
						
					// Custom field column.
					if ( RSDirectoryHelper::startsWith($column, 'f_') )
					{
						$entry_custom->$column = $value;
					}
					// Core column.
					else
					{
						$entry->$column = $value;
					}
				}
					
				if ( empty($entry->id) )
				{
					// Unset the entry id to avoid ids with the value 0.
					unset($entry->id);
				}
				else
				{
					$query = $db->getQuery(true)
						   ->select('COUNT(*)')
						   ->from( $db->qn('#__rsdirectory_entries') )
						   ->where( $db->qn('id') . ' = ' . $db->q($entry->id) );
						   
					$db->setQuery($query);
						
					if ( $db->loadResult() )
					{
						$update = true;
					}
				}
					
				if ($update)
				{
					$db->updateObject('#__rsdirectory_entries', $entry, 'id');
						
					$entry_custom->entry_id = $entry->id;
						
					$db->updateObject('#__rsdirectory_entries_custom', $entry_custom, 'entry_id');
				}
				else
				{
					$db->insertObject('#__rsdirectory_entries', $entry, 'id');
						
					$entry_custom->entry_id = $entry->id;
						
					$db->insertObject('#__rsdirectory_entries_custom', $entry_custom, 'entry_id');
				}
					
				$entries_ids[] = $entry->id;
			}
			
			if ( !empty($data['regenerate_titles']) && $entries_ids )
			{
				// Get entries.
				$entries = RSDirectoryHelper::getEntriesObjectListByIds($entries_ids);
				
				// Regenerate titles.
				RSDirectoryHelper::regenerateEntriesTitles($entries);
			}
		}
			
		$count = count($list);
		$totals = $app->getUserState('com_rsdirectory.measure.import.totals');
			
		$vars->response['total'] = $totals['total'];
			
		$progress = $offset + $count;
		$progress = $progress > $totals['total'] ? $totals['total'] : $progress;
			
		$vars->response['progress'] = $progress;
		$vars->response['completition'] = $totals['total'] ? number_format( ($progress * 100) / $totals['total'], 1 ) : 100.0;
			
		$vars->response['action'] = $vars->response['completition'] < 100 ? 'import' : 'done';
		$vars->response['messages'] = $messages;
	}
		
	/**
	 * Convert the section name into a valid id attribute string.
	 *
	 * @access public
	 *
	 * @param string $section
	 *
	 * @return string
	 */
	public static function sectionNameToId($section)
	{
		return "import-msg-$section";
	}
		
	/**
	 * Method to get categories options.
	 *
	 * @access public
	 * 
	 * @static
	 *
	 * @return array
	 */
	public static function getCategoriesOptions()
	{
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			   ->select( $db->qn( array('id', 'title', 'level') ) )
			   ->from( $db->qn('#__categories') )
			   ->where( $db->qn('parent_id') . ' > ' . $db->q(0) )
			   ->where( $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') )
			   ->where( $db->qn('published') . ' IN (' . $db->q(0) . ', ' . $db->q(1) . ')' )
			   ->order( $db->qn('lft') );
				
		$db->setQuery($query);
		$items = $db->loadObjectList();
			
		$options = array();
			
		if ($items)
		{
			foreach ($items as $i => $item)
			{
				$repeat = $item->level - 1 >= 0 ? $item->level - 1 : 0;
				$item->title = str_repeat('- ', $repeat) . $item->title;
				$disable = isset($items[$i + 1]) && $items[$i + 1]->level > $item->level;
				$options[] = JHtml::_('select.option', $item->id, $item->title, 'value', 'text', $disable);
			}
		}
			
		return $options;
	}
		
	/**
	 * Method to add an error to the errors array.
	 *
	 * @access public
	 *
	 * @param string $error
	 */
	public function setError($error)
	{
		$this->errors[] = $error;
	}
		
	/**
	 * Method to get the errors array.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
		
	/**
	 * Gets line from file pointer and parse for CSV fields.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param resource  $handle
	 * @param int $length
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escape
	 *
	 * @return mixed
	 *
	 * @see fgetcsv
	 */
	public static function fgetcsv($handle, $length = 0, $delimiter = ",", $enclosure = '"', $escape = "\\")
	{
		if ( version_compare( phpversion(), '5.3.0' ) == -1 )
		{
			return fgetcsv($handle, $length, $delimiter, $enclosure);
		}
		else
		{
			return fgetcsv($handle, $length, $delimiter, $enclosure, $escape);
		}
	}
}
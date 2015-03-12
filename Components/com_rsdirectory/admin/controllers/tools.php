<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Tools controller.
 */
class RSDirectoryControllerTools extends JControllerForm
{
	/**
     * Regenerate titles ajax task.
     *
     * @access public
     */
    public function regenerateTitlesAjax()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
		$jinput = $app->input;
            
        $forms_ids =$jinput->get( 'forms_ids', array(), 'array' );
        $elements = $jinput->get( 'elements', array(), 'array' );
        $offset = $jinput->getInt('offset');
            
        // Get the Tools model.
        $model = $this->getModel('Tools');
            
        $model->regenerateTitles($forms_ids, $elements, $offset);
            
        $reponse = $model->getTitlesRegenerationProgress();
            
        echo json_encode($reponse);
            
        $app->close();
    }
		
	/**
     * Backup ajax task.
     *
     * @access public
     */
	public function backupAjax()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
		$jinput = $app->input;
			
        $offset = $jinput->getInt('offset');
            
        // Get the Tools model.
        $model = $this->getModel('Tools');
            
        $model->backup($offset);
            
        $reponse = $model->getBackupProgress();
            
        echo json_encode($reponse);
            
        $app->close();
	}
		
	/**
	 * Delete the selected cached backup files.
	 *
	 * @access public
	 */
	public function deleteBackupFiles()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
		$jinput = $app->input;
			
		$files = $jinput->get( 'files', array(), 'array' );
			
		// Get the Tools model.
        $model = $this->getModel('Tools');
            
        if ( $model->deleteBackupFiles($files) )
		{
			echo 1;	
		}
			
		$app->close();
	}
		
	/**
	 * Initialize the restore process for an uploaded archive.
	 *
	 * @access public
	 */
	public function restoreInitUploadedArchive()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		// Get mainframe.
		$app = JFactory::getApplication();
			
		$files = $app->input->files->get( 'jform', array(), 'array' );
			
		// Get the Tools model.
		$model = $this->getModel('Tools');
			
		echo '<script type="text/javascript">';
			
		if ( $model->restoreInitUploadedArchive($files) )
		{
			echo 'window.top.window.addLog( window.top.document.getElementById("restore-log"), "' . RSDirectoryHelper::escapeHTML( JText::_('COM_RSDIRECTORY_RESTORE_UPLOADED_AND_EXPANDED') ) . '", "success" );';
			echo 'window.top.window.verifyRestore();';
		}
		else
		{
			$errors = $model->getErrors();
				
			foreach ($errors as $error)
			{
				echo 'window.top.window.addLog( window.top.document.getElementById("restore-log"), "' . RSDirectoryHelper::escapeHTML($error) . '", "error" );';
			}
				
			echo 'window.top.window.abortRestore("' . RSDirectoryHelper::escapeHTML( JText::_('COM_RSDIRECTORY_RESTORE_ABORTED') ) . '");';
		}
			
		echo '</script>';
			
		$app->close();
	}
		
	/**
	 * Initialize the restore process for a local or remote CSV archive.
	 *
	 * @access public
	 */
	public function restoreInit()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
			
		$data = $app->input->get( 'jform', array(), 'array' );
			
		// Get the Tools model.
		$model = $this->getModel('Tools');
			
		// Initialize the response array.
		$response = array(
			'messages' => array(),
			'action' => 'abort',
		);
			
		if ( $model->restoreInit($data) )
		{
			if ($data['restore_from'] == 'local_archive')
			{
				$response['messages'][] = array(
					'message' => JText::_('COM_RSDIRECTORY_RESTORE_EXPANDED'),
					'type' => 'success',
				);
			}
			else
			{
				$response['messages'][] = array(
					'message' => JText::_('COM_RSDIRECTORY_RESTORE_FETCHED_AND_EXPANDED'),
					'type' => 'success',
				);
			}
				
			$response['action'] = 'verify';
		}
		else
		{
			$errors = $model->getErrors();
				
			foreach ($errors as $error)
			{
				$response['messages'][] = array(
					'message' => $error,
					'type' => 'error',
				);
			}
		}
			
		echo json_encode($response);
			
		$app->close();
	}
		
	/**
	 * Verify the restore backup data.
	 *
	 * @access public
	 */
	public function restoreVerify()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
			
		$offset = $app->input->getInt('offset');
			
		// Get the Tools model.
		$model = $this->getModel('Tools');
			
		// Initialize the response array.
		$response = array(
			'messages' => array(),
			'action' => 'abort',
		);
			
		if ( $model->restoreVerify($offset) )
		{
			$tables = $model->getBackupTables();
				
			$response['messages'][] = array(
				'message' => JText::sprintf('COM_RSDIRECTORY_RESTORE_TABLE_CHECK_OK', $tables[$offset]),
				'type' => 'success',
			);
				
			$offset++;
				
			$response['action'] = 'measure';
				
			if ( isset($tables[$offset]) )
			{
				$response['action'] = 'verify';
				$response['offset'] = $offset;
			}
		}
		else
		{
			$errors = $model->getErrors();
				
			foreach ($errors as $error)
			{
				$response['messages'][] = array(
					'message' => $error,
					'type' => 'error',
				);
			}
		}
			
		echo json_encode($response);
			
		$app->close();
	}
		
	/**
	 * Measure the restore backup data.
	 *
	 * @access public
	 */
	public function restoreMeasure()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		// Get the Tools model.
		$model = $this->getModel('Tools');
			
		// Initialize the response array.
		$response = array(
			'messages' => array(),
			'action' => 'abort',
		);
			
		if ( $model->restoreMeasure() )
		{
			$response['messages'][] = array(
				'message' => JText::_('COM_RSDIRECTORY_RESTORE_MEASURING_DATA_DONE'),
				'type' => 'success',
				'id' => 'restore-msg-data-measuring',
			);
				
			$response['messages'][] = array(
				'message' => JText::_('COM_RSDIRECTORY_RESTORING_DATA'),
				'type' => 'info',
				'id' => null,
			);
				
			$response['action'] = 'restore';
				
			$tables = $model->getBackupTables();
				
			$response['messages'][] = array(
				'message' => JText::sprintf('COM_RSDIRECTORY_RESTORE_TABLE_PROGRESS', $tables[0], 0.0),
				'type' => 'success',
				'id' => self::tableNameToId($tables[0]),
			);
				
			$response['table'] = $tables[0];
		}
		else
		{
			$errors = $model->getErrors();
				
			foreach ($errors as $error)
			{
				$response['messages'][] = array(
					'message' => $error,
					'type' => 'error',
					'id' => null,
				);
			}
		}
			
		echo json_encode($response);
			
		JFactory::getApplication()->close();
	}
		
	/**
	 * Restore backup.
	 *
	 * @access public
	 */
	public function restore()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
			
		list($table) = $app->input->get( 'table', array(), 'array' );
		$table_offset = $app->input->getInt('table_offset');
		$offset = $app->input->getInt('offset');
			
		// Get the Tools model.
		$model = $this->getModel('Tools');
			
		// Initialize the response array.
		$response = array(
			'messages' => array(),
			'action' => 'abort',
		);
			
		if ( $model->restore($table, $table_offset, $offset) )
		{
			$progress = $model->getRestoreProgress();
				
			$response = array_merge($response, $progress);
				
			$response['messages'][] = array(
				'message' => JText::sprintf('COM_RSDIRECTORY_RESTORE_TABLE_PROGRESS', $table, $progress['table_completition']),
				'type' => 'success',
				'id' => self::tableNameToId($table),
			);
				
			$tables = $model->getBackupTables();		
			$key = array_search($table, $tables);
				
			if ($progress['table_completition'] == 100 && isset($tables[$key + 1]))
			{
				// Get the next table.
				$table = $tables[$key + 1];
					
				// Reset table completition.
				$response['table_completition'] = 0.0;
					
				// Reset table progress.
				$response['table_progress'] = 0.0;
					
				$response['messages'][] = array(
					'message' => JText::sprintf('COM_RSDIRECTORY_RESTORE_TABLE_PROGRESS', $table, 0.0),
					'type' => 'success',
					'id' => self::tableNameToId($table),
				);
			}
				
			$response['action'] = 'restore';
			$response['table'] = $table;
				
			if ( $progress['completition'] == 100 && !isset($tables[$key + 1]) )
			{
				$response['action'] = 'done';
					
				// Clear the restore hash.
				$app->setUserState('com_rsdirectory.restore.hash', null);	
			}
		}
		else
		{
			$errors = $model->getErrors();
				
			foreach ($errors as $error)
			{
				$response['messages'][] = array(
					'message' => $error,
					'type' => 'error',
					'id' => null,
				);
			}
		}
			
		echo json_encode($response);
			
		$app->close();
	}
		
	/**
	 * Import.
	 *
	 * @access public
	 */
	public function import()
	{
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get jform data.
		$data = $app->input->get( 'jform', array(), 'array' );
			
		$check_token_method = empty($data['check_token_method']) ? 'post' : $data['check_token_method'];
			
		// Check for request forgeries.
        JSession::checkToken($check_token_method) or jexit( JText::_('JINVALID_TOKEN') );
			
		$vars = (object)array(
			'import_from' => empty($data['import_from']) ? '' : $data['import_from'],
			'import_action' => empty($data['import_action']) ? '' : $data['import_action'],
			'response' => array(
				'messages' => array(),
				'action' => 'abort',
			),
		);
			
		$app->triggerEvent( 'rsdirectory_import', array($vars) );
			
		if ( !empty($vars->response) )
		{
			echo json_encode($vars->response);
		}
			
		$app->close();
	}
		
	/**
	 * Convert the table name into a valid id attribute string.
	 *
	 * @access public
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	public static function tableNameToId($table)
	{
		return str_replace( array('#__', '_'), array('restore-msg', '-'), $table );
	}
		
	/**
     * The cancel task.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
     */
    public function cancel($key = null)
    {
        // Redirect to the dashboard.
        $this->setRedirect('index.php?option=com_rsdirectory');
    }
}
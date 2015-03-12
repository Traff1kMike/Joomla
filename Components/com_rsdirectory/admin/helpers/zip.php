<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Slighlty modified version of JArchiveZip.
 *
 * Used only for extraction.
 *
 * @see JArchiveZip
 */
class RSDirectoryZip
{
	/**
	 * ZIP compression methods.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $_methods = array(
		0x0 => 'None',
		0x1 => 'Shrunk',
		0x2 => 'Super Fast',
		0x3 => 'Fast',
		0x4 => 'Normal',
		0x5 => 'Maximum',
		0x6 => 'Imploded',
		0x8 => 'Deflated'
	);
		
	/**
	 * Beginning of central directory record.
	 *
	 * @access protected
	 * 
	 * @var string
	 */
	protected $_ctrlDirHeader = "\x50\x4b\x01\x02";
		
	/**
	 * End of central directory record.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_ctrlDirEnd = "\x50\x4b\x05\x06\x00\x00\x00\x00";
		
	/**
	 * Beginning of file contents.
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $_fileHeader = "\x50\x4b\x03\x04";
		
	/**
	 * ZIP file data buffer
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $_data = null;
		
	/**
	 * ZIP file metadata array
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $_metadata = null;
		
	/**
	 * Extract a ZIP compressed file to a given path.
	 *
	 * @access public
	 *
	 * @param string $archive Path to ZIP archive to extract
	 * @param string $destination Path to extract archive into
	 * @param array $options Extraction options
	 *
	 * @return boolean  True if successful
	 *
	 * @throws RuntimeException
	 */
	public function extract( $archive, $destination, array $options = array() )
	{
		if ( !is_file($archive) )
		{
			if ( class_exists('JError') )
			{
				return JError::raiseWarning(100, 'Archive does not exist');
			}
			else
			{
				throw new RuntimeException('Archive does not exist');
			}
		}
			
		// Process the options array.	
		if ( !is_array($options) )
		{
			$options = array();	
		}
			
		if ( empty($options['allowedFileTypes']) || !is_array($options['allowedFileTypes']) )
		{
			$options['allowedFileTypes'] = array();
		}
			
		if ( $this->hasNativeSupport() )
		{
			return $this->extractNative($archive, $destination, $options);
		}
		else
		{
			return $this->extractCustom($archive, $destination, $options);
		}
	}
		
	/**
	 * Tests whether this adapter can unpack files on this computer.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @return boolean True if supported
	 */
	public static function isSupported()
	{
		return self::hasNativeSupport() || extension_loaded('zlib');
	}
		
	/**
	 * Method to determine if the server has native zip support for faster handling.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @return boolean True if php has native ZIP support
	 */
	public static function hasNativeSupport()
	{
		return function_exists('zip_open') && function_exists('zip_read');
	}
		
	/**
	 * Checks to see if the data is a valid ZIP file.
	 *
	 * @access public
	 *
	 * @param string &$data  ZIP archive data buffer.
	 *
	 * @return boolean True if valid, false if invalid.
	 */
	public function checkZipData(&$data)
	{
		return strpos($data, $this->_fileHeader) === false ? false : true;
	}
		
	/**
	 * Extract a ZIP compressed file to a given path using a php based algorithm that only requires zlib support.
	 *
	 * @access protected
	 *
	 * @param string $archive Path to ZIP archive to extract.
	 * @param string $destination Path to extract archive into.
	 * @param array $options
	 *
	 * @return mixed True if successful
	 *
	 * @throws RuntimeException
	 */
	protected function extractCustom( $archive, $destination, $options = array() )
	{
		$this->_data = null;
		$this->_metadata = null;
			
		if ( !extension_loaded('zlib') )
		{
			if ( class_exists('JError') )
			{
				return JError::raiseWarning(100, 'Zlib not supported');
			}
			else
			{
				throw new RuntimeException('Zlib not supported');
			}
		}
			
		$this->_data = file_get_contents($archive);
			
		if (!$this->_data)
		{
			if ( class_exists('JError') )
			{
				return JError::raiseWarning(100, 'Unable to read archive (zip)');
			}
			else
			{
				throw new RuntimeException('Unable to read archive (zip)');
			}
		}
			
		if ( !$this->_readZipInfo($this->_data) )
		{
			if ( class_exists('JError') )
			{
				return JError::raiseWarning(100, 'Get ZIP Information failed');
			}
			else
			{
				throw new RuntimeException('Get ZIP Information failed');
			}
		}
			
		for ($i = 0, $n = count($this->_metadata); $i < $n; $i++)
		{
			// Get the whole file name.
			$fileName = $this->_metadata[$i]['name'];
				
			$lastPathCharacter = substr($fileName, -1, 1);
				
			if ($lastPathCharacter !== '/' && $lastPathCharacter !== '\\')
			{	
				// Get the file extension.
				$ext = JFile::getExt($fileName);
					
				if ( !empty($options['allowedFileTypes']) && !in_array($ext, $options['allowedFileTypes']) )
					continue;
					
				// Get the file name without the extension.
				$fileName = JFile::stripExt($fileName);
					
				if ( isset($options['fileNamePrefix']) )
				{
					$fileName = $options['fileNamePrefix'] . $fileName;
				}
					
				if ( isset($options['fileNameSuffix']) )
				{
					$fileName .= $options['fileNameSuffix'];
				}
					
				if ($ext)
				{
					$fileName .= ".$ext";
				}
					
				$buffer = $this->_getFileData($i);
				$path = JPath::clean("$destination/$fileName");
					
				// Make sure the destination folder exists.
				if ( !JFolder::create( dirname($path) ) )
				{
					if ( class_exists('JError') )
					{
						return JError::raiseWarning(100, 'Unable to create destination');
					}
					else
					{
						throw new RuntimeException('Unable to create destination');
					}
				}
					
				if ( JFile::write($path, $buffer) === false )
				{
					if ( class_exists('JError') )
					{
						return JError::raiseWarning(100, 'Unable to write entry');
					}
					else
					{
						throw new RuntimeException('Unable to write entry');
					}
				}
			}
		}
			
		return true;
	}
		
	/**
	 * Extract a ZIP compressed file to a given path using native php api calls for speed.
	 *
	 * @access protected
	 *
	 * @param string $archive Path to ZIP archive to extract
	 * @param string $destination Path to extract archive into
	 * @param array $options
	 *
	 * @return boolean True on success
	 *
	 * @throws RuntimeException
	 */
	protected function extractNative($archive, $destination, $options = array() )
	{
		$zip = zip_open($archive);
			
		if ( is_resource($zip) )
		{
			// Make sure the destination folder exists.
			if ( !JFolder::create($destination) )
			{
				if (class_exists('JError'))
				{
					return JError::raiseWarning(100, 'Unable to create destination');
				}
				else
				{
					throw new RuntimeException('Unable to create destination');
				}
			}
				
			// Read files in the archive.
			while ( $file = @zip_read($zip) )
			{
				if ( zip_entry_open($zip, $file, 'r') )
				{
					if ( substr( zip_entry_name($file), strlen( zip_entry_name($file) ) - 1) != "/" )
					{
						$buffer = zip_entry_read( $file, zip_entry_filesize($file) );
							
						if ( !empty($options['allowedFileTypes']) && !in_array( JFile::getExt( zip_entry_name($file) ), $options['allowedFileTypes']) )
							continue;
							
						// Get the whole file name.
						$fileName = zip_entry_name($file);
							
						// Get the file extension.
						$ext = JFile::getExt($fileName);
							
						// Get the file name without the extension.
						$fileName = JFile::stripExt($fileName);
							
						if ( isset($options['fileNamePrefix']) )
						{
							$fileName = $options['fileNamePrefix'] . $fileName;
						}
							
						if ( isset($options['fileNameSuffix']) )
						{
							$fileName .= $options['fileNameSuffix'];
						}
							
						if ($ext)
						{
							$fileName .= ".$ext";
						}
							
						if ( JFile::write( "$destination/$fileName", $buffer ) === false )
						{
							if ( class_exists('JError') )
							{
								return JError::raiseWarning(100, 'Unable to write entry');
							}
							else
							{
								throw new RuntimeException('Unable to write entry');
							}
						}
							
						zip_entry_close($file);
					}
				}
				else
				{
					if ( class_exists('JError') )
					{
						return JError::raiseWarning(100, 'Unable to read entry');
					}
					else
					{
						throw new RuntimeException('Unable to read entry');
					}
				}
			}
				
			@zip_close($zip);
		}
		else
		{
			if ( class_exists('JError') )
			{
				return JError::raiseWarning(100, 'Unable to open archive');
			}
			else
			{
				throw new RuntimeException('Unable to open archive');
			}
		}
			
		return true;
	}
		
	/**
	 * Get the list of files/data from a ZIP archive buffer.
	 *
	 * <pre>
	 * KEY: Position in zipfile
	 * VALUES: 'attr'  --  File attributes
	 * 'crc'   --  CRC checksum
	 * 'csize' --  Compressed file size
	 * 'date'  --  File modification time
	 * 'name'  --  Filename
	 * 'method'--  Compression method
	 * 'size'  --  Original file size
	 * 'type'  --  File type
	 * </pre>
	 *
	 * @access protected
	 *
	 * @param string &$data The ZIP archive buffer.
	 *
	 * @return boolean True on success
	 *
	 * @throws  RuntimeException
	 */
	protected function _readZipInfo(&$data)
	{
		$entries = array();
			
		// Find the last central directory header entry
		$fhLast = strpos($data, $this->_ctrlDirEnd);
			
		do
		{
			$last = $fhLast;
		}
		while ( ( $fhLast = strpos($data, $this->_ctrlDirEnd, $fhLast + 1) ) !== false );
			
		// Find the central directory offset
		$offset = 0;
			
		if ($last)
		{
			$endOfCentralDirectory = unpack(
				'vNumberOfDisk/vNoOfDiskWithStartOfCentralDirectory/vNoOfCentralDirectoryEntriesOnDisk/' .
				'vTotalCentralDirectoryEntries/VSizeOfCentralDirectory/VCentralDirectoryOffset/vCommentLength',
				substr($data, $last + 4)
			);
			$offset = $endOfCentralDirectory['CentralDirectoryOffset'];
		}
			
		// Get details from central directory structure.
		$fhStart = strpos($data, $this->_ctrlDirHeader, $offset);
		$dataLength = strlen($data);
			
		do
		{
			if ($dataLength < $fhStart + 31)
			{
				if ( class_exists('JError') )
				{
					return JError::raiseWarning(100, 'Invalid Zip Data');
				}
				else
				{
					throw new RuntimeException('Invalid Zip Data');
				}
			}
				
			$info = unpack( 'vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength', substr($data, $fhStart + 10, 20) );
			$name = substr($data, $fhStart + 46, $info['Length']);
				
			$entries[$name] = array(
				'attr' => null,
				'crc' => sprintf( "%08s", dechex($info['CRC32']) ),
				'csize' => $info['Compressed'],
				'date' => null,
				'_dataStart' => null,
				'name' => $name,
				'method' => $this->_methods[$info['Method']],
				'_method' => $info['Method'],
				'size' => $info['Uncompressed'],
				'type' => null,
			);
				
			$entries[$name]['date'] = mktime(
				( ($info['Time'] >> 11) & 0x1f ),
				( ($info['Time'] >> 5) & 0x3f ),
				( ($info['Time'] << 1) & 0x3e ),
				( ($info['Time'] >> 21) & 0x07 ),
				( ($info['Time'] >> 16) & 0x1f ),
				( ( ($info['Time'] >> 25) & 0x7f ) + 1980 )
			);
				
			if ($dataLength < $fhStart + 43)
			{
				if ( class_exists('JError') )
				{
					return JError::raiseWarning(100, 'Invalid ZIP data');
				}
				else
				{
					throw new RuntimeException('Invalid ZIP data');
				}
			}
				
			$info = unpack( 'vInternal/VExternal/VOffset', substr($data, $fhStart + 36, 10) );
				
			$entries[$name]['type'] = $info['Internal'] & 0x01 ? 'text' : 'binary';
			$entries[$name]['attr'] = ($info['External'] & 0x10 ? 'D' : '-') . ($info['External'] & 0x20 ? 'A' : '-') .
			                          ($info['External'] & 0x03 ? 'S' : '-') . ($info['External'] & 0x02 ? 'H' : '-') . ($info['External'] & 0x01 ? 'R' : '-');
			$entries[$name]['offset'] = $info['Offset'];
				
			// Get details from local file header since we have the offset
			$lfhStart = strpos($data, $this->_fileHeader, $entries[$name]['offset']);
				
			if ($dataLength < $lfhStart + 34)
			{
				if ( class_exists('JError') )
				{
					return JError::raiseWarning(100, 'Invalid Zip Data');
				}
				else
				{
					throw new RuntimeException('Invalid Zip Data');
				}
			}
				
			$info = unpack( 'vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength/vExtraLength', substr($data, $lfhStart + 8, 25) );
			$name = substr($data, $lfhStart + 30, $info['Length']);
			$entries[$name]['_dataStart'] = $lfhStart + 30 + $info['Length'] + $info['ExtraLength'];
				
			// Bump the max execution time because not using the built in php zip libs makes this process slow.
			@set_time_limit( ini_get('max_execution_time') );
		}
		while ( ( $fhStart = strpos($data, $this->_ctrlDirHeader, $fhStart + 46) ) !== false );
			
		$this->_metadata = array_values($entries);
			
		return true;
	}
		
	/**
	 * Returns the file data for a file by offsest in the ZIP archive.
	 *
	 * @access protected
	 *
	 * @param integer $key The position of the file in the archive.
	 *
	 * @return string Uncompressed file data buffer.
	 */
	protected function _getFileData($key)
	{
		if ($this->_metadata[$key]['_method'] == 0x8)
		{
			return gzinflate( substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']) );
		}
		elseif ($this->_metadata[$key]['_method'] == 0x0)
		{
			/* Files that aren't compressed. */
			return substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']);
		}
		elseif ($this->_metadata[$key]['_method'] == 0x12)
		{
			// If bz2 extension is loaded use it
			if ( extension_loaded('bz2') )
			{
				return bzdecompress( substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']) );
			}
		}
			
		return '';
	}
}

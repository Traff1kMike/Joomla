<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

class com_rsdirectoryInstallerScript
{   
    /**
     * An array of modules to be installed with the component.
     *
     * @var array
     * 
     * @access private
     * 
     * @static
     */
    private static $defaultModules = array(
        'mod_rsdirectory_credits' => 'RSDirectory! Front-End Credits Module ...',
		'mod_rsdirectory_entries_carousel' => 'RSDirectory! Front-End Entries Carousel Module ...',
        'mod_rsdirectory_filtering' => 'RSDirectory! Front-End Filtering Module ...',
        'mod_rsdirectory_newest_entries' => 'RSDirectory! Front-End Newest Entries Module ...',
        'mod_rsdirectory_related_entries' => 'RSDirectory! Front-End Related Entries Module ...',
        'mod_rsdirectory_popular_entries' => 'RSDirectory! Front-End Popular Entries Module ...',
        'mod_rsdirectory_recently_visited_entries' => 'RSDirectory! Front-End Recently Visited Entries Module ...',
        'mod_rsdirectory_top_rated_entries' => 'RSDirectory! Front-End Top Rated Entries Module ...',
        'mod_rsdirectory_simple_search' => 'RSDirectory! Front-End Simple Search Module ...',
    );
        
    /**
     * An array of plugins to be installed with the component.
     *
     * @var array
     *
     * @access private
     *
     * @static
     */
    private static $defaultPlugins = array(
        'plg_rsdirectory' => array(
            'name' => 'System Plugin ...',
            'min_version' => '1.1.3',
        ),
        'plg_rsdirectorywiretransfer' => array(
            'name' => 'RSDirectory! Wire Transfer Payment Plugin ...',
            'min_version' => '1.1.3',
        ),
        'plg_rsdirectoryimportcsv' => array(
            'name' => 'RSDirectory! CSV Import Plugin ...',
            'min_version' => '1.0.0',
        ),
    );
        
    /**
     * An array of other RSDirectory! plugins.
     *
     * @var array
     * 
     * @access private
     * 
     * @static
     */
    private static $otherPlugins = array(
        'rsdirectory2co' => array(
            'name' => 'RSDirectory! 2Checkout Payment Plugin ...',
            'min_version' => '1.1.3',
        ),
        'rsdirectoryauthorize' => array(
            'name' => 'RSDirectory! Authorize Payment Plugin ...',
            'min_version' => '1.1.3',
        ),
        'rsdirectorypaypal' => array(
            'name' => 'RSDirectory! PayPal Payment Plugin ...',
            'min_version' => '1.1.3',
        ),
        'rsdirectoryimportsobipro' => array(
            'name' => 'RSDirectory! SobiPro Import Plugin ...',
            'min_version' => '1.0.0',
        ),
    );
        
    /**
     * The install hook.
     *
     * @access public
     * 
     * @param object $parent
     */
    public function install($parent)
    {
    }
        
    /**
     * The uninstall hook.
     * 
     * @access public
     * 
     * @param object $parent
     */
    public function uninstall($parent)
    {   
        // Get the database object.
        $db = JFactory::getDbo();
            
        // Get all the RSDirectory! plugins.
        $plugins = array_merge(self::$defaultPlugins, self::$otherPlugins);
            
        // Uninstall plugins.
        if ($plugins)
        {
            foreach ($plugins as $plugin => $data)
            {
                $query = $db->getQuery(true)
                       ->select( $db->qn('extension_id') )
                       ->from( $db->qn('#__extensions') )
                       ->where( $db->qn('element') . '=' . $db->q( str_replace('plg_', '', $plugin) ) )
                       ->where( $db->qn('folder') . '=' . $db->q('system') )
                       ->where( $db->qn('type') . '=' . $db->q('plugin') );
                        
                $db->setQuery($query);
                    
                if ( $extension_id = $db->loadResult() )
                {
                    // Initialize JInstaller.
                    $plg_installer = new JInstaller();
                        
                    $plg_installer->uninstall('plugin', $extension_id);
                }
            }
        }
            
        // Uninstall modules.
        if (self::$defaultModules)
        {
            foreach (self::$defaultModules as $module => $name)
            {
                $query = $db->getQuery(true)
                       ->select( $db->qn('extension_id') )
                       ->from( $db->qn('#__extensions') )
                       ->where( $db->qn('element') . '=' . $db->q($module) )
                       ->where( $db->qn('client_id') . '=' . $db->q('0') )
                       ->where( $db->qn('type') . '=' . $db->q('module') );
                        
                $db->setQuery($query);
                    
                if ( $extension_id = $db->loadResult() )
                {
                    $plg_installer->uninstall('module', $extension_id);
                }
            }
        }
    }
        
    /**
     * The preflight hook.
     *
     * @access public
     * 
     * @param string $type
     * @param object $parent
     */
    public function preflight($type, $parent)
    {
	}
		
    /**
     * The postflight hook.
     *
     * @access public
     * 
     * @param string $type
     * @param object $parent
     */
    public function postflight($type, $parent)
    {
        if ($type == 'uninstall')
            return true;
            
        $messages = array();
            
        // Process install.
        if ($type == 'install')
        {
            $this->processInstall($type, $parent, $messages);
        }
           
        // Process update. 
        if ($type == 'update')
        {
            $this->processUpdate($type, $parent, $messages);
        }
            
        // Process plugins.
        $this->processPlugins($type, $parent, $messages);
            
        // Output install screen.
        $this->outputInstallScreen($messages);
    }
        
    /**
     * Process install.
     *
     * @access public
     * 
     * @param string $type
     * @param object $parent
     * @param array &$messages
     */
    public function processInstall($type, $parent, &$messages)
    {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
            
        // Create the sample category.
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables/');
        $category = JTable::getInstance('Category', 'CategoriesTable');
            
        $category->id = null;
        $category->title = 'Vehicle';
        $category->alias = 'vehicle';
        $category->extension = 'com_rsdirectory';
        $category->setLocation(1, 'last-child');
        $category->created_user_id = $user->id;
        $category->language = '*';
        $category->published = 1;
            
        $params = new JRegistry( array('form_id' => 1) );
            
        $category->params = $params->toString();
            
        $category->store();
        $category->rebuildPath($category->id);
        $category->rebuild($category->id, $category->lft, $category->level, $category->path);
            
            
        // Update entries.
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_entries') )
               ->set( $db->qn('category_id') . ' = ' . $db->q($category->id) )
               ->set( $db->qn('user_id') . ' = ' . $db->q($user->id) )
               ->set( $db->qn('created_time') . ' = ' . $db->q( JFactory::getDate()->toSql() ) )
               ->set( $db->qn('published_time') . ' = ' . $db->q( JFactory::getDate()->toSql() ) );
               
        $db->setQuery($query);
        $db->execute();
            
        // Update uploaded files.
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_uploaded_files') )
               ->set( $db->qn('user_id') . ' = ' . $db->q($user->id) );
               
        $db->setQuery($query);
        $db->execute();
    }
        
    /**
     * Process update.
     *
     * @access public
     * 
     * @param string $type
     * @param object $parent
     * @param array &$messages
     */
    public function processUpdate($type, $parent, &$messages)
    {
        $source = $parent->getParent()->getPath('source');
            
        $db = JFactory::getDbo();
            
        // Rename the users table.
        $db->setQuery( 'SHOW TABLES LIKE ' . $db->q( $db->getPrefix() . 'rsdirectory_users_credits' ) );
            
        if ( $db->loadResult() )
        {
            $db->setQuery( 'RENAME TABLE ' . $db->qn('#__rsdirectory_users_credits') . ' TO ' . $db->qn('#__rsdirectory_users') );
            $db->execute();
        }
            
        // Rename the users > unlimited column.
        $columns = $db->getTableColumns('#__rsdirectory_users');
            
        if ( isset($columns['unlimited']) )
        {
            $db->setQuery( 'ALTER TABLE ' . $db->qn('#__rsdirectory_users') . ' CHANGE ' . $db->qn('unlimited') . ' ' . $db->qn('unlimited_credits') . ' TINYINT(1) UNSIGNED NOT NULL' );
            $db->execute();
        }
            
        // Rename the entries credits > unlimited column.
        $columns = $db->getTableColumns('#__rsdirectory_entries_credits');
            
        if ( isset($columns['unlimited']) )
        {
            $db->setQuery( 'ALTER TABLE ' . $db->qn('#__rsdirectory_entries_credits') . ' CHANGE ' . $db->qn('unlimited') . ' ' . $db->qn('unlimited_credits') . ' TINYINT(1) UNSIGNED NOT NULL' );
            $db->execute();
        }
            
        // Process email messages columns.
        $columns = $db->getTableColumns('#__rsdirectory_email_messages');
            
        if ( isset($columns['to']) )
        {
            $db->setQuery( 'ALTER TABLE ' . $db->qn('#__rsdirectory_email_messages') . ' CHANGE ' . $db->qn('to') . ' ' . $db->qn('to_email') . ' TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL' );
            $db->execute();
        }
            
        if ($columns['subject'] != 'text')
        {
            $db->setQuery( 'ALTER TABLE ' . $db->qn('#__rsdirectory_email_messages') . ' CHANGE ' . $db->qn('subject') . ' ' . $db->qn('subject') . ' TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL' );
            $db->execute();
        }
            
        // Update tables.
        $tables = array(
            '#__rsdirectory_entries' => array(
                'paid' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ),
				'expiration_notice_time' => array(
                    'alter' => 'DATETIME NOT NULL',
                ),
            ),
                
            '#__rsdirectory_entries_credits' => array(
                'paid' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ),
            ),
                
            '#__rsdirectory_email_messages' => array(
				'entry_expiration_period' => array(
					'alter' => 'INT( 11 ) UNSIGNED NOT NULL',
				),
                'description' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'to_name' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'send_html' => array(
                    'alter' => 'TINYINT( 1 ) UNSIGNED NOT NULL',
                ),
            ),
                
            '#__rsdirectory_field_types' => array(
                'all_forms' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL',
                ),
            ),
                
            '#__rsdirectory_forms' => array(
                'listing_detail_show_contact' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ),
                'contact_from_name' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => '{contact.name}',
                ),
                'contact_from_email' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => '{contact.email}',
                ),
                'contact_to_name' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => '{name}',
                ),
                'contact_to_email' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => '{email}',
                ),
                'contact_cc' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'contact_bcc' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'contact_subject' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => 'Contact message from your entry {title}',
                ),
                'contact_send_html' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ),
                'contact_message' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                    'update' => '<p>From: {contact.name} ({contact.email})</p><p>Message body:<br />{contact.message}</p><p>---<br />This email was sent via the contact form on <a href="{url}">{title}</a></p>',
                ),
            ),
                
            '#__rsdirectory_groups' => array(
                'add_entry_captcha' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL',
                ),
                'can_vote_own_entries' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL',
                ),
                'can_contact_entries_authors' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ),
                'contact_captcha' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL',
                ),
            ),
                
            '#__rsdirectory_reviews' => array(
                'name' => array(
                    'alter' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'email' => array(
                    'alter' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
                'owner_reply' => array(
                    'alter' => 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL',
                ),
            ),
                
            '#__rsdirectory_users' => array(
                'enable_contact_form' => array(
                    'alter' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT "1"',
                ), 
            ),
                
            '#__rsdirectory_users_transactions' => array(
                'entry_id' => array(
                    'alter' => 'INT(11) UNSIGNED NOT NULL',
                ),
            ),
        );
            
        foreach ($tables as $table => $columns)
        {
            $existing_columns = $db->getTableColumns($table);
                
            foreach ($columns as $column => $type)
            {
                if ( !isset($existing_columns[$column]) )
                {
                    if ( isset($type['alter']) )
                    {
                        $db->setQuery( 'ALTER TABLE ' . $db->qn($table) . ' ADD ' . $db->qn($column) . ' ' . $type['alter'] );
                        $db->execute();
                    }
                        
                    if ( isset($type['update']) )
                    {
                        $query = $db->getQuery(true)
                               ->update( $db->qn($table) )
                               ->set( $db->qn($column) . ' = ' . $db->q($type['update']) );
                                
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }
        }
            
        $this->runSQL($source, 'import_tmp.structure.sql');
        $this->runSQL($source, 'restore_tmp.structure.sql');
            
        // Add image upload field.
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_field_types') )
               ->where( $db->qn('type') . ' = ' . $db->q('image_upload') );
               
        $db->setQuery($query, 0, 1);
            
        if ( !$db->loadResult() )
        {
            // Get the position of the file upload field.
            $query = $db->getQuery(true)
                   ->select( $db->qn('ordering') )
                   ->from( $db->qn('#__rsdirectory_field_types') )
                   ->where( $db->qn('type') . ' = ' . $db->q('fileupload') );
                   
            $db->setQuery($query);
            $odering = $db->loadResult();
                
            // Increment the positions of all field types above the file upload field.
            $query = $db->getQuery(true)
                   ->update( $db->qn('#__rsdirectory_field_types') )
                   ->set( $db->qn('ordering') . ' = ' . $db->qn('ordering') . ' + ' . $db->q(1) )
                   ->where( $db->qn('ordering') . ' > ' . $db->q($odering) );
                   
            $db->setQuery($query);
            $db->execute();
                
            // Insert the image upload field.
            $object = (object)array(
                'type' => 'image_upload',
                'xml_file' => 'image_upload',
                'core' => 0,
                'always_published' => 0,
                'create_column' => 0,
                'expect_value' => 1,
                'ordering' => $odering + 1,
            );
                
            $db->insertObject('#__rsdirectory_field_types', $object);
        }
          
            
        // Add email messages data.
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_email_messages') );
               
        $db->setQuery($query, 0, 1);
            
        if ( !$db->loadResult() )
        {
            $this->runSQL($source, 'email_messages.data.sql');
        }
            
        // Set all_forms.
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_field_types') )
               ->set( $db->qn('all_forms') . ' = ' . $db->q(1) )
               ->where( $db->qn('type') . ' IN (' . $this->quoteImplode( array('title', 'publishing_period') ) . ')' );
               
        $db->setQuery($query);
        $db->execute();
    }
        
    /**
     * Process plugins.
     *
     * @access public
     * 
     * @param string $type
     * @param object $parent
     * @param array &$messages
     */
    public function processPlugins($type, $parent, &$messages)
    {
        $source = $parent->getParent()->getPath('source');
            
        // Get the database object.
        $db = JFactory::getDbo();
           
        $messages['items'] = array(
            'installed' => array(),
            'disabled' => array(),
        );
            
        // Install default plugins.
        if (self::$defaultPlugins)
        {
            foreach (self::$defaultPlugins as $plugin => $data)
            {
                // Initialize JInstaller.
                $installer = new JInstaller();
                    
                if ( $installer->install("$source/other/$plugin") )
                {
                    $query = $db->getQuery(true)
                           ->update('#__extensions')
                           ->set( $db->qn('enabled') . '=' . $db->q(1) )
                           ->where( $db->qn('element') . '=' . $db->q( str_replace('plg_', '', $plugin) ) )
                           ->where( $db->qn('type') . '=' . $db->q('plugin') )
                           ->where( $db->qn('folder') . '=' . $db->q('system') );
                    $db->setQuery($query);
                    $db->execute();
                        
                    $messages['items']['installed'][] = (object)array(
                        'name' => $data['name'],
                        'status' => 'ok',
                        'text' => 'Installed',
                    );
                }
                else
                {
                    $messages['items']['installed'][] = (object)array(
                        'name' => $data['name'],
                        'status' => 'not-ok',
                        'text' => 'Error installing!',
                    );
                }
            }
        }
            
        // Install default modules.    
        if (self::$defaultModules)
        {
            foreach (self::$defaultModules as $module => $name)
            {
                // Initialize JInstaller.
                $installer = new JInstaller();
                    
                if ( $installer->install("$source/other/$module") )
                {
                    $messages['items']['installed'][] = (object)array(
                        'name' => $name,
                        'status' => 'ok',
                        'text' => 'Installed',
                    );
                }
                else
                {
                    $messages['items']['installed'][] = (object)array(
                        'name' => $name,
                        'status' => 'not-ok',
                        'text' => 'Error installing!',
                    );
                }
            }
        }
            
        // Check other plugins.
        if ( $installedPlugins = $this->getPlugins( array_keys(self::$otherPlugins) ) )
        {
			foreach ($installedPlugins as $plugin)
            {
                $data = self::$otherPlugins[$plugin->element];
                    
				$file = JPATH_SITE . '/plugins/' . $plugin->folder . '/' . $plugin->element . '/' . $plugin->element . '.xml';
                    
				if ( file_exists($file) )
                {
					$xml = file_get_contents($file);
                        
                    preg_match('/<version>(.*)<\/version>/', $xml, $matches);
                        
                    if ( !isset($matches[1]) || version_compare($matches[1], $data['min_version']) == -1 )
                    {
                        $this->disableExtension($plugin->extension_id);
                            
                        $messages['items']['disabled'][] = (object)array(
                            'name' => $data['name'],
                            'status' => 'not-ok',
                            'text' => 'Disabled'
                        );
                    }
				}
			}
		}
    }
        
    /**
     * Ouput install message.
     *
     * @access private
     * 
     * @param object $messages
     */
    private function outputInstallScreen($messages)
    {
        // Get the database object.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('value') )
               ->from( $db->qn('#__rsdirectory_config') )
               ->where( $db->qn('name') . ' = ' . $db->q('credit_cost') );
               
        $db->setQuery($query);
        $credit_cost = $db->loadResult();
            
        ?>
            
        <style type="text/css">
            .installer-title {
                font-size: 18px;
                line-height: 20px;
                margin: 12px 0;
                padding-bottom: 5px;
                border-bottom: 1px solid #999;
            }
                
            .install-ok {
                background: #7dc35b;
                color: #fff;
                padding: 3px;
            }
                
            .install-not-ok {
                background: #E9452F;
                color: #fff;
                padding: 3px;
            }
                
            #installer-left {
                float: left;
                width: 230px;
                padding: 5px;
            }
                
            #installer-right {
                float: left;
            }
                
            .version-history {
                margin: 0 0 1em 0;
                padding: 0;
                list-style-type: none;
            }
                
            .version-history > li {
                margin: 0 0 0.5em 0;
                padding: 0 0 0 4em;
            }
                
            .version-new,
            .version-fixed,
            .version-upgraded {
                float: left;
                font-size: 0.8em;
                margin-left: -4.9em;
                width: 4.5em;
                color: white;
                text-align: center;
                font-weight: bold;
                text-transform: uppercase;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
            }
                
            .version-new {
                background: #7dc35b;
            }
                
            .version-fixed {
                background: #e9a130;
            }
                
            .version-upgraded {
                background: #61b3de;
            }
                
            .com-rsdirectory-button {
                display: inline-block;
                background: #459300 url("../media/com_rsdirectory/images/bg-button-green.gif") top left repeat-x !important;
                border: 1px solid #459300 !important;
                padding: 2px;
                color: #fff !important;
                cursor: pointer;
                margin: 0;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
                
            .big-warning {
                background: #FAF0DB;
                border: solid 1px #EBC46F;
                padding: 5px;
            }
            
            .big-warning strong {
                color: red;
            }
            
            .big-info {
                background: #D5FFDC;
                border: solid 1px #EBC46F;
                padding: 5px;
            }
        </style>
            
        <div id="installer-left">
            <img src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/rsdirectory-box.png" alt="RSDirectory!" width="553" height="679" style="width: 230px; height: 282px;" />
        </div>
            
        <div id="installer-right">
                
            <h3 class="installer-title">Plugins & Modules</h3>
                
            <?php if ( !empty($messages['items']['installed']) ) { ?>
                <?php foreach ($messages['items']['installed'] as $item) { ?>
                <p>
                    <?php echo $item->name; ?> <strong class="install-<?php echo $item->status; ?>"><?php echo $item->text; ?></strong>
                </p> 
                <?php } ?>
            <?php } ?>
                
            <?php if ( !empty($messages['items']['disabled']) ) { ?>
                <p class="big-warning"><strong>Warning!</strong> The following plugins have been temporarily disabled to prevent any errors being shown on your website. Please <a href="http://www.rsjoomla.com/downloads.html">download the latest versions</a> from your account and update your installation before enabling them.</p>
                <?php foreach ($messages['items']['disabled'] as $item) { ?>
                <p>
                    <?php echo $item->name; ?> <strong class="install-<?php echo $item->status; ?>"><?php echo $item->text; ?></strong>
                </p> 
                <?php } ?>
            <?php } ?>
                
            <h3 class="installer-title">Changelog v1.4.3</h3>
                
            <ul class="version-history">
				<li><span class="version-upgraded">Upg</span> &quot;Credits&quot; is now shown next to &quot;Credit Packages&quot; in the &quot;Transactions&quot; screen.</li>
				<li><span class="version-upgraded">Upg</span> Added a &quot;Transaction Details&quot; column in the &quot;Transactions&quot; screen to increase visibility.</li>
				<li><span class="version-upgraded">Upg</span> Clicking on a username in the &quot;Transactions&quot; screen will now take you to the &quot;Users&quot; section of RSDirectory!.</li>
				<li><span class="version-upgraded">Upg</span> &quot;Transactions&quot; are now by default ordered by their &quot;Creation Date&quot;.</li>
				<li><span class="version-fixed">Fix</span> &quot;Map Radius Search&quot; was not working correctly when set as homepage.</li>
            </ul>
                
            <p>Click <a href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/204-changelog.html">here</a> to access the full changelog.</p>
                
            <p>
                <a class="com-rsdirectory-button" href="index.php?option=com_rsdirectory">Start using RSDirectory!</a>
                    
                <a class="com-rsdirectory-button" href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/197-rsdirectory.html" target="_blank">Read the RSDirectory! User Guide</a>
                    
                <a class="com-rsdirectory-button" href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
            </p>
                
            <?php if ( empty($credit_cost) ) { ?>
            <div class="big-warning"><strong>Warning!</strong> The credit cost is set to 0. You should change this, if you wish to use the credits functionality, by accessing the Payments tab on the Configuration page.</div>
            <?php } ?>
                
            <p>&nbsp;</p>
                
        </div>
            
        <div style="clear: both;"></div>
            
        <?php
    }
        
    /**
     * Get plugins data.
     *
     * @access private
     *
     * @param array $plugins
     *
     * @return mixed
     */
    private function getPlugins($plugins)
    {
		$db = JFactory::getDbo();
            
		$query = $db->getQuery(true)
               ->select('*')
			   ->from('#__extensions')
			   ->where( $db->qn('type') . '=' . $db->q('plugin') )
			   ->where( $db->qn('folder') . ' IN (' . $this->quoteImplode( array('search', 'system') ) . ')' )
			   ->where( $db->qn('element') . ' IN (' . $this->quoteImplode($plugins) . ')' );
                
		$db->setQuery($query);
            
		return $db->loadObjectList();
	}
        
    /**
     * Disable extension.
     *
     * @access private
     *
     * @param int $extension_id
     */
    private function disableExtension($extension_id)
    {
		$db = JFactory::getDbo();
            
		$query = $db->getQuery(true)
               ->update('#__extensions')
			   ->set( $db->qn('enabled') . '=' . $db->q(0) )
			   ->where( $db->qn('extension_id') . '=' . $db->q($extension_id) );
                
		$db->setQuery($query);
		$db->execute();
	}
        
    /**
     * Quote an array of values.
     *
     * @access private
     *
     * @param array $array
     *
     * @return string
     */
    private function quoteImplode($array)
    {
		$db = JFactory::getDbo();
            
		foreach ($array as &$value)
        {
			$value = $db->q($value);
		}
            
		return implode(',', $array);
	}
        
    /**
     * Load and execute a SQL file.
     *
     * @access private
     *
     * @param string $source
     * @param string $file
     */
    private function runSQL($source, $file)
    {
		$db = JFactory::getDbo();
		$driver = strtolower($db->name);
            
		if ($driver == 'mysqli')
        {
			$driver = 'mysql';
		}
        else if ($driver == 'sqlsrv')
        {
			$driver = 'sqlazure';
		}
            
		$sqlfile = $source . '/admin/sql/' . $driver . '/' . $file;
            
		if ( file_exists($sqlfile) )
        {
			$buffer = file_get_contents($sqlfile);
                
			if ($buffer !== false)
            {
				$queries = JInstallerHelper::splitSql($buffer);
                    
				foreach ($queries as $query)
                {
					$query = trim($query);
                        
					if ($query != '' && $query{0} != '#')
                    {
						$db->setQuery($query);
                            
						if ( !$db->execute() )
                        {
							JError::raiseWarning( 1, JText::sprintf( 'JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true) ) );
						}
					}
				}
			}
		}
	}
}
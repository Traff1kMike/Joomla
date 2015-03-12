<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry view.
 */
class RSDirectoryViewEntry extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$layout = $this->getLayout();
		$pathway = $app->getPathway();
		$user = JFactory::getUser();
		$this->user = $user;
		$config = RSDirectoryConfig::getInstance();
		$this->config = $config;
			
		// Get params.
		$params = $app->getParams();
		$this->params = $params;
			
		// View entry.
		if ($layout == 'default')
		{
			$jinput = $app->input;
			$id = $jinput->getInt('id');
			$entry = $this->get('Item');
			$this->print = $jinput->getInt('print');
				
				
			// Raise an error if no entry was found with that id.
			if (!$entry)
			{
				JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
			}
				
			// Build breadcrumb.
			if ( !$app->input->get('Itemid') )
			{
				$pathway->addItem(
					JText::_('COM_RSDIRECTORY_ENTRIES_VIEW_DEFAULT_TITLE'),
					JRoute::_('index.php?option=com_rsdirectory&view=entries')
				);
			}
				
			$pathway->addItem( $entry->title );
				
			// Get entry status.
			$this->published = $entry->published && JFactory::getDate($entry->published_time)->toUnix() <= JFactory::getDate()->toUnix();
				
			$can_view_entry = true;	
			$can_view_all_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_all_unpublished_entries');
			$can_view_own_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_own_unpublished_entries') && $entry->user_id == $user->id;
				
			if (!$this->published)
			{
				if (!$this->print)
				{
					if (!$entry->paid)
					{
						// TODO: Schimba mesajul asta de aici.
						$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_UNPAID'), 'info' );
					}
					else if ($entry->published)
					{
						$app->enqueueMessage( JText::sprintf('COM_RSDIRECTORY_ENTRY_PUBLISHING_DATE', $entry->published_time), 'info' );
					}
					else
					{
						$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_AWAITING_MODERATION'), 'info' );	
					}
				}
					
				$can_view_entry = $can_view_all_unpublished_entries || $can_view_own_unpublished_entries;
			}
				
			// Get expiry status.
			$this->expired = $entry->expiry_time != '0000-00-00 00:00:00' && JFactory::getDate($entry->expiry_time)->toUnix() < JFactory::getDate()->toUnix();
				
			if ($this->expired)
			{
				if (!$this->print)
				{
					$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_EXPIRED'), 'error' );	
				}
					
				$can_view_entry = $can_view_entry && $entry->user_id == $user->id;
			}
				
			if ($can_view_entry)
			{
				RSDirectoryHelper::addRecentlyVisited($id);
					
				require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/field.php';
				require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/formfield.php';
					
				JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
					
				// Get an instance of the Entry table.
				$entry_table = JTable::getInstance('Entry', 'RSDirectoryTable');
					
				// Update the number of hits.
				$entry_table->hit($id);
				$entry->hits += $entry_table->hits;
					
				// Sanitize the pageclass_sfx.
				$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );	
					
				$doc = JFactory::getDocument();
				$form = RSDirectoryHelper::getForm($entry->form_id);
					
				$this->entry = $entry;
				$this->form = $form;
					
				// Build the breadcrumbs.
				if ($form->listing_detail_show_breadcrumb)
				{
					$categories = RSDirectoryHelper::getCategories();
						
					// Get the category hierarchy.
					$hierarchy = RSDirectoryHelper::getCategoryHierarchy($entry->category_id, $categories);
						
					// Get the Joomla! config object.
					$jconfig = JFactory::getConfig();
						
					$breadcrumbs = array(
						(object)array(
							'text' => $jconfig->get('sitename'),
							'url' => JURI::root(true),
						),
					);
						
					foreach ($hierarchy as $category)
					{
						$breadcrumbs[] = (object)array(
							'text' => $category->title,
							'url' => RSDirectoryRoute::getCategoryEntriesURL($category->id, $category->title),
						);
					}
						
					$breadcrumbs[] = (object)array(
						'text' => $entry->title,
					);
						
					$this->breadcrumbs = $breadcrumbs;
				}
					
				// Get the files uploaded for this entry.
				$files_list = RSDirectoryHelper::getFilesObjectList(0, $entry->id);
					
				$this->files_list = $files_list;
					
				// Get the form fields assigned to this form.
				$form_fields = RSDirectoryHelper::getFormFields($entry->form_id, 1, 1);
					
				$this->form_fields = $form_fields;
					
				// Get the custom fields ids.
				$this->custom_form_fields_ids = RSDirectoryHelper::getFormCustomFieldsIds($entry);
					
				// Get the image upload fields.
				$this->image_uploads = RSDirectoryHelper::findFormField('image_upload', $form_fields, false);
					
				// Get the fileupload fields.
				$this->fileuploads = RSDirectoryHelper::findFormField('fileupload', $form_fields, false);
					
				// Get the fileupload fields.
				$this->fileuploads = RSDirectoryHelper::findFormField('fileupload', $form_fields, false);
					
				if ( $this->youtube = RSDirectoryHelper::findFormField('youtube', $form_fields, false) )
				{
					require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/youtube.php';
				}
					
				// Load the files required by the image gallery.
				if ( $images_field = RSDirectoryHelper::findFormField('images', $form_fields) )
				{
					if ($files_list)
					{
						$images = RSDirectoryHelper::findElements( array('field_id' => $images_field->id), $files_list, false );
							
						if ( !empty($images[0]) )
						{
							$og_image_content = htmlentities( RSDirectoryHelper::getImageURL($images[0]->hash) );
							$doc->addCustomTag('<meta property="og:image" content="' . $og_image_content . '" />');
						}
							
						if ($form->listing_detail_show_gallery)
						{
							$this->images = $images;
						}
					}
				}
					
				if ( $this->maps = RSDirectoryHelper::findFormField('map', $form_fields, false) )
				{
					$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
					$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.rsmap.js?v=' . RSDirectoryVersion::$version );
						
					foreach ($this->maps as $i => $map)
					{
						$address_column_name = "{$map->column_name}_address";
						$lat_column_name = "{$map->column_name}_lat";
						$lng_column_name = "{$map->column_name}_lng";
							
						$address = $entry->{$address_column_name};
						$lat = $entry->{$lat_column_name};
						$lng = $entry->{$lng_column_name};
							
						if ( (float)$lat || (float)$lng )
						{
							$script = 'jQuery(function($)
							{
								jQuery( document.getElementById("rsdir-map-canvas-' . $map->id . '") ).rsMap(
								{
									address: ' . json_encode($address) . ',
									lat: ' . $lat . ',
									lng: ' . $lng . ',
									zoom: ' . $map->properties->get('default_zoom') . ',
								});
							});';
								
							$doc->addScriptDeclaration($script);
						}
						else
						{
							unset($this->maps[$i]);
						}
					}
				}
					
				$this->enable_reviews = $config->get('enable_reviews');
				$this->can_post_reviews = RSDirectoryHelper::checkUserPermission('can_post_reviews');
					
				$this->enable_ratings = $config->get('enable_ratings');
				$this->can_cast_votes = RSDirectoryHelper::checkUserPermission('can_cast_votes');
					
				$this->can_vote_own_entry = $entry->user_id != $user->id || RSDirectoryHelper::checkUserPermission('can_vote_own_entries');
					
				// Load raty?
				$load_raty = false;
					
				// Load the files required by the ratings system.
				if ( ($this->enable_ratings && $this->can_cast_votes && $this->can_vote_own_entry) || $form->listing_detail_show_ratings )
				{
					$load_raty = true;
				}
					
				if ($this->enable_reviews || $this->enable_ratings)
				{
					$ratings_model = RSDirectoryModel::getInstance('Ratings');
					$reviews_pagination = $ratings_model->getPagination();
					$pagesTotal = isset($reviews_pagination->pagesTotal) ? $reviews_pagination->pagesTotal : $reviews_pagination->get('pages.total');
						
					$this->reviews = $ratings_model->getItems();
					$this->has_review = RSDirectoryHelper::hasReview($id);
					$this->has_posted_review = $ratings_model->hasPostedReview($id);
					$this->load_more = $pagesTotal > 1;
						
					if ($this->reviews)
					{
						$load_raty = true;
							
						$script = 'jQuery(function($)
						{
							$( document.getElementById("reviews-list") ).rsReviews(
							{
								entryId: ' . $entry->id . ',
							});
						});';
							
						$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.rsreviews.js?v=' . RSDirectoryVersion::$version );
						$doc->addScriptDeclaration($script);
					}
				}
					
				if ($load_raty)
				{		
					$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
					$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/script.js?v=' . RSDirectoryVersion::$version );	
				}
					
				// Get the print param.
				if ($this->print)
				{
					$doc->addScriptDeclaration('jQuery(function(){window.print();});');
				}
					
				$can_edit_entry = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
				$this->can_edit_entry = $can_edit_entry || ( $user->id == $entry->user_id && RSDirectoryHelper::checkUserPermission('can_edit_own_entries') );
					
				$can_delete_entry = RSDirectoryHelper::checkUserPermission('can_delete_all_entries');
				$this->can_delete_entry = $can_delete_entry || ( $user->id == $entry->user_id && RSDirectoryHelper::checkUserPermission('can_delete_own_entries') );
					
				if ($this->can_delete_entry)
				{
					JText::script('COM_RSDIRECTORY_ENTRY_DELETION_CONFIRMATION');
				}
					
				if ($form->listing_detail_show_favorites_button)
				{
					JText::script('COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES');
					JText::script('COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES');
						
					$this->is_favorite = RSDirectoryHelper::isFavorite($id);
				}
					
				// Setup contact button.
				if ( $form->listing_detail_show_contact && RSDirectoryHelper::canContactEntryAuthor($entry->user_id) )
				{
					// Trigger any plugin that might change the contact button.
					$args = array(
						(object)array(
							'entry' => $entry,
							'html' => $this->loadTemplate('contact'),
						),
					);
						
					$app->triggerEvent('onBeforeRSDirectoryEntryContactCreate', $args);
						
					if ( !empty($args[0]->html) )
					{
						$this->contact = $args[0]->html;
					}
				}
					
				$doc->addCustomTag('<meta property="og:title" content="' . $this->escape($entry->title) . '" />');
					
				$og_description_content = str_replace( array("\r\n", "\r", "\n"), ' ', substr( strip_tags($entry->description), 0, 200 ) );
					
				if ($og_description_content)
				{
					$doc->addCustomTag('<meta property="og:description" content="' . $this->escape($og_description_content) . '" />');	
				}
					
				JText::script('COM_RSDIRECTORY_SCORE_REQUIRED');
				JText::script('COM_RSDIRECTORY_NAME_REQUIRED');
				JText::script('COM_RSDIRECTORY_EMAIL_REQUIRED');
				JText::script('COM_RSDIRECTORY_SUBJECT_REQUIRED');
				JText::script('COM_RSDIRECTORY_REVIEW_REQUIRED');
				JText::script('COM_RSDIRECTORY_ADD_OWNER_REPLY');
				JText::script('COM_RSDIRECTORY_EDIT_OWNER_REPLY');
					
				// Prepare document.
				$this->_prepareDocument($entry);
			}
		}
		// Edit entry.
		else if ($layout == 'edit')
		{
			// Get the JInput object.
			$jinput = $app->input;
				
			// Get stored data.
			$data = $app->getUserState('com_rsdirectory.edit.entry.data');
				
			// Get the category id.
			$category_id = empty($data['category_id']) ? $jinput->getInt('category_id') : $data['category_id'];
				
			// Get the menu item data.
			$menu = $app->getMenu();
			$item = $menu->getActive();
				
			if ( !empty($item->params) )
			{
				$this->params = $item->params;
				$this->pageclass_sfx = htmlspecialchars( $item->params->get('pageclass_sfx') );
			}
				
			// Initialize the display add entry form value.
			$display_form = false;
				
			// Get the entry id.
			$entry_id = $jinput->getInt('id');
				
			$this->entry_credits = array();
				
			$this->can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
				
			if ($entry_id)
			{
				// Get the entry.
				$entry = $this->get('Item');
					
				// Raise an error if no entry was found with that id.
				if (!$entry)
				{
					JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
				}
					
				if (!$category_id)
				{
					$category_id = $entry->category_id;    
				}
					
				// Build breadcrumb.
				if ( !$app->input->get('Itemid') )
				{
					$pathway->addItem(
						JText::_('COM_RSDIRECTORY_YOUR_ENTRIES'),
						JRoute::_('index.php?option=com_rsdirectory&view=myentries')
					);
				}
					
				$pathway->addItem( JText::_('COM_RSDIRECTORY_EDIT_ENTRY') );
					
				$can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries') && $user->id == $entry->user_id;
					
				// Check permissions.
				if ( !$user->id && JFactory::getApplication()->getUserState('com_rsdirectory.registration.user.id') )
				{
					return $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_EDIT_GUEST_WARNING'), 'warning' );
				}
				else if ( !( $user->id != 0 && ($this->can_edit_all_entries || $can_edit_own_entries) ) )
				{
					return $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_EDIT_PERMISSION_ERROR'), 'error' );
				}
					
				if (!$entry->paid)
				{
					$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_UNPAID'), 'info' );
				}
				else if (!$entry->published)
				{
					$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_AWAITING_MODERATION'), 'info' );
				}
					
				$display_form = true;
					
				// Get the number of unpaid entry credits.
				$unpaid_entry_credits = RSDirectoryCredits::getUnpaidEntryCreditsSum($entry_id);
					
				$this->entry = $entry;
				$this->entry_credits = RSDirectoryCredits::getEntryCreditsObjectList($entry_id);
			}
			else
			{
				// Deny permission if the user does not have the proper permission.
				if ( !RSDirectoryHelper::checkUserPermission('can_add_entries') )
				{
					return $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_ADD_PERMISSION_ERROR'), 'error' );
				}
					
				if ($category_id)
				{
					$display_form = true;
				}
				else
				{
					$subcategories = RSDirectoryHelper::getSubcategories( $item->params->get('catid') );
						
					if ( ( isset($subcategories[0]) && $subcategories[0]->getChildren() ) || isset($subcategories[1]) )
					{
						$this->categories_select = RSDirectoryHelper::getCategoriesSelect($subcategories);
					}
					else if ( isset($subcategories[0]) )
					{    
						$display_form = true;
						$category_id = $subcategories[0]->id;
					}
					else if ( $item->params->get('catid') )
					{
						$display_form = true;
						$category_id = $item->params->get('catid');
							
						$category = RSDirectoryHelper::getCategory($category_id);
							
						if (!$category || !$category->published)
						{
							JError::raiseError( 500, JText::_('COM_RSDIRECTORY_NO_CATEGORY_CONFIGURED') );
						}
					}
					else
					{
						JError::raiseError( 500, JText::_('COM_RSDIRECTORY_NO_CATEGORY_CONFIGURED') );
					}
				}
			}
				
				
			if ($display_form)
			{
				// Get the form associated to the category.
				$form = RSDirectoryHelper::getCategoryInheritedForm($category_id);
					
				if (!$form->id)
				{
					JError::raiseError( 500, JText::_('COM_RSDIRECTORY_NO_CATEGORY_FORM_CONFIGURED') );
				}
					
				$this->form = $form;
					
				if ($form)
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
						$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
						$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.rsmap.js?v=' . RSDirectoryVersion::$version );
					}
						
					$this->form_fields = $form_fields;
					   
					require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/formfield.php';
				}
					
				// Build the login return url.
				$this->login_return = JUri::getInstance()->toString();
			}
				
			$this->data = $data;
			$this->data['category_id'] = $category_id;
			$this->item = $item; 
			$this->display_form = $display_form;
			$this->jform = $this->get('Form');
		}
		// "Thank you" page.
		else if ($layout == 'thank_you')
		{
			// Retrieve the entry id from the session.
			$id = $app->getUserState('com_rsdirectory.edit.entry.id');
				
			if (!$id)
			{
				$app->redirect('index.php');
			}
				
			// Clear the id from the session.
			$app->setUserState('com_rsdirectory.edit.entry.id', null);
				
			// Get the Entry model.
			$model = $this->getModel();
				
			// Get the entry.
			$entry = $model->getItem($id);
				
			if (!$entry)
			{
				JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
			}
				
			// Get the form assigned to the category.
			$form = RSDirectoryHelper::getCategoryInheritedForm($entry->category_id);
				
			// Get form fields.
			$form_fields = RSDirectoryHelper::getFormFields($form->id);
				
				
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/placeholders.php';
				
			// Create a new placeholders object.
			$this->message = RSDirectoryPlaceholders::getInstance($form->entry_submit_thank_you_message, $form_fields, $entry, $form)->process();
		}
		// Confirm finalization.
		else if ($layout == 'finalize_confirm')
		{
			$entry = $this->get('Item');
				
			// Raise an error if no entry was found with that id.
			if (!$entry)
			{
				JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
			}
				
			$can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
			$can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries') && $user->id == $entry->user_id;
				
			// Check permissions.
			if ( !( $user->id != 0 && ($can_edit_all_entries || $can_edit_own_entries) ) )
			{
				return $app->enqueueMessage( JText::_('COM_RSDIRECTORY_PERMISSION_DENIED'), 'error' );
			}
				
			$this->id = $entry->id;
			$this->entry_summary = RSDirectoryCredits::getEntrySummary($entry->id, false);
			$this->total = RSDirectoryCredits::getUnpaidEntryCreditsSum($entry->id);
		}
			
		parent::display($tpl);
			
		// Clear the stored data.
		$app->setUserState('com_rsdirectory.edit.entry.data', null);
			
		// Clear errors.
		$app->setUserState('com_rsdirectory.edit.entry.error_field_ids', null);
		$app->setUserState('com_rsdirectory.edit.entry.captcha_error', null);
		$app->setUserState('com_rsdirectory.edit.entry.error_reg_fields', null);
    }
		
	/**
	 * Prepares the document.
	 *
	 * @access protected
	 *
	 * @param object $entry
	 */
	protected function _prepareDocument($entry)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
			
		// Because the application sets a default page title, we need to get it from the menu item itself.
		$menu = $menus->getActive();
			
		if ($menu)
		{
			$this->params->def( 'page_heading', $this->params->get('page_title', $menu->title) );
		}
		else
		{
			$this->params->def( 'page_heading', $entry->title );
		}
			
		$robots = $this->params->get('robots');
			
		if ( $menu && isset($menu->query['view'], $menu->query['id']) && $menu->query['view'] == 'entry' && $menu->query['id'] == $entry->id )
		{
			$title = $this->params->get('page_title', '');
			$metadesc = $this->params->get('menu-meta_description');
			$metakey = $this->params->get('menu-meta_keywords');
		}
		else
		{
			$title = $entry->title;
			$metadesc = RSDirectoryHelper::cut( strip_tags($entry->description), 140);
			$metakey = str_replace(', ', ' ', $entry->title);
		}
			
		// Set the browser title.
		$sitename = $app->getCfg('sitename');
		$sitename_pagetitles = $app->getCfg('sitename_pagetitles', 0);
			
		if ( empty($title) )
		{
			$title = $sitename;
		}
		else if ($sitename_pagetitles == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $sitename, $title);
		}
		else if ($sitename_pagetitles == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $sitename);
		}
			
		$this->document->setTitle($title);
			
		// Set the meta description.
		if ($metadesc)
		{
			$metadesc = str_replace( array("\r\n", "\n"), ' ', $metadesc );
				
			$this->document->setDescription($metadesc);
		}
			
		// Set the meta keywords.
		if ($metakey)
		{
			$this->document->setMetadata('keywords', $metakey);
		}
			
		// Set the robots tag.
		if ( !empty($robots) )
		{
			$this->document->setMetadata('robots', $robots);
		}
	}
}
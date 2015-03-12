<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ($this->print)
    return;

$config = $this->config;

if ( $config->get('enable_comments') )
{
    $doc = JFactory::getDocument();
         
    switch ( $config->get('commenting_system') )
    {
		case 'facebook':
				 
			$script =  '(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=160858864014923";
						  fjs.parentNode.insertBefore(js, fjs);
						  }(document, "script", "facebook-jssdk"));';
				 
			// Add the script declaration.
			$doc->addScriptDeclaration($script);
				
			$href = htmlentities( RSDirectoryHelper::absJRoute($_SERVER['REQUEST_URI'], false, false) );
				
			// Get the posts limit.
			$posts_number = $config->get('facebook_comments_posts_number');
				
			echo '<div id="fb-root"></div>';
				
			echo '<div class="fb-comments" data-href="' . $href . '" data-num-posts="' . $posts_number . '"></div>';
				
			break;
				
		case 'disqus':
				
			$script = ' var disqus_shortname = "' . htmlentities( $config->get('disqus_short_name') ) . '";
						var disqus_developer = ' . ( (int)$config->get('disqus_developer_mode') ) . ';
						  
						(function()
						{
							var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
							dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
							(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
						})();';
						  
			// Add the script declaration.
			$doc->addScriptDeclaration($script);
				
			echo '<div id="disqus_thread"></div><noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>';
				
			break;
				
		case 'com_rscomments':
				
			if ( file_exists(JPATH_SITE . '/components/com_rscomments/helpers/rscomments.php') ) 
			{
				require_once JPATH_SITE . '/components/com_rscomments/helpers/rscomments.php';
				echo RSCommentsHelper::showRSComments('com_rsdirectory', $this->entry->id);
			}
				
			break;
    }
}
<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

class RSDirectoryRoute
{
	/**
	 * RSDirectoryRoute instance.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @var RSDirectoryRoute
	 */
	private static $instance;
		
	/**
	 * Base route.
	 */
	const BASE_ROUTE = 'index.php?option=com_rsdirectory';
		
	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct()
	{
		$this->router = JApplication::getInstance('site')->getRouter();
	}
		
	/**
	 * Method to get RSDirectoryRoute instance.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @return RSDirectoryRoute
	 */	
	public static function getInstance()
	{
		// Create a new instance of RSDirectoryRoute.
		if ( empty(self::$instance) )
		{
			self::$instance = new RSDirectoryRoute;
		}
			
		return self::$instance;
	}
		
	/**
	 * Method to get a humanly readible URL from an internal Joomla URL.
	 *
	 * @access public
	 *
	 * @param string $route
	 * @param bool $absolute
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getParsedURL($route, $absolute = false, $xhtml = true)
	{
		$router = $this->router->build($route);
			
		$url = $router->toString();
        $url = str_replace('/administrator', '', $url);
		 
		if ($absolute)
		{
			$base = str_replace( '/administrator', '', JURI::base(true) );
			$url = JURI::root() . substr( $url, strlen($base) + 1 );
		}
			
		if ($xhtml)
		{
			$url = htmlspecialchars($url);
		}
			
		return $url;
	}
		
	/**
	 * Method to generate an entry URL.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $id
	 * @param string $title
	 * @param string $layout
	 * @param int $Itemid
	 * @param bool $absolute
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public static function getEntryURL($id, $title, $layout = 'default', $Itemid = 0, $absolute = false, $xhtml = true)
	{
		$layout = empty($layout) ? 'default' : $layout;
			
		$route = self::BASE_ROUTE . "&view=entry&layout=$layout&id=" . self::sef($id, $title) . ($Itemid ? "&Itemid=$Itemid" : '');
		return self::getInstance()->getParsedURL($route, $absolute, $xhtml);
	}
		
	/**
	 * Method to generate a category entries URL.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $id
	 * @param string $title
	 * @param int $Itemid
	 * @param bool $absolute
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public static function getCategoryEntriesURL($id, $title, $Itemid = 0, $absolute = false, $xhtml = true)
	{
		$route = self::BASE_ROUTE . "&view=entries&category=" . self::sef($id, $title) . ($Itemid ? "&Itemid=$Itemid" : '');
		return self::getInstance()->getParsedURL($route, $absolute, $xhtml);
	}
		
	/**
	 * Method to generate an user entries URL.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $id
	 * @param string $name
	 * @param bool $absolute
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public static function getUserEntriesURL($id, $name, $absolute = false, $xhtml = true)
	{
		$route = self::BASE_ROUTE . "&view=entries&user=" . self::sef($id, $name);
		return self::getInstance()->getParsedURL($route, $absolute, $xhtml);
	}
		
	/**
	 * Method to get a generic URL.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param string $view
	 * @param string $layout
	 * @param mixed $params
	 * @param bool $absolute
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public static function getURL($view, $layout = 'default', $params = '', $absolute = false, $xhtml = true)
	{
		$layout = empty($layout) ? 'default' : $layout;
			
		$params_string = '';
			
		if ($params)
		{
			if ( is_array($params) )
			{
				foreach ($params as $key => $value)
				{
					$params_string .= "&$key=$value";
				}
			}
			else if ( is_string($params) )
			{
				$params_string = $params{0} != '&' ? "&$params" : $params;
			}
		}
			
		$route = self::BASE_ROUTE . "&view=$view&layout=$layout" . $params_string;
		return self::getInstance()->getParsedURL($route, $absolute, $xhtml);
	}
		
	/**
     * SEF.
     *
     * @access public
     *
     * @static
     *
     * @param int $id
     * @param string $name
     *
     * @return string
     */
    public static function sef($id, $name)
    {
        return intval($id) . ( $name ? ':' . JFilterOutput::stringURLSafe($name) : '');
    }
}
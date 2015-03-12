<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

echo '<?xml version="1.0" encoding="utf-8"?>';

?>

<root>
<?php
if ($this->items)
{
	foreach ($this->items as $item)
	{
		unset($image);
			
		if ( !empty($item->form->fields) )
		{
			$images = RSDirectoryHelper::findFormField('images', $item->form->fields);
				
			if ( $images && !empty($images->files[0]->hash) )
			{
				$image = RSDirectoryHelper::getImageURL($images->files[0]->hash);
			}
		}
			
		$link = RSDirectoryRoute::getEntryURL($item->id, $item->title, '', 0, true);
			
		$itemDate = JFactory::getDate($item->published_time);
		$itemDate->setTimeZone($this->tz);
			
		?>
	<item>
        <title><?php echo htmlspecialchars( strip_tags($item->title), ENT_COMPAT, 'UTF-8' ); ?></title>
		<link><?php echo str_replace(' ', '%20', $link); ?></link>
		<description><![CDATA[<?php echo RSDirectoryHelper::relToAbs($item->description); ?>]]></description>
		<author><?php echo htmlspecialchars($item->author, ENT_COMPAT, 'UTF-8'); ?></author>
		<category><?php echo htmlspecialchars($item->category_title, ENT_COMPAT, 'UTF-8'); ?></category>
        <pubDate><?php echo htmlspecialchars( $itemDate->toRFC822(true), ENT_COMPAT, 'UTF-8' ); ?></pubDate>
		<?php if ( !empty($image) ) { ?>
        <image><?php echo $image; ?></image>
		<?php } ?>
    </item>
		<?php
	}
}
?>
</root>
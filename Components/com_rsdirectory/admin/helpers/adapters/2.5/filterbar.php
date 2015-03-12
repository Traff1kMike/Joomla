<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

class RSFilterBar
{
    /**
     * Show the search (filter).
     *
     * @var mixed
     * 
     * @access public
     */
    public $search = null;
        
    /**
     * Show additional items located in the right.
     *
     * @var mixed
     * 
     * @access public
     */ 
    public $rightItems = array();
        
    /**
     * Class constructor.
     *
     * @access public
     * 
     * @param array $options
     */
    public function __construct( $options = array() )
    {
        foreach ($options as $k => $v)
        {
            $this->{$k} = $v;
        }
    }
        
    /**
     * Show filter bar.
     *
     * @access public
     */
    public function show()
    { 
        ?>
        <fieldset id="filter-bar"> 
        <?php
        if ($this->search)
        {
            $s = new JRegistry($this->search);
        ?>
            <div class="filter-search fltlft">
                <label class="filter-search-lbl" for="filter_search"><?php echo self::escape( $s->get('label') ); ?></label>
                <input id="filter_search" type="text" name="filter_search" value="<?php echo self::escape( $s->get('value') ); ?>" title="<?php echo self::escape( $s->get('title') ); ?>" placeholder="<?php echo self::escape( $s->get('placeholder') ); ?>" />
                <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
            </div>
        <?php
        }
        ?>
                
            <?php if ($this->rightItems) { ?>
                <?php foreach ($this->rightItems as $item) { ?>
                <div class="filter-select fltrt">
                    <?php echo $item['input']; ?>
                </div>
                <?php } ?>
            <?php } ?>
                
        </fieldset>
        <div class="clr"> </div>
        <?php
    }
        
    /**
     * Escape string.
     *
     * @access protected
     * 
     * @static
     * 
     * @param string
     * 
     * @return string
     */
    protected static function escape($string)
    {
        return htmlentities($string, ENT_COMPAT, 'utf-8');
    }
}
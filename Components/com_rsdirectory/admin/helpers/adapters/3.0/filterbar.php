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
     * Load the formbehavior.
     *
     * @var bool
     * 
     * @access public
     */
    public $behavior = true;
        
    /**
     * Show the search (filter).
     *
     * @var mixed
     * 
     * @access public
     */
    public $search = null;
        
    /**
     * Show the pagination limit box.
     *
     * @var mixed
     * 
     * @access public
     */ 
    public $limitBox = null;
        
    /**
     * Show additional items located in the right.
     *
     * @var mixed
     * 
     * @access public
     */ 
    public $rightItems = array();
        
    /**
     * Show the ordering select.
     *
     * @var bool
     * 
     * @access public
     */ 
    public $orderDir = true;
        
    /**
     * The ordering direction.
     *
     * @var string
     * 
     * @access public
     */
    public $listDirn = '';
        
    /**
     * Show the sorting select.
     *
     * @var array
     * 
     * @access public
     */ 
    public $sortFields = array();
        
    /**
     * The ordering column.
     *
     * @var string
     * 
     * @access public
     */
    public $listOrder = '';
        
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
            
        if ($this->behavior)
        {
            JHtml::_('formbehavior.chosen', 'select');
        }
    }
        
    /**
     * Show filter bar.
     *
     * @access public
     */
    public function show()
    {
        if ($this->sortFields || $this->orderDir)
        {
            $doc = JFactory::getDocument();
                
            $script = " Joomla.orderTable = function(listOrder)
                        {
                            table = document.getElementById('sortTable');
                            direction = document.getElementById('directionTable');
                            order = table.options[table.selectedIndex].value;
                                
                            if (order != listOrder)
                            {
                                dirn = 'asc';
                            }
                            else
                            {
                                dirn = direction.options[direction.selectedIndex].value;
                            }
                                
                            Joomla.tableOrdering(order, dirn, '');
                        }";
                            
            $doc->addScriptDeclaration($script);
        }
        ?>
        <div id="filter-bar" class="btn-toolbar">
                
            <?php if ($this->search) { ?>
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo $this->search['label']; ?></label>
                <input type="text" name="filter_search" id="filter_search" value="<?php echo self::escape($this->search['value']); ?>" title="<?php echo self::escape($this->search['title']); ?>" placeholder="<?php echo self::escape($this->search['placeholder']); ?>" />
            </div>
            <div class="btn-group hidden-phone">
                <button class="btn" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn" type="button" onclick="document.id('filter_search').value=''; this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
            </div>
            <?php } ?>
                
            <?php if ($this->limitBox) { ?>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->limitBox; ?>
            </div>
            <?php } ?>
                
            <?php if ($this->orderDir) { ?>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select id="directionTable" name="filter_order_Dir" class="input-small" onchange="Joomla.orderTable('<?php echo self::escape($this->listOrder); ?>')">
                    <?php echo JHtml::_( 'select.options', array( JHtml::_('select.option', '', JText::_('JFIELD_ORDERING_DESC') ), JHtml::_( 'select.option', 'asc', JText::_('COM_RSDIRECTORY_ASC') ), JHtml::_( 'select.option', 'desc', JText::_('COM_RSDIRECTORY_DESC') ) ), 'value', 'text', $this->listDirn, false); ?>
                </select>
            </div>
            <?php } ?>
                
            <?php if ($this->sortFields) { ?>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select id="sortTable" name="filter_order" class="input-medium" onchange="Joomla.orderTable('<?php echo self::escape($this->listOrder); ?>')">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrder);?>
                </select>
            </div>
            <?php } ?>
                
            <div class="clearfix"> </div>
        </div>
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
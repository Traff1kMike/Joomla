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

// Display the reviews list.
if ($this->enable_reviews)
{
    $config = $this->config;
        
    ?>
        
    <h4 id="reviews" class="rsdir-detail-section-title"><?php echo JText::_('COM_RSDIRECTORY_REVIEWS'); ?></h4>
        
	<div id="reviews-list" class="control-group">
    <?php
	if ($this->reviews)
	{
		foreach ($this->reviews as $review)
		{
			echo RSDirectoryHelper::getReviewHTML($review);
		}
			
		if ($this->load_more)
		{
			?>
				
			<div class="rsdir-load-more">
				<div class="btn btn-primary"><?php echo JText::_('COM_RSDIRECTORY_SHOW_MORE'); ?></div>
				<img class="rsdir-loader hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />
			</div>
				
			<?php
		}
	}
	else
	{
	?>
        <p id="no-reviews"><?php echo JText::_('COM_RSDIRECTORY_NO_REVIEWS'); ?></p>
    <?php } ?>
	</div>
        
    <?php
	
	if ( $this->entry->user_id == $this->user->id && $config->get('enable_owner_reply') )
	{
	?>
		<div id="rsdir-owner-reply-modal" class="rsdir-iframe-modal modal hide fade" tabindex="-1" role="dialog" aria-labelledby="rsdir-owner-reply-header" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="rsdir-owner-reply-header"></h3>
			</div>
			<div class="modal-body">
				<iframe style="height: <?php echo $this->config->get('owner_reply_modal_body_height', 350); ?>px;"></iframe>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSDIRECTORY_CLOSE'); ?></button>
				<button class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
			</div>
		</div>
	<?php
	}
}
    
// Display the review/rating form.
if ( $this->can_vote_own_entry && ( ($this->enable_reviews && $this->can_post_reviews) || ($this->enable_ratings && $this->can_cast_votes) ) )
{
    if (!$this->has_review)
    {
		if ($this->enable_reviews && $this->can_post_reviews)
		{
			?>
				
			<form id="rsdir-review-form" action="#" method="post">
					
				<?php if ($this->enable_ratings && $this->can_cast_votes) { ?>
				<div class="control-group">
					<label><?php echo JText::_('COM_RSDIRECTORY_RATE_THIS'); ?></label>
					<div class="rsdir-rate-entry"></div>
				</div>
				<?php } ?>
					
				<?php if ($this->user->guest) { ?>
				<div class="control-group">
					<label><?php echo JText::_('COM_RSDIRECTORY_NAME'); ?></label>
					<input type="text" name="jform[name]" />
				</div>
					
				<div class="control-group">
					<label><?php echo JText::_('COM_RSDIRECTORY_EMAIL'); ?></label>
					<input type="text" name="jform[email]" />
				</div>
				<?php } ?>
					
				<div class="control-group">
					<label><?php echo JText::_('COM_RSDIRECTORY_SUBJECT'); ?></label>
					<input type="text" name="jform[subject]" />
				</div>
					
				<div class="control-group">
					<label><?php echo JText::_('COM_RSDIRECTORY_REVIEW'); ?></label>
					<textarea class="input-xxlarge" cols="60" rows="10" name="jform[review]"></textarea>    
				</div>
					
				<div class="control-group clearfix">
					<img id="rsdir-review-loader" class="hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />
					<button id="rsdir-review-submit" class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
				</div>
					
				<div>
					<?php echo JHTML::_('form.token') . "\n"; ?>
					<input type="hidden" name="jform[entry_id]" value="<?php echo $this->entry->id; ?>" />
				</div>    
					
			</form>
				 
			<?php
		}
		else if ($this->enable_ratings && $this->can_cast_votes)
		{
			?>
				
			<form id="rsdir-review-form" class="rsdir-rating-form" action="#" method="post">
				<label><?php echo JText::_('COM_RSDIRECTORY_RATE_THIS'); ?></label>
				<div class="rsdir-rate-entry"></div>
				<img id="rsdir-review-loader" class="hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />
				<div>
					<?php echo JHTML::_('form.token') . "\n"; ?>
					<input type="hidden" name="jform[entry_id]" value="<?php echo $this->entry->id; ?>" />
				</div>
			</form>
				
			<?php
		}
    }
	else if (!$this->has_posted_review)
	{
		?>
			
		<div class="alert alert-info">
			<?php echo JText::_('COM_RSDIRECTORY_REVIEW_AWAITING_MODERATION'); ?>
		</div>
			
		<?php
    }
}
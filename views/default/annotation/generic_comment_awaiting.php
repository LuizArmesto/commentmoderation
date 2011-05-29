<?php

	/**
	 * Elgg generic comment awaiting
	 *
	 * @package ElggCommentModeration
	 */

$owner = get_user($vars['annotation']->owner_guid);
$entity = get_entity($vars['annotation']->entity_guid);

?>
<div class="generic_comment generic_comment_awaiting"><!-- start of generic_comment div -->

	<div class="generic_comment_icon">
		<?php
			echo elgg_view("profile/icon",
				array(
					'entity' => $owner,
					'size' => 'small'
				)
			);
		?>
	</div>
	<div class="generic_comment_details">

		<!-- output the actual comment -->
		<?php echo elgg_view("output/longtext",array("value" => $vars['annotation']->value)); ?>

		<p class="generic_comment_owner">
			<a href="<?php echo $owner->getURL(); ?>"><?php echo $owner->name; ?></a> <?php echo elgg_view_friendly_time($vars['annotation']->time_created); ?>
		</p>

		<?php
			// if we are listing all pending comments, show the entity link
			if (get_context() == "commentmoderation") {
		?>

		<p class="generic_comment_entity">
			<?php echo elgg_echo("commentmoderation:commented_in"); ?> <a href="<?php echo $entity->getURL(); ?>"><?php echo $entity->title; ?></a>
		</p>

		<?php
			} else {
				?>

		<p class="generic_comment_info">
			<?php echo elgg_echo("commentmoderation:comment_is_awaiting"); ?>
		</p>

				<?php
			}

			// if the user looking at the comment can edit, show the delete link
			if ($vars['annotation']->canEdit()) {
				if ($vars['annotation']->owner_guid != get_loggedin_userid()) {
					?>
		<p>
			<a href="<?php echo elgg_add_action_tokens_to_url($vars['url'] . "action/comments/approve?annotation_id=" . $vars['annotation']->id); ?>"><?php echo elgg_echo('commentmoderation:approve'); ?></a>
		</p>

					<?php
				}
				?>
		<p>
				<?php

				echo elgg_view("output/confirmlink",array(
					'href' => $vars['url'] . "action/comments/delete?annotation_id=" . $vars['annotation']->id,
					'text' => elgg_echo('delete'),
					'confirm' => elgg_echo('deleteconfirm'),
				));

				?>
		</p>

				<?php
			} //end of can edit if statement
		?>
	</div><!-- end of generic_comment_details -->
</div><!-- end of generic_comment div -->

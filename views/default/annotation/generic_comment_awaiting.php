
<?php
/**
 * Elgg generic comment view
 *
 * @uses $vars['annotation']    ElggAnnotation object
 * @uses $vars['full_view']          Display fill view or brief view
 */

if (!isset($vars['annotation'])) {
	return true;
}

$full_view = elgg_extract('full_view', $vars, true);

$comment = $vars['annotation'];

$entity = get_entity($comment->entity_guid);
$commenter = get_user($comment->owner_guid);
if (!$entity || !$commenter) {
	return true;
}

$friendlytime = elgg_view_friendly_time($comment->time_created);

$commenter_icon = elgg_view_entity_icon($commenter, 'tiny');
$commenter_link = "<a href=\"{$commenter->getURL()}\">$commenter->name</a>";

$entity_title = $entity->title ? $entity->title : elgg_echo('untitled');
$entity_link = "<a href=\"{$entity->getURL()}\">$entity_title</a>";

if ($full_view) {

	$approve_button = '';
	if(commentmoderation_can_approve($comment)) {
		$url = elgg_add_action_tokens_to_url("{$vars['url']}action/comments/approve?annotation_id={$comment->id}");
		$text = "<span class=\"elgg-icon elgg-icon-checkmark right\"></span>";
		$title = elgg_echo('commentmoderation:approve');
		if (elgg_get_context() == "admin") {
			$text = "<span class=\"right\">$title&nbsp;|&nbsp;</span>";
		}
		$approve_button = "<a title=\"$title\" href=\"$url\">$text</a>";
	}

	$delete_button = '';
	if ($comment->canEdit()) {
		$url = "action/comments/delete?annotation_id=$comment->id";
		$text = "<span class=\"elgg-icon elgg-icon-delete right\"></span>";
		$title = elgg_echo('delete');
		if (elgg_get_context() == "admin") {
			$text = "<span class=\"right\">$title</span>";
		}
		$delete_button = elgg_view("output/confirmlink", array(
							'href' => $url,
							'text' => $text,
							'title' => $title,
							'confirm' => elgg_echo('deleteconfirm'),
							'text_encode' => false,
						));
	}

	$comment_text = elgg_view("output/longtext", array("value" => $comment->value));

	$class = '';
	$entity_info = '';
	if ((elgg_get_context() == "commentmoderation") || (elgg_get_context() == "admin")) {
		$entity_info = "<div class=\"elgg-subtext\">" . elgg_echo('commentmoderation:commented_in', array($entity_link)) . "</div>";
	} else {
		$class = "commentmoderation-generic-comment-awaiting";
	}

	$moderation_info = elgg_echo("commentmoderation:comment_is_awaiting");

	$body = <<<HTML
<div class="mbn">
	$delete_button
	$approve_button
	$commenter_link
	<span class="elgg-subtext">
		$friendlytime
		$entity_info
	</span>
	$comment_text
	<span class="elgg-subtext">
		$moderation_info
	</span>
</div>
HTML;

	echo "<div class=\"$class\">";
	echo elgg_view_image_block($commenter_icon, $body);
	echo "</div>";

} else {
	// brief view

	//@todo need link to actual comment!

	$on = elgg_echo('on');

	$excerpt = elgg_get_excerpt($comment->value, 80);

	$body = <<<HTML
<span class="elgg-subtext">
	$commenter_link $on $entity_link ($friendlytime): $excerpt
</span>
HTML;

	echo elgg_view_image_block($commenter_icon, $body);
}

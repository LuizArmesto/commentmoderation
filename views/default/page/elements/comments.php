<?php
/**
 * List comments with optional add form
 *
 * @uses $vars['entity']        ElggEntity
 * @uses $vars['show_add_form'] Display add form or not
 * @uses $vars['id']            Optional id for the div
 * @uses $vars['class']         Optional additional class for the div
 */

$show_add_form = elgg_extract('show_add_form', $vars, true);

$id = '';
if (isset($vars['id'])) {
	$id = "id =\"{$vars['id']}\"";
}

$class = 'elgg-comments';
if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
}

// work around for deprecation code in elgg_view()
unset($vars['internalid']);

echo "<div $id class=\"$class\">";

$logged_in_user_guid = elgg_get_logged_in_user_guid();

$where = '';
if ($logged_in_user_guid != $vars['entity']->owner_guid AND !elgg_is_admin_logged_in()) {
	$where = "(n_table.owner_guid IN ($logged_in_user_guid) AND msn.string IN ('generic_comment_awaiting')) OR msn.string IN ('generic_comment')";
} else {
	$num_awaiting = (int)commentmoderation_count_comments_awaiting($vars['entity']->getGUID());
	$comments_awaiting_info = "<p class=\"comments_awaiting_bar\">";
	$comments_awaiting_info .= elgg_echo("commentmoderation:comments_awaiting_approval", array($num_awaiting));
	$comments_awaiting_info .= "</p>";
}
$options = array(
	'guid' => $vars['entity']->getGUID(),
	'annotation_names' => array('generic_comment', 'generic_comment_awaiting'),
	'where' => $where
);
$html = elgg_list_annotations($options);
if ($html) {
	echo '<h3>' . elgg_echo('comments') . '</h3>';
	echo $html;
}

if ($comments_awaiting_info) {
	echo $comments_awaiting_info;
}

if ($show_add_form) {
	$form_vars = array('name' => 'elgg_add_comment');
	echo elgg_view_form('comments/add', $form_vars, $vars);
}

echo '</div>';

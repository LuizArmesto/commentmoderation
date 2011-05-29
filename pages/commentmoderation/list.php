<?php
/**
* Elgg comment moderation comments awaiting list page
*
* @package ElggCommentModeration
*/

gatekeeper();

// Get the logged in user, only admin can see other peoples comments awaiting approval
$logged_in_user = elgg_get_logged_in_user_entity();
$filter = get_input('filter', 'owner');


if ($filter == "all") {
	// Display ALL comments awaiting approval
	admin_gatekeeper();
	$page_owner = $logged_in_user;
} else if ((!$username = get_input('username', $logged_in_user->username)) ||
	(!$page_owner = get_user_by_username($username))) {
	// Forward to dashboard if passed an invalid username
	forward();
}

elgg_set_page_owner_guid($page_owner->getGUID());

$layout = 'one_sidebar';

// Only page owner or admin can access
if ($page_owner != $logged_in_user) {
	admin_gatekeeper();
}

if ($filter == "all") {
	// Get all comments awaiting approval
	$options = array(
		'annotation_name' => 'generic_comment_awaiting'
	);
	$comments = elgg_list_annotations($options);
	// Set the page title
	$title = elgg_echo('commentmoderation:list_all');
} else {
	// Get comments awaiting approval for "page_owner" posts
	$options = array(
		'annotation_name' => 'generic_comment_awaiting',
		'where' => "(e.owner_guid in ({$page_owner->getGUID()}))"
	);
	$comments = elgg_list_annotations($options);
// Set the page title
	if ($page_owner == $logged_in_user) {
		$title = elgg_echo('commentmoderation:list_mine');
	} else {
		$title = elgg_echo('commentmoderation:list', array($page_owner->name));
	}
}
if ($comments) {
	$content .= $comments;
} else {
	$content .= '<p class="mtm">' . elgg_echo('commentmoderation:no_comments_awaiting') . '</p>';
}
// format
$body = elgg_view_layout($layout, array(
	'content' => $content,
	'title' => $title,
	'sidebar' => '',
));

// Draw page
echo elgg_view_page($title, $body);

?>

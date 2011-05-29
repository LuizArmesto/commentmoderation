<?php

	/**
	 * Elgg comment moderation comments awaiting list page
	 *
	 * @package ElggCommentModeration
	 */

	// Load Elgg engine
		require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

	// You need to be logged in!
		gatekeeper();

	// Get the logged in user, only admin can see other peoples comments awaiting approval
		$loggedin_user = get_loggedin_user();
		$filter = get_input('filter', 'owner');

		if ($filter == "all") {
	// Display ALL comments awaiting approval
			admin_gatekeeper();
			$page_owner = $loggedin_user;
		} else if ((!$username = get_input('username', $logged_in_user->username)) ||
		  (!$page_owner = get_user_by_username($username))) {
	// Forward to dashboard if passed an invalid username
			forward();
		}
		set_page_owner($page_owner->getGUID());

	// Only page owner or admin can access
		if ($page_owner != $loggedin_user) {
			admin_gatekeeper();
		}

	// Get offset
		$offset = get_input('annoff', 0);

	// Set limit
		$limit = 10;

		if ($filter == "all") {
		// Get all comments awaiting approval
			$count = count_comments_awaiting(0, "", "", "", "", 0, 0, 0, 0);
			$comments = get_annotations(0, "", "", "generic_comment_awaiting", "", 0, $limit, $offset, "asc", 0, 0, 0);
	// Set the page title
			$area2 = elgg_view_title(elgg_echo('commentmoderation:list_all'));
		} else {
		// Get comments awaiting approval for "page_owner" posts
			$count = count_comments_awaiting(0, "", "", "", "", 0, 0, 0, $page_owner->getGUID());

			if ($count > 0) {
				$comments = get_annotations(0, "", "", "generic_comment_awaiting", "", 0, $limit, $offset, "asc", 0, 0, $page_owner->getGUID());
			}

	// Set the page title
            if ($page_owner == $loggedin_user) {
                $area2 = elgg_view_title(elgg_echo('commentmoderation:list_mine'));
            } else {
                $area2 = elgg_view_title(sprintf(elgg_echo('commentmoderation:list'), $page_owner->name));
            }
        }
		if ($count > 0) {
			$area2 .= elgg_view_annotation_list($comments, $count, $offset, $limit);
		} else {
			$area2 .= "<div class=\"generic_comment generic_comment_awaiting\">";
			$area2 .= elgg_echo("commentmoderation:no_comments_awaiting");
			$area2 .= "</div>";
		}

	// format
		$body = elgg_view_layout("two_column_left_sidebar", '', $area2);

	// Draw page
		page_draw(sprintf(elgg_echo('commentmoderation:user'), $page_owner->name), $body);

?>

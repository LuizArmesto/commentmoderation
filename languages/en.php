<?php

	/**
	 * Elgg comment moderation translation EN
	 *
	 * @package ElggCommentModeration
	 */

	$language = array(

		/**
		 * Menu items and titles
		 */

			'admin:utilities:commentmoderation' => "Comments awaiting",
			'commentmoderation' => "Comment Moderation",
			'commentmoderation:approve' => "Approve",
			'commentmoderation:commented_in' => "Commented on %s",
			'commentmoderation:comment_is_awaiting' => "This comment is awaiting approval.",
			'commentmoderation:comments_awaiting_approval' => "Comments awaiting approval: %s",
			'commentmoderation:list' => "List of comments awaiting approval from %s",
			'commentmoderation:list_mine' => "List of comments awaiting your approval",
			'commentmoderation:list_all' => "List of all comments awaiting approval",
			'commentmoderation:no_comments_awaiting' => "There are no comments awaiting to be approved.",
			'commentmoderation:admin:moderate_all_comments' => "Moderate comments",
			'commentmoderation:allow_custom_settings' => "Allow members to use custom settings?",
			'commentmoderation:custom_settings_not_allowed' => "The comment moderation settings are disabled.",
			'commentmoderation:moderate_subtypes' => "Which content type comments should be moderated?",

		/**
		 * Status messages
		 */
			'commentmoderation:awaiting' => "Your comment is awaiting approval.",
			'commentmoderation:approved' => "The comment was successfully approved.",

		/**
		 * Error messages
		 */
			'commentmoderation:notapproved' => "Could not approve the comment.",

	);

	add_translation("en",$language);

?>

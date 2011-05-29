<?php

	/**
	 * Elgg add comment action
	 *
	 * @package ElggCommentModeration
	 */

	// Make sure we're logged in; forward to the front page if not
	gatekeeper();

	// Get input
	$entity_guid = (int) get_input('entity_guid');
	$comment_text = get_input('generic_comment');

	// make sure comment is not empty
	if (empty($comment_text)) {
		register_error(elgg_echo("generic_comment:blank"));
		forward($_SERVER['HTTP_REFERER']);
	}

	// Let's see if we can get an entity with the specified GUID
	$entity = get_entity($entity_guid);
	if (!$entity) {
		register_error(elgg_echo("generic_comment:notfound"));
		forward($_SERVER['HTTP_REFERER']);
	}

	$user = get_loggedin_user();

// If the comment should not be moderated then call the original "comments/add" action
	if (!is_subtype_comments_moderated($entity->subtype, $entity->owner_guid) || isadminloggedin() || $entity->owner_guid == $user->guid) {
		return FALSE;
	}

	$name = 'generic_comment_awaiting';
	$email_subject = elgg_echo('generic_comment:email:subject');
	$email_body = elgg_echo('generic_comment:email:body');

	$annotation = create_annotation($entity->guid,
									$name,
									$comment_text,
									"",
									$user->guid,
									$entity->access_id);

	// tell user annotation posted
	if (!$annotation) {
		register_error(elgg_echo("generic_comment:failure"));
		forward($_SERVER['HTTP_REFERER']);
	}

	// notify if poster wasn't owner
	if ($entity->owner_guid != $user->guid) {

		notify_user($entity->owner_guid,
					$user->guid,
					$email_subject,
					sprintf(
						$email_body,
						$entity->title,
						$user->name,
						$comment_text,
						$entity->getURL(),
						$user->name,
						$user->getURL()
					)
				);
	}

	system_message(elgg_echo("commentmoderation:awaiting"));

	// Forward to the entity page
	forward($entity->getURL());

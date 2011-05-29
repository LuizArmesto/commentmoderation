<?php

	/**
	 * Elgg comment moderation: approve comment action
	 *
	 * @package ElggCommentModeration
	 */

	// Make sure we're logged in (send us to the front page if not)
		gatekeeper();

		$user = get_loggedin_user();

	// Get input data
		$annotation_id = (int) get_input('annotation_id');

	// Get comment object
		$annotation = get_annotation($annotation_id);
		$entity = get_entity($annotation->entity_guid);

		if ($user->getGUID() != $entity->owner_guid && !isadminloggedin()) {
			register_error(elgg_echo('actionunauthorized'));

		} else {

			if ($annotation && $annotation->name == "generic_comment_awaiting") {
			// Approve it
				$annotation->name = "generic_comment";
				$result = $annotation->save();

				if ($result) {
			// Success message
					system_message(elgg_echo("commentmoderation:approved"));
				//add to river
					add_to_river('annotation/annotate','comment',$annotation->owner_guid,$annotation->entity_guid, "", 0, $annotation->id);
				} else {
					register_error(elgg_echo("commentmoderation:notapproved"));
				}

			}
		}


	// Go back
		forward(REFERER);

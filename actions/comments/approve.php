<?php

/**
* Elgg comment moderation: approve comment action
*
* @package ElggCommentModeration
*/

// Make sure we're logged in (send us to the front page if not)
gatekeeper();

$user = get_loggedin_user();

//get input data
$annotation_id = (int) get_input('annotation_id');

//get comment object
$annotation = get_annotation($annotation_id);
$entity = get_entity($annotation->entity_guid);

$vars = array(
	'entity' => $entity,
	'annotation' => $annotation
);
$hook = elgg_trigger_plugin_hook('comments:approve', $entity->getType(), $vars, false);
if ($hook) {
	return true;
}

if (!commentmoderation_can_approve($annotation)) {
	register_error(elgg_echo('actionunauthorized'));

} else {

	if (commentmoderation_approve($annotation)) {
		//success message
		system_message(elgg_echo("commentmoderation:approved"));
		//add to river
		add_to_river('river/annotation/generic_comment/create', 'comment', $annotation->owner_guid, $entity->guid, "", 0, $annotation_id);
	} else {
		register_error(elgg_echo("commentmoderation:notapproved"));
	}
}

// Go back
forward(REFERER);

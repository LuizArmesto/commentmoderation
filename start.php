<?php
/**
* Elgg Comment Moderation plugin
*
* @package ElggCommentModeration
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
* @author Luiz Armesto
* @copyright Luiz Armesto 2011
*/

//@todo intercept notification message


elgg_register_event_handler('init', 'system', 'commentmoderation_init');

function commentmoderation_init() {
	// Load system configuration
	global $CONFIG;

	$logged_in_user = elgg_get_logged_in_user_entity();

	//load the language file
	register_translations($CONFIG->pluginspath . "commentmoderation/languages/");

	//extend some views
	elgg_extend_view('css/elgg', 'commentmoderation/css');

	//register a page handler
	elgg_register_page_handler('commentmoderation','commentmoderation_page_handler');

	//prevent to add moderated comments to river
	elgg_register_plugin_hook_handler('creating', 'river', 'commentmoderation_creating_river');

	//verify if the comment should await approval
	elgg_register_event_handler('create', 'annotation', 'commentmoderation_create_annotation');

	//register "comments/approve" action
	$action_base = elgg_get_plugins_path() . 'commentmoderation/actions/comments';
	elgg_register_action("comments/approve", "$action_base/approve.php");

	if ($logged_in_user) {
		//add topbar icon
		$class = "elgg-icon elgg-icon-speech-bubble";
		$text = "<span class=\"$class\"></span>";
		//get comments awaiting
		$num_comments = (int)commentmoderation_count_comments_awaiting(0, $logged_in_user->getGUID());
		if ($num_comments != 0) {
			$text .= "<span class=\"messages-new\">$num_comments</span>";
		}

		elgg_register_menu_item('topbar', array(
			'name' => 'commentmoderation',
			'href' => 'commentmoderation/owner/' . $logged_in_user->username,
			'text' => $text,
			'priority' => 600,
		));

		elgg_register_admin_menu_item('administer', 'commentmoderation', 'utilities');
	}
}

/**
 * commentmoderation page handler; allows the use of fancy URLs
 *
 * @param array $page From the page_handler function
 * @return true|false Depending on success
 */
function commentmoderation_page_handler($page) {
	// The first component of a moderation URL is the username
	if (isset($page[0])) {
		set_input('filter', $page[0]);
		set_input('username', $page[1]);
		$pages_base = elgg_get_plugins_path() . 'commentmoderation/pages/commentmoderation';
		@include("$pages_base/list.php");
		return true;
	}
	return false;
}

function commentmoderation_creating_river($hook, $action, $params, $returnvalue) {
	$annotation = elgg_get_annotation_from_id($params['annotation_id']);
	if ($annotation->name == 'generic_comment_awaiting') {
		//remove the successfully posted message
		foreach ($_SESSION['msg']['success'] as $pos => $message) {
			if ($message == elgg_echo("generic_comment:posted")) {
				unset($_SESSION['msg']['success'][$pos]);
				break;
			}
		}
		//prevent to post moderated comments to river
		return false;
	}
	return true;
}

function commentmoderation_create_annotation($event, $object_type, $annotation) {
	//only modify 'generic_comment' annotations
	if ($annotation->name != 'generic_comment') {
		return true;
	}

	$entity = get_entity($annotation->entity_guid);

	//verify the entity subtype moderation settings
	if (!commentmoderation_is_subtype_moderated($entity->subtype, $entity->owner_guid)) {
		return true;
	}

	//don't moderate comments from admin or entity owner
	if ((elgg_is_admin_logged_in()) || ($entity->owner_guid == elgg_get_logged_in_user_guid())) {
		return true;
	}

	//moderate the comment
	if (elgg_trigger_event('moderate', 'annotation', $annotation)) {
		$annotation->name = 'generic_comment_awaiting';
		$result = $annotation->save();
		system_message(elgg_echo("commentmoderation:awaiting"));

		return $result;
	}

	return true;
}

function commentmoderation_is_subtype_moderated($subtype, $user_guid = 0) {
	if (is_numeric($subtype)) {
		$subtype = get_subtype_from_id($subtype);
	}

	//if users can change the plugin settings use the user custom settings
	if (elgg_get_plugin_setting('allow_custom_settings', 'commentmoderation') != "no") {
		if (elgg_get_plugin_user_setting("moderate_subtype_{$subtype}", $user_guid, 'commentmoderation') != "no") {
			return true;
		}
	} else {
		//use the global settings
		if (elgg_get_plugin_setting("moderate_subtype_{$subtype}", 'commentmoderation') != "no") {
			return true;
		}
	}

	return false;
}

function commentmoderation_can_approve($comment, $user = false) {
	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}

	//admin can approve anything
	if ($user->isAdmin()) {
		return true;
	}

	//the entity owner can approve too
	$entity = get_entity($comment->entity_guid);
	if ($user->getGUID() == $entity->owner_guid) {
		return true;
	}

	return false;
}

function commentmoderation_count_comments_awaiting($guid = 0, $user_guid = false) {
	$options = array(
		'annotation_names' => array('generic_comment_awaiting'),
		'count' => true
	);

	if ($guid) {
		$options['guid'] = $guid;
	}

	if ($user_guid) {
		$options['where'] = "(e.owner_guid in ({$user_guid}))";
	}

	return elgg_get_annotations($options);
}

function commentmoderation_approve($annotation) {
	if (($annotation) && ($annotation->name == "generic_comment_awaiting") &&
	  (commentmoderation_can_approve($annotation))) {
		//approve it
		$annotation->name = "generic_comment";
		return $annotation->save();
	}

	return false;
}

?>

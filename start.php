<?php

	/**
	 * Elgg Comment Moderation plugin
	 *
	 * @package ElggCommentModeration
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Luiz Armesto
	 * @copyright Luiz Armesto 2011
	 */

	/**
	 * commentmoderation initialisation
	 *
	 * These parameters are required for the event API, but we won't use them:
	 *
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object
	 */

		function commentmoderation_init() {
			// Load system configuration
				global $CONFIG;

			// Load the language file
				register_translations($CONFIG->pluginspath . "commentmoderation/languages/");

				if (isloggedin()) {
            // Extend the elgg topbar
                    elgg_extend_view('elgg_topbar/extend','commentmoderation/topbar');
				}


			// Extend system CSS with our own styles, which are defined in the commentmoderation/css view
				elgg_extend_view('css','commentmoderation/css');

			//
				register_plugin_hook('action', 'comments/add', 'commentmoderation_action_comments_add');

			//
				register_plugin_hook('comments', 'object', 'commentmoderation_all_comments');

			// Register a page handler, so we can have nice URLs
				register_page_handler('commentmoderation','commentmoderation_page_handler');

		}


		function commentmoderation_pagesetup() {
			global $CONFIG;
			if (get_context() == 'admin' && isadminloggedin()) {
				add_submenu_item(elgg_echo('commentmoderation:admin:moderate_all_comments'), $CONFIG->wwwroot . 'pg/commentmoderation/all/');
            }
		}


		/**
		 * commentmoderation page handler; allows the use of fancy URLs
		 *
		 * @param array $page From the page_handler function
		 * @return TRUE|FALSE Depending on success
		 */
		function commentmoderation_page_handler($page) {
			// The first component of a moderation URL is the username
			if (isset($page[0])) {
				set_input('filter', $page[0]);
				set_input('username', $page[1]);
				@include(dirname(__FILE__) . "/index.php");
				return TRUE;
			}
			return FALSE;
		}


		function commentmoderation_action_comments_add($hook, $action, $returnvalue, $params) {
			global $CONFIG;
$teste = "aaaa";
		// Run our "comments/add" action instead of the original
			if (!include($CONFIG->pluginspath . "commentmoderation/actions/add.php")) {
				return TRUE;
			}
			return FALSE;
		}


		function commentmoderation_all_comments($hook, $entity_type, $returnvalue, $params) {
		//display the comments
			$comments = list_all_comments($params['entity']->getGUID(), 25, TRUE, $params['entity']->owner_guid);
		//display how many comments are awaiting approval
			if (isadminloggedin() || get_loggedin_userid() == $params['entity']->owner_guid) {
				$num_comments_awaiting = count_comments_awaiting($params['entity']->getGUID());
				$comments .= elgg_view('commentmoderation/comments_awaiting_bar',array('count' => $num_comments_awaiting));
			}
		//display the comment form
			$comments .= elgg_view('comments/forms/edit',$params);
			return $comments;
		}


		function list_all_comments($entity_guid, $limit = 25, $asc = TRUE, $entity_owner_guid) {
			if ($asc) {
				$asc = "asc";
			} else {
				$asc = "desc";
			}

			$count = count_annotations($entity_guid, "", "", "generic_comment");

			if (isadminloggedin() || get_loggedin_userid() == $entity_owner_guid) {
				$count += count_annotations($entity_guid, "", "", "generic_comment_awaiting");
			} else {
				$count += count_annotations($entity_guid, "", "", "generic_comment_awaiting", "", "", get_loggedin_userid());
			}

			$offset = (int) get_input("annoff",0);
			$annotations = get_all_comments($entity_guid, "", "", "", "", $limit, $offset, $asc, 0, 0, $entity_owner_guid);

			return elgg_view_annotation_list($annotations, $count, $offset, $limit);
		}


		function get_all_comments($entity_guid = 0, $entity_type = "", $entity_subtype = "",
		$value = "", $owner_guid = 0, $limit = 10, $offset = 0, $order_by = "asc", $timelower = 0, $timeupper = 0, $entity_owner_guid = 0) {
			global $CONFIG;

			$timelower = (int) $timelower;
			$timeupper = (int) $timeupper;

			if (is_array($entity_guid)) {
				if (sizeof($entity_guid) > 0) {
					foreach($entity_guid as $key => $val) {
						$entity_guid[$key] = (int) $val;
					}
				} else {
					$entity_guid = 0;
				}
			} else {
				$entity_guid = (int)$entity_guid;
			}

			if (is_array($entity_owner_guid)) {
				if (sizeof($entity_owner_guid) > 0) {
					foreach($entity_owner_guid as $key => $val) {
						$entity_owner_guid[$key] = (int) $val;
					}
				} else {
					$entity_owner_guid = 0;
				}
			} else {
				$entity_owner_guid = (int)$entity_owner_guid;
			}

			$limit = (int)$limit;
			$offset = (int)$offset;
			if($order_by == 'asc') {
				$order_by = "a.time_created asc";
			}

			if($order_by == 'desc') {
				$order_by = "a.time_created desc";
			}

			$where = array();

			if ($entity_guid != 0 && !is_array($entity_guid)) {
				$where[] = "a.entity_guid=$entity_guid";
			} else if (is_array($entity_guid)) {
				$where[] = "a.entity_guid in (". implode(",",$entity_guid) . ")";
			}

			if ($entity_type != "") {
				$where[] = "e.type='$entity_type'";
			}

			if ($entity_subtype != "") {
				$where[] = "e.subtype='$entity_subtype'";
			}

			if ($owner_guid != 0 && !is_array($owner_guid)) {
				$where[] = "a.owner_guid=$owner_guid";
			} else {
				if (is_array($owner_guid)) {
					$where[] = "a.owner_guid in (" . implode(",",$owner_guid) . ")";
				}
			}

			if ($entity_owner_guid != 0 && !is_array($entity_owner_guid)) {
				$where[] = "e.owner_guid=$entity_owner_guid";
			} else {
				if (is_array($entity_owner_guid)) {
					$where[] = "e.owner_guid in (" . implode(",",$entity_owner_guid) . ")";
				}
			}

			if (isadminloggedin() || ((is_array($entity_owner_guid) && in_array(get_loggedin_userid(), $entity_owner_guid)) || $entity_owner_guid == get_loggedin_userid())) {
				$where[] = "a.name_id in (" . get_metastring_id('generic_comment') . "," . get_metastring_id('generic_comment_awaiting') . ")";
			} else {
				$where[] = "(a.name_id=" . get_metastring_id('generic_comment') . " or (a.name_id=" . get_metastring_id('generic_comment_awaiting') . " and a.owner_guid=" . get_loggedin_userid() . "))";
			}

			if ($value != "") {
				$where[] = "a.value_id='$value'";
			}

			if ($timelower) {
				$where[] = "a.time_created >= {$timelower}";
			}

			if ($timeupper) {
				$where[] = "a.time_created <= {$timeupper}";
			}

			$query = "SELECT a.*, n.string as name, v.string as value
				FROM {$CONFIG->dbprefix}annotations a
				JOIN {$CONFIG->dbprefix}entities e on a.entity_guid = e.guid
				JOIN {$CONFIG->dbprefix}metastrings v on a.value_id=v.id
				JOIN {$CONFIG->dbprefix}metastrings n on a.name_id = n.id where ";

			foreach ($where as $w) {
				$query .= " $w and ";
			}
			$query .= get_access_sql_suffix("a"); // Add access controls
			$query .= " order by $order_by limit $offset,$limit"; // Add order and limit

			return get_data($query, "row_to_elggannotation");
		}


		function count_comments_awaiting($entity_guid, $entity_type = "", $entity_subtype = "", $value = "", $value_type = "", $owner_guid = 0, $timelower = 0, $timeupper = 0, $entity_owner_guid = 0) {
			global $CONFIG;

			$name = "generic_comment_awaiting";
			$sum = "count";

			$sum = sanitise_string($sum);
			$entity_guid = (int)$entity_guid;
			$entity_type = sanitise_string($entity_type);
			$timeupper = (int)$timeupper;
			$timelower = (int)$timelower;
			$entity_subtype = get_subtype_id($entity_type, $entity_subtype);
			if ($name != '' AND !$name = get_metastring_id($name)) {
				return 0;
			}

			if ($value != '' AND !$value = get_metastring_id($value)) {
				return 0;
			}
			$value_type = sanitise_string($value_type);
			$owner_guid = (int)$owner_guid;

			if (is_array($entity_owner_guid)) {
				if (sizeof($entity_owner_guid) > 0) {
					foreach($entity_owner_guid as $key => $val) {
						$entity_owner_guid[$key] = (int) $val;
					}
				} else {
					$entity_owner_guid = 0;
				}
			} else {
				$entity_owner_guid = (int)$entity_owner_guid;
			}

			// if (empty($name)) return 0;

			$where = array();

			if ($entity_guid) {
				$where[] = "e.guid=$entity_guid";
			}

			if ($entity_type!="") {
				$where[] = "e.type='$entity_type'";
			}

			if ($entity_subtype) {
				$where[] = "e.subtype=$entity_subtype";
			}

			if ($entity_owner_guid != 0 && !is_array($entity_owner_guid)) {
				$where[] = "e.owner_guid=$entity_owner_guid";
			} else {
				if (is_array($entity_owner_guid)) {
					$where[] = "e.owner_guid in (" . implode(",",$entity_owner_guid) . ")";
				}
			}

			if ($name!="") {
				$where[] = "a.name_id='$name'";
			}

			if ($value!="") {
				$where[] = "a.value_id='$value'";
			}

			if ($value_type!="") {
				$where[] = "a.value_type='$value_type'";
			}

			if ($owner_guid) {
				$where[] = "a.owner_guid='$owner_guid'";
			}

			if ($timelower) {
				$where[] = "a.time_created >= {$timelower}";
			}

			if ($timeupper) {
				$where[] = "a.time_created <= {$timeupper}";
			}

			if ($sum != "count") {
				$where[] = "a.value_type='integer'"; // Limit on integer types
			}

			$query = "SELECT $sum(ms.string) as sum
				FROM {$CONFIG->dbprefix}annotations a
				JOIN {$CONFIG->dbprefix}entities e on a.entity_guid = e.guid
				JOIN {$CONFIG->dbprefix}metastrings ms on a.value_id=ms.id WHERE ";

			foreach ($where as $w) {
				$query .= " $w and ";
			}

			$query .= get_access_sql_suffix("a"); // now add access
			$query .= ' and ' . get_access_sql_suffix("e"); // now add access

			$row = get_data_row($query);
			if ($row) {
				return $row->sum;
			}

			return FALSE;
		}


		function is_subtype_comments_moderated($subtype, $user_guid = 0) {
			if (is_numeric($subtype)) {
				$subtype = get_subtype_from_id($subtype);
			}

		// If users can change the plugin settings use the user custom settings
			if (get_plugin_setting('allow_custom_settings', 'commentmoderation') != "no") {
				if (get_plugin_usersetting("moderate_subtype_{$subtype}", $user_guid, 'commentmoderation') != "no") {
					return TRUE;
				}
			} else {
		// Use the global settings
				if (get_plugin_setting("moderate_subtype_{$subtype}", 'commentmoderation') != "no") {
					return TRUE;
				}
			}

			return FALSE;
		}


	// Make sure the commentmoderation initialisation function is called on initialisation
		register_elgg_event_handler('init', 'system', 'commentmoderation_init');
		register_elgg_event_handler('pagesetup', 'system', 'commentmoderation_pagesetup');

	// Register actions
		global $CONFIG;
		register_action("comments/approve", FALSE, $CONFIG->pluginspath . "commentmoderation/actions/approve.php");

?>

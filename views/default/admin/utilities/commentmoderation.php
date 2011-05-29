<?php
/**
 * Elgg Reported content admin page
 *
 * @package ElggReportedContent
 */

$options = array(
	'annotation_name' => 'generic_comment_awaiting'
);
$list = elgg_list_annotations($options);
if (!$list) {
	$list = '<p class="mtm">' . elgg_echo('commentmoderation:no_comments_awaiting') . '</p>';
}

echo $list;

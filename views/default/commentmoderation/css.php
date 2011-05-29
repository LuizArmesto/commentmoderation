<?php

	/**
	 * Elgg comment moderation: css
	 *
	 * @package ElggCommentModeration
	 */

?>

.generic_comment_awaiting {
	background: #efefef !important;
}

.comments_awaiting_bar {
	background: none repeat scroll 0 0 white;
	border-radius: 8px 8px 8px 8px;
	margin: 0 10px 10px;
	padding: 10px;
}

.generic_comment_info {
	font-style: italic;
}

#elgg_topbar_container_left a.commentmoderation {
	background:transparent url(<?php echo $vars['url']; ?>mod/commentmoderation/graphics/toolbar_comments_icon.gif) no-repeat left 2px;
	padding:0 0 4px 16px;
	margin:0 15px 0 5px;
	cursor:pointer;
}
#elgg_topbar_container_left a.commentmoderation:hover {
	text-decoration: none;
	background:transparent url(<?php echo $vars['url']; ?>mod/commentmoderation/graphics/toolbar_comments_icon.gif) no-repeat left -34px;
}
#elgg_topbar_container_left a.commentmoderation_new {
	background:transparent url(<?php echo $vars['url']; ?>mod/commentmoderation/graphics/toolbar_comments_icon.gif) no-repeat left -16px;
	padding:0 0 4px 18px;
	margin:0 15px 0 5px;
	color:white;
}
/* IE6 */
* html #elgg_topbar_container_left a.commentmoderation_new { background-position: left -18px; }
/* IE7 */
*+html #elgg_topbar_container_left a.commentmoderation_new { background-position: left -18px; }

#elgg_topbar_container_left a.commentmoderation_new:hover {
	text-decoration: none;
}

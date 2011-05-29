<?php

	/**
	 * Elgg generic comment awaiting
	 *
	 * @package ElggCommentModeration
	 */

	 //need to be logged in to moderate comments
	 gatekeeper();

	//get awaiting comments
	$num_comments_awaiting = count_comments_awaiting(0, "", "", "", "", 0, 0, 0, get_loggedin_userid());
	if($num_comments_awaiting){
		$num = $num_comments_awaiting;
	} else {
		$num = 0;
	}

	if($num == 0){

?>

<a href="<?php echo $vars['url']; ?>pg/commentmoderation/owner/<?php echo get_loggedin_user()->username; ?>" class="commentmoderation" >&nbsp;</a>

<?php
    }else{
?>

<a href="<?php echo $vars['url']; ?>pg/commentmoderation/owner/<?php echo get_loggedin_user()->username; ?>" class="commentmoderation_new" >[<?php echo $num; ?>]</a>

<?php
    }
?>

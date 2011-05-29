<?php

/**
 * Comment Moderation
 */

?>

<p>
<?php
if (elgg_get_plugin_setting('allow_custom_settings', 'commentmoderation') == "yes") {
	echo "<label>" . elgg_echo('commentmoderation:moderate_subtypes') . "</label>";
	$subtypes = get_registered_entity_types('object');
	echo "<br />";
	foreach ($subtypes as $subtype) {
		$setting = "moderate_subtype_{$subtype}";
		echo "<label>" . elgg_echo($subtype) . "</label>";
		echo "<br />";
		echo elgg_view("input/pulldown",
		 array(
		  'value' => $vars['entity']->$setting,
		  'internalname' => "params[$setting]",
		  'options_values' => array (
		   "yes" => elgg_echo('option:yes'),
		   "no"  => elgg_echo('option:no')
		  )
		 )
		);
		echo "<br />";
	}
} else {
	echo elgg_echo('commentmoderation:custom_settings_not_allowed');
}
?>

</p>

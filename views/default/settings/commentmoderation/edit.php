<?php

	/**
	 * @package ElggCommentModeration
	 */

?>

<p>
<?php
	echo "<label>" . elgg_echo('commentmoderation:allow_custom_settings') . "</label>";
	echo elgg_view("input/pulldown",
	 array(
      'value' => $vars['entity']->allow_custom_settings,
	  'internalname' => 'params[allow_custom_settings]',
	  'options_values' => array (
       "yes" => elgg_echo('option:yes'),
	   "no"  => elgg_echo('option:no')
	  )
	 )
	);
?>
</p>
<br />
<p>
<?php
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
?>
</p>

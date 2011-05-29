<?php

    /**
     * Content debugger plugin settings
     *
     * @package content_debugger
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Andras Szepeshazi
     * @copyright Andras Szepeshazi
     * @link http://wamped.org
     */

?>

<p>
<?php
	echo "<label>" . elgg_echo('commentmoderation:allow_custom_settings') . "</label>";
	echo elgg_view("input/dropdown",
	 array(
      'value' => $vars['entity']->allow_custom_settings,
	  'name' => 'params[allow_custom_settings]',
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
		echo elgg_view("input/dropdown",
		 array(
		  'value' => $vars['entity']->$setting,
		  'name' => "params[$setting]",
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

<?php

$selected = ( $selected ? 'selected="selected"' : '' );

echo <<<DOCHERE
<option value="{$term_id}" {$selected}>{$label}</option>

DOCHERE;


/* EOF */

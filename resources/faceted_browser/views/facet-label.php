<?php

if ( $selected ) {

  $label = <<<DOCHERE
{$label} <a href="{$base_url}?{$query_string}" class="remove_link" title="Remove facet">X</a>

DOCHERE;

}
// if

echo <<<DOCHERE
<span class="label">$label</span>
DOCHERE;


/* EOF */
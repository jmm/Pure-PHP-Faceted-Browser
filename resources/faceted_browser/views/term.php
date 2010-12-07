<?php

$class = ( $selected ? ' selected' : '' );

echo <<<DOCHERE
<div class="term{$class}">
{$label}
</div>

DOCHERE;


/* EOF */
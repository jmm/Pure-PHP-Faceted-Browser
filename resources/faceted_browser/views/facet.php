<?php

echo <<<DOCHERE
<div class="facet simple" id="facet_{$facet_id}">

<h1 class="label">
{$facet_label}
</h1>

<div class="terms">

{$term_items}

</div>
<!-- .terms -->

</div>
<!-- #facet_{$facet_id} -->

DOCHERE;


/* EOF */
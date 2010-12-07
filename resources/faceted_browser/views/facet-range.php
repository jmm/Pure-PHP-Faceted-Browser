<?php

echo <<<DOCHERE
<div class="facet range" id="facet_{$facet_id}">

<h1 class="label">
{$facet_label}
</h1>

<form method="get" action="{$base_url}">

<div class="term_sets">

DOCHERE;


foreach ( $terms as $term_set_id => $term_set_terms ) {

  $default_label = ucfirst( $term_set_id );

  echo <<<DOCHERE

<div class="term_set {$term_set_id}">

<select name="{$facet_request_key}[{$facet_id}][{$term_set_id}]" class="terms {$term_set_id}">

<option value="" class="term">{$default_label}</option>

{$term_set_terms}

</select> {$default_label}

</div>
<!-- .term_set.{$term_set_id} -->

DOCHERE;

}
// foreach


echo <<<DOCHERE

</div>
<!-- .terms_sets -->


<div class="form_controls">

<input type="submit" value="Go" />

</div>


{$request_persist_inputs}

</form>

</div>
<!-- #facet_{$facet_id} -->

DOCHERE;


/* EOF */
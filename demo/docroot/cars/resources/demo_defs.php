<?php

$demo_defs = array(

  'default' => array(

    'label' => "Default Configuration",

    'description' => <<<DOCHERE

<p>
This is the default demo configuration.
</p>

DOCHERE

  )
  // array

  ,

  'multi-match-any' => array(

    'label' => "Multiple Matches",

    'description' => <<<DOCHERE

<p>
In this demo configuration the faceting engine is configured to allow multiple terms to be selected for the Make facet, and return records that match any of those terms.  Contrast with the default demo configuration where the faceting engine will return records that match only one term at a time for the Make facet.
</p>

DOCHERE

  )
  // array

  ,

  'populate-dynamically' => array(

    'label' => "Dynamic Term Population",

    'description' => <<<DOCHERE

<p>
In this demo configuration the faceting engine is configured to dynamically extract terms for the MPG facet from the record set.  Contrast with the default demo configuration where the terms for the MPG facet are set to arbitrary values that the data from the record set is grouped into.
</p>

DOCHERE

  )
  // array

);


switch ( $demo_id ) {

  case 'multi-match-any':

    $facet_defs[ 'make' ][ 'multiple_match_mode' ] = 'any';

  break;
  // 'multi-match-any'


  case 'populate-dynamically':

    $facet_defs[ 'mpg' ][ 'terms' ] = array( 'min' => array(), 'max' => array() );

    $facet_defs[ 'mpg' ][ 'pre_defined_terms' ] = FALSE;

    $facet_defs[ 'mpg' ][ 'populate_dynamically' ] = TRUE;

  break;
  // 'populate-dynamically'


  default:

    $demo_id = 'default';

  break;
  // default

}
// switch


$demo_description = $demo_descriptions[ $demo_id ];


/* EOF */

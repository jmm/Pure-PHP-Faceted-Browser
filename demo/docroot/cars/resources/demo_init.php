<?php

$base_url = explode( "?", $_SERVER[ 'REQUEST_URI' ] );

$base_url = $base_url[0];

$demo_dir_url = dirname( $_SERVER[ 'SCRIPT_NAME' ] );

require_once ( "{$_SERVER[ 'MAIN_LIB_PATH' ]}/php/faceted_browser/faceted_browser.php" );

require_once ( "{$_SERVER[ 'MAIN_LIB_PATH' ]}/php/faceted_browser/array_faceted_browser.php" );

require_once "{$demo_resources_path}/term_label_formatters.php";

require_once "{$demo_resources_path}/facet_defs.php";

$facet_defs = get_facet_defs();


if ( $demo_id = trim( $_SERVER[ 'PATH_INFO' ], "/" ) ) {

  include "{$demo_resources_path}/demo_defs.php";

  $demo_def = $demo_defs[ $demo_id ];

}
// if


$records = file_get_contents( "{$demo_resources_path}/records.json" );

$records = json_decode( $records, TRUE );


$faceter = new Array_Faceted_Browser(

  'cars',

  $facet_defs,

  $_SERVER[ 'QUERY_STRING' ],

  array(

    'facet_key' => 'f',

    'base_url' => $base_url

  )

);


$faceter->set_records( $records );

$faceted_records = $faceter->get_faceted_records( TRUE, TRUE );

$facet_nav = $faceter->generate_ui();


/* EOF */

<?php

header( "Content-Type: text/plain" );

require "cars_data.php";


$records = array();


for ( $index = 1 ; $index <= 60 ; ++$index ) {

  $record = array( 'id' => $index );


  foreach ( array( 'make', 'model', 'price', 'year', 'mpg', 'mileage', 'color', 'form_factor' ) as $param ) {

    if ( array_key_exists( 'min', $cars_data[ $param ] ) ) {

      $record[ $param ] = mt_rand( $cars_data[ $param ][ 'min' ], $cars_data[ $param ][ 'max' ] );

    }
    // if


    else {

      $key = array_rand( $cars_data[ $param ] );

      $id = ( array_search( $key, array_keys( $cars_data[ $param ] ) ) + 1 );

      $record[ "{$param}_id" ] = $id;

      $record[ $param ] = $cars_data[ $param ][ $key ];

    }
    // else

  }
  // foreach


  $record[ 'model_id' ] = "{$record[ 'make_id' ]}{$record[ 'model_id' ]}";


  $records[ $index ] = $record;

}
// for


// var_dump( $records );  echo "\n\n";


echo json_encode( $records );


/* EOF */

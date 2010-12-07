<?php

function get_facet_defs() {

  $facet_defs = array(

    'year' => array(

      'id' => 'year',

      'label' => "Year",

      'data_type' => 'integer',

      'data_field' => 'year',

      'label_field' => 'year',

      'terms' => array(),

      'display_empty' => FALSE,

      'display_redundant_terms' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'range'

    ),
    // 'year'


    'make' => array(

      'label' => "Make",

      'data_type' => 'string',

      'data_field' => 'make',

      'label_field' => 'make',

      'terms' => array(),

      'display_empty' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'string'

    ),
    // 'make'


    'model' => array(

      'label' => "Model",

      'data_type' => 'string',

      'data_field' => 'model',

      'label_field' => 'model',

      'terms' => array(),

      'display_empty' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'string',

      'populate_dynamically' => FALSE

    ),
    // 'model'


    'form_factor' => array(

      'label' => "Style",

      'data_type' => 'string',

      'data_field' => 'form_factor',

      'label_field' => 'form_factor',

      'terms' => array(),

      'display_empty' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'string'

    ),
    // 'form_factor'


    'mileage' => array(

      'label' => "Mileage",

      'data_type' => 'integer',

      'data_field' => 'mileage',

      'label_field' => 'mileage',

      'terms' => array(),

      'display_empty' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'range',

      'pre_defined_terms' => TRUE,

      'label_format_callback' => array( 'Term_Label_Formatters', 'thousands' )

    ),
    // 'mileage'


    'color' => array(

      'label' => "Color",

      'data_type' => 'string',

      'data_field' => 'color',

      'label_field' => 'color',

      'terms' => array(),

      'display_empty' => TRUE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'string'

    ),
    // 'color'


    'mpg' => array(

      'label' => "MPG",

      'data_type' => 'integer',

      'data_field' => 'mpg',

      'label_field' => 'mpg',

      'terms' => array(),

      'display_empty' => FALSE,

      'display_redundant_terms' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'range',

      'pre_defined_terms' => TRUE

    ),
    // 'mpg'


    'price' => array(

      'label' => "Price",

      'data_type' => 'integer',

      'data_field' => 'price',

      'label_field' => 'price',

      'terms' => array(),

      'display_empty' => FALSE,

      'multiple_match_mode' => 'none',

      'facet_type' => 'range',

      'pre_defined_terms' => TRUE,

      'label_format_callback' => array( 'Term_Label_Formatters', 'thousands' )

    )
    // 'price'

  );
  // array


  require ( dirname( __FILE__ ) . "/cars_data.php" );


  /* start compile term values */

  $terms = array();


  $terms[ 'make' ][ 'all' ] = $cars_data[ 'makes' ];


  $terms[ 'model' ][ 'all' ] = array();

  foreach ( $cars_data[ 'models' ] as $make => $models ) {

    $make_id = ( array_search( $make, $cars_data[ 'makes' ] ) + 1 );

    foreach ( $models as $model_id => $model ) {

      $terms[ 'model' ][ 'all' ][ "{$make_id}{$model_id}" ] = $model;

    }
    // foreach

  }
  // foreach


  $terms[ 'year' ][ 'min' ] = range( $cars_data[ 'year' ][ 'min' ], $cars_data[ 'year' ][ 'max' ] );

  $terms[ 'year' ][ 'max' ] = $terms[ 'year' ][ 'min' ];


  $terms[ 'form_factor' ][ 'all' ] = $cars_data[ 'form_factor' ];


  $terms[ 'mileage' ][ 'min' ] = range( $cars_data[ 'mileage' ][ 'min' ], $cars_data[ 'mileage' ][ 'max' ], 25000 );

  $terms[ 'mileage' ][ 'max' ] = $terms[ 'mileage' ][ 'min' ];


  $terms[ 'mpg' ][ 'min' ] = range( $cars_data[ 'mpg' ][ 'min' ], $cars_data[ 'mpg' ][ 'max' ], 5 );

  $terms[ 'mpg' ][ 'max' ] = $terms[ 'mpg' ][ 'min' ];


  /* Set a $increment and generate term values every $increment until $max_price */

  $max_price = $cars_data[ 'price' ][ 'max' ];

  $terms[ 'price' ][ 'min' ] = array( 0, 2500, 5000, 7500, 10000 );

  $last_price = end( $terms[ 'price' ][ 'min' ] );

  $increment = $last_price;

  while ( $last_price < $max_price ) {

    $last_price += $increment;

    $terms[ 'price' ][ 'min' ][] = $last_price;

  }
  // while


  $terms[ 'price' ][ 'max' ] = $terms[ 'price' ][ 'min' ];


  $terms[ 'color' ][ 'all' ] = $cars_data[ 'color' ];


  /* Process compiled term values into the format required for the term definitions */

  foreach ( $terms as $f_id => $term_sets ) {

    foreach ( $term_sets as $term_set_id => $values ) {

      $facet_defs[ $f_id ][ 'terms' ][ $term_set_id ] = array();


      if ( array_key_exists( 0, $values ) ) {

        // Start indexing at 1 like DB records

        array_unshift( $values, NULL );

        unset( $values[0] );

      }
      // if



      foreach ( $values as $id => $value ) {

        if (

          in_array( $term_set_id, array( 'min', 'max' ) ) ||

          $facet_defs[ $f_id ][ 'data_type' ] == 'string'

        ) {

          $id = $value;

        }
        // if

        $facet_defs[ $f_id ][ 'terms' ][ $term_set_id ][ $id ] = array(

          'id' => $id, 'label' => $value

        );

      }
      // foreach

    }
    // foreach

  }
  // foreach

  /* end compile term values */


  foreach ( $facet_defs as $f_id => $facet_def ) {

    $facet_defs[ $f_id ][ 'id' ] = $f_id;

  }
  // foreach


  return $facet_defs;

}
// get_facet_defs


/* EOF */

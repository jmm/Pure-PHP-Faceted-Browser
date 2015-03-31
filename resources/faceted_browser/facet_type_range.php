<?php

class Facet_Type_Range extends Facet {

  /**
   * Instantiate.
   *
   * @param $params Object properties.
   *
   * @return void
   *
   */

  public function __construct( $params ) {

    $params[ 'facet_type' ] = 'range';

    $params[ 'allowed_term_sets' ] = array( 'min', 'max' );


    parent::__construct( $params );


    return;

  }
  // __construct


  /**
   * Generate UI output representing this facet.
   *
   * @param obj $host_env The host environment.
   *
   * @param array $data Data and content needed to generate the output.
   *
   * @return string
   *
   */

  public function generate_ui( $data ) {

    $output = "";

    $host_env = $this->host_env();


    // Store the UI output that represents the min / max options.

    $term_items = array();


    // $range_point is min or max.

    foreach ( $this->get_terms() as $range_point => $terms ) {

      foreach ( $terms as $t_id => $term ) {

        // Omit terms that are empty and not specified in the request.

        if ( ! $term->visible() ) {

          continue;

        }
        // if


        $term_items[ $range_point ][ $t_id ] = $term->generate_ui();

      }
      // foreach


      if ( $term_items[ $range_point ] ) {

        $term_items[ $range_point ] = join( "\n\n", $term_items[ $range_point ] );

      }
      // if

    }
    // foreach


    if ( $this->selected() ) {

      $data[ 'facet_label' ] = $host_env->gen_nav_frag( 'facet-label', array(

        'label' => $data[ 'facet_label' ],

        'base_url' => $host_env->base_url(),

        'query_string' => $this->query_string(),

        'selected' => $this->selected()

      ) );

    }
    // if


    $output = $host_env->gen_nav_frag( 'facet-range', array(

      'facet_id' => $this->id(),

      'facet_label' => $data[ 'facet_label' ],

      'base_url' => $host_env->base_url(),

      'terms' => $term_items,

      'facet_request_key' => $data[ 'facet_request_key' ],

      'request_persist_inputs' => $host_env->get_request_persist_output( $this )

    ) );


    return $output;

  }
  // generate_ui


  /**
   * Count the number of records matching each term, cumulatively.
   *
   * @return int
   *
   */

  public function count_term_matches() {

    $range_points = array(

      'min' => array_reverse( $this->get_terms( 'min' ), TRUE ),

      'max' => $this->get_terms( 'max' )

    );
    // array


    /*

    To do the range counts, each step in min and max has to be processed, in DESC order for min, and ASC order for max, to get cumulative totals.  For example, any data that matches a min term value of 100 will also match any greater min term value, e.g. 200.  So the count for the 100 term needs to include the count for all greater terms as well as the count that only matches the 100 term, but not the next greater term.

    */

    foreach ( $range_points as $range_point => $current_terms ) {

      $previous_term = NULL;

      foreach ( $current_terms as $t_id => $term ) {

        $term->accumulate_count( $previous_term );

        $previous_term = $term;

      }
      // foreach

    }
    // foreach


    return;

  }
  // count_term_matches


  // For a range facet, its necessary to unset the non-current range point.  That allows a count to be made that show how many records will be returned for each term value in this range point in addition to the other selected facet values and the value selected for the other range point.

  /**
   * Get criteria that excludes one of the range points ( min | max ) of this facet.
   *
   * @param string $range_point The current range point.
   *
   * @return mixed
   *
   */

  public function get_alt_records( $range_point, $records, $faceted_records, $callback ) {

    $other_range_point = ( ( $range_point == 'min' ) ? 'max' : 'min' );

    $criteria = $this->get_criteria();


    if ( ! $this->get_terms( $range_point ) ) {

      $alt_records = array();

    }
    // if


    elseif (

      strlen( $criteria[ $range_point ] ) &&

      (

        count( $callback[ 'args' ][ 'criteria' ] ) > 1

        ||

        strlen( $criteria[ $other_range_point ] )

      )

    ) {

      $alt_criteria = clone $this;

      $alt_criteria->set_criteria_item( $range_point, NULL, TRUE );

      $callback[ 'args' ][ 'criteria' ][ $this->id() ] = $alt_criteria;

      $alt_records = call_user_func_array( $callback[ 'callback' ], $callback[ 'args' ] );

    }
    // elseif


    elseif ( strlen( $criteria[ $other_range_point ] ) ) {

      $alt_records = $faceted_records;

    }
    // elseif


    else {

      $alt_records = $records;

    }
    // else


    return $alt_records;

  }
  // get_alt_records


  /**
   * Set criteria.
   *
   * @param mixed $criteria New criteria.
   *
   * @return void
   *
   */

  public function set_criteria( $criteria ) {

    $criteria = (array) $criteria;


    foreach ( $criteria as $rp_key => $rp_value ) {

      if ( ! in_array( $rp_key, $this->allowed_term_sets ) ) {

        unset( $criteria[ $rp_key ] );

        continue;

      }
      // if


      elseif ( is_array( $rp_value ) ) {

        $criteria[ $rp_key ] = end( $rp_value );

      }
      // elseif


      if ( ! strlen( $rp_value ) ) {

        unset( $criteria[ $rp_key ] );

      }
      // if

    }
    // foreach


    /*

    Make sure that max, if specified, is >= min.

    */

    if (

      strlen( $criteria[ 'max' ] ) &&

      strlen( $criteria[ 'min' ] ) &&

      $criteria[ 'max' ] < $criteria[ 'min' ]

    ) {

      $criteria[ 'max' ] = $criteria[ 'min' ];

    }
    // if


    $this->criteria = $criteria;


    return;

  }
  // set_criteria

}
// Facet_Type_Range


/* EOF */

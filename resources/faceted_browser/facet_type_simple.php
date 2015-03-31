<?php

class Facet_Type_Simple extends Facet {

  /**
   * Instantiate.
   *
   * @param array $params Object properties.
   *
   * @return void
   *
   */

  public function __construct( $params ) {

    $params[ 'allowed_term_sets' ] = array( 'all' );


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


    $term_items = array();

    foreach ( $this->get_terms( 'all' ) as $t_id => $term ) {

      if ( ! $term->visible() ) {

        continue;

      }
      // if


      $term_items[ $t_id ] = $term->generate_ui();

    }
    // foreach


    $term_items = join( "\n\n", $term_items );



    $facet_label = $data[ 'facet_label' ];


    if ( $this->selected() ) {

      $facet_label = $host_env->gen_nav_frag( 'facet-label', array(

        'label' => $facet_label,

        'selected' => $this->selected(),

        'base_url' => $host_env->base_url(),

        'query_string' => $this->query_string()

      ) );

    }
    // if


    $output = $host_env->gen_nav_frag( 'facet', array(

      'facet_id' => $this->id(),

      'facet_label' => $facet_label,

      'term_items' => $term_items

    ) );


    return $output;

  }
  // generate_ui


  /**
   * Get records that exclude this facets criteria.
   *
   * @param string $term_set_id Identifies the relevant term set.
   *
   * @param array $records All records, before filtering.
   *
   * @param array $faceted_records Filtered records matching all requested criteria.
   *
   * @param callback $callback Callback method to retrieve records with alternate criteria.
   *
   * @return array
   *
   */

  public function get_alt_records( $term_set_id, $records, $faceted_records, $callback ) {

    if ( in_array( $this->multiple_match_mode(), array( 'none', 'any' ) ) ) {

      unset( $callback[ 'args' ][ 'criteria' ][ $this->id() ] );

      if ( $callback[ 'args' ][ 'criteria' ] ) {

        $alt_records = call_user_func_array( $callback[ 'callback' ], $callback[ 'args' ] );

      }
      // if

    }
    // if


    elseif ( $this->multiple_match_mode() == 'all' ) {

      $alt_records = $faceted_records;

    }
    // elseif


    if ( ! is_array( $alt_records ) ) {

      $alt_records = $records;

    }
    // if


    return $alt_records;

  }
  // get_alt_records


  /**
   * Set $this->criteria.
   *
   * @param mixed $criteria New value.
   *
   * @return void
   *
   */

  function set_criteria( $criteria ) {

    /*

    If the facet does not allow multiple matches, but multiple values were specified in the request, then use only the last specified value.

    */

    if (

      $this->multiple_match_mode() == 'none' &&

      is_array( $criteria )

    ) {

      $criteria = end( $criteria );

    }
    // if


    elseif ( ! (

      is_array( $criteria ) && count( $criteria ) ||

      ! is_array( $criteria ) && strlen( $criteria )

    ) ) {

      $criteria = array();

    }
    // elseif


    $this->criteria = $criteria;


    return;

  }
  // set_criteria

}
// Facet_Type_Simple


/* EOF */

<?php

class Term_Type_Range extends Term {

  /** @param array Records matching the current term and greater min or lesser max terms. */

  protected $cumulative_records = array();


  /**
   * Add the records matched by the greater min or lesser max terms to the current term.
   *
   * @param NULL|obj $previous_term NULL or an object of this class (the "previous" term).
   *
   * @return void
   *
   */

  public function accumulate_count( $previous_term ) {

    if ( $previous_term instanceof $this ) {

      $this->cumulative_records = ( $previous_term->cumulative_records() + $this->records );

    }
    // if

    else {

      $this->cumulative_records = $this->records;

    }
    // else


    return;

  }
  // accumulate_count


  /**
   * Return the cumulative count.
   *
   * @return int
   *
   */

  public function cumulative_count() {

    return count( $this->cumulative_records );

  }
  // cumulative_count


  /**
   * Return the cumulative records matching this term.
   *
   * @return array
   */

  protected function cumulative_records() {

    return $this->cumulative_records;

  }
  // cumulative_records


  /**
   * Generate data necessary to generate the UI output for a term.
   *
   * @param array $request_params The request parameters.
   *
   * @return void
   *
   */

  function generate_ui_data( $request_params ) {

    $host_env = $this->host_env();

    $facet = $this->get_facet();

    $term_set_id = $this->get_term_set_id();

    $requested = ( (string) $request_params[ $host_env->facet_key() ][ $facet->id() ][ $term_set_id ] == (string) $this->id() );


    if ( $requested || $this->count() ) {

      // The term was specified in the request

      if ( $requested ) {

        // Remove the current term value from the new request

        unset( $request_params[ $host_env->facet_key() ][ $facet->id() ][ $term_set_id ] );


        // Indicate the terms selected status

        $this->selected( TRUE );

      }
      // if


      // The term wasnt specified in the request

      else {

        // Add the current term value to the new request

        $request_params[ $host_env->facet_key() ][ $facet->id() ][ $term_set_id ] = $this->id();

      }
      // else


      // Build the new query string.  It will either add or subtract the current term.

      $this->query_string( http_build_query( $request_params, '', $host_env->param_delim() ) );

      $this->visible( TRUE );

    }
    // if


    // The current term is empty or redundant, and configured to display.

    elseif (

      $facet->display_empty() && ! $this->cumulative_count()

      ||

      $facet->display_redundant_terms() && $this->cumulative_count()

    ) {

      $this->visible( TRUE );

    }
    // elseif


    return;

  }
  // generate_ui_data


  /**
   * Generate and return UI for the term.
   *
   * @return string
   */

  public function generate_ui() {

    $host_env = $this->host_env();


    $label = $host_env->gen_nav_frag( 'term-range-label', array(

      'label' => htmlspecialchars( $this->label( TRUE ) ),

      'count' => htmlspecialchars( $this->cumulative_count() )

    ) );


    $selected = $this->selected();


    $output = $host_env->gen_nav_frag( 'term-range-point', array(

      'term_id' => htmlspecialchars( $this->id() ),

      'selected' => $selected,

      'label' => $label

    ) );


    return $output;

  }
  // generate_ui

}
// Term_Type_Range


/* EOF */

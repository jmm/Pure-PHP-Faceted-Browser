<?php

class Array_Faceted_Browser extends Faceted_Browser {

  protected $records;

  protected $faceted_records;


  /**
   * Set the original record set.
   *
   * @param array $records Records to filter.
   *
   * @return void
   *
   */

  public function set_records( $records ) {

    if ( is_array( $records ) ) {

      $this->records = $records;

    }
    // if


    return;

  }
  // set_records


  /**
   * Get the filtered records.
   *
   * @param bool $count_matches Whether to count the number of term matches.
   *
   * @param bool $populate_terms_dynamically Whether to dynamically populate terms from $records.
   *
   * @return array
   *
   */

  public function get_faceted_records( $count_matches = TRUE, $populate_terms_dynamically = FALSE ) {

    $filtered_records = $this->apply_facets(

      $this->records,

      $this->criteria,

      $count_matches,

      $populate_terms_dynamically

    );


    return $filtered_records;

  }
  // get_faceted_records


  /**
   * Populate terms dynamically from $record.
   *
   * @param array $record A single record from $this->records.
   *
   * @return bool Indicate whether to continue populating terms dynamically.  If not facets are configured to do it, then stop doing unnecessary processing.
   *
   */

  function populate_terms_dynamically( $record ) {

    $populate_dynamically = FALSE;


    foreach ( $this->facet_defs as $f_id => $facet_def ) {

      if ( ! $facet_def->populate_dynamically() ) {

        continue;

      }
      // if


      $populate_dynamically = TRUE;


      $column = $this->extract_record_column( $facet_def, $record );


      foreach ( $facet_def->get_terms() as $term_set => $terms ) {

        foreach ( $column as $value ) {

          if (

            strlen( $value ) &&

            ( ! array_key_exists( $value, $terms ) )

          ) {

            $facet_def->set_terms(

              $term_set,

              $value,

              $this->term_factory(

                array(

                  'id' => $value,

                  'label' => $value

                ),

                array( 'facet_type' => $facet_def->facet_type() )

              )

            );

          }
          // if

        }
        // foreach

      }
      // foreach

    }
    // foreach


    return $populate_dynamically;

  }
  // populate_terms_dynamically


  /**
   * Filter $records according to $criteria.
   *
   * @param array $records
   *
   * @param array $criteria Facet definitions with requested criteria.
   *
   * @param bool $count_matches Whether to count the number of term matches.
   *
   * @param bool $populate_terms_dynamically Whether to dynamically populate terms from $records.
   *
   * @return array
   *
   */

  public function apply_facets(

    $records,

    $criteria,

    $count_matches = TRUE,

    $populate_terms_dynamically = FALSE

  ) {

    $faceted_records = array();


    if ( $criteria || $populate_terms_dynamically ) {

      /*

      Process each record to determine whether it is included or excluded by $criteria, and / or to dynamically populate terms.

      */

      foreach ( $records as $r_id => $record ) {

        if ( $populate_terms_dynamically ) {

          $populate_terms_dynamically = $this->populate_terms_dynamically( $record );

        }
        // if


        if ( ! $criteria ) {

          continue;

        }
        // if


        /* Process each element of criteria until $record fails to satisfy one. */

        foreach ( $criteria as $f_id => $facet_def ) {

          if ( ! ( $matched = $facet_def->match_criteria(

            $record,

            $this->extract_record_column( $facet_def, $record )

          ) ) ) {

            break;

          }
          // if

        }
        // foreach


        // $record matched all criteria, include it in the results.

        if ( $matched ) {

          $faceted_records[ $r_id ] = $record;

        }
        // if

      }
      // foreach


      // Sort dynamically populated terms

      if ( $populate_terms_dynamically ) {

        foreach ( $this->facet_defs as $f_id => $facet_def ) {

          if ( $facet_def->facet_type() == 'range' && $facet_def->populate_dynamically() ) {

            $terms = $facet_def->get_terms();

            foreach ( $terms as $term_set_id => $term_set_terms ) {

              asort( $term_set_terms );

              $terms[ $term_set_id ] = $term_set_terms;

            }
            // foreach


            $facet_def->set_terms( NULL, NULL, $terms );

          }
          // if

        }
        // foreach

      }
      // if

    }
    // if


    if ( ! $criteria ) {

      $faceted_records = $records;

    }
    // else


    if ( is_null( $this->faceted_records ) ) {

      $this->faceted_records = $faceted_records;

    }
    // if


    if ( $count_matches ) {

      $this->count_matches();

    }
    // if


    return $faceted_records;

  }
  // apply_facets


  /**
   * Count the number of records matched by each term of each facet.
   *
   * @return void
   *
   */

  protected function count_matches() {

    $records = $this->records;

    $faceted_records = ( $this->faceted_records ? $this->faceted_records : $records );

    $facet_defs = $this->facet_defs;

    $criteria = $this->criteria;

    $alt_records_callback = array(

      'callback' => array( $this, 'apply_facets' ),

      'args' => array(

        'records' => $records,

        'criteria' => $criteria,

        'count_matches' => FALSE,

        'populate_terms_dynamically' => FALSE

      )

    );
    // array


    foreach ( $facet_defs as $f_id => $facet_def ) {

      foreach ( $facet_def->get_terms() as $term_set_id => $terms ) {

        $alt_records = $faceted_records;


        if ( $criteria[ $f_id ] ) {

          $alt_records = $criteria[ $f_id ]->get_alt_records(

            $term_set_id,

            $records,

            $faceted_records,

            $alt_records_callback

          );

        }
        // if


        foreach ( $alt_records as $record ) {

          // Process each element of $record[ $data_field[0] ]

          $column = $this->extract_record_column( $facet_def, $record );

          foreach ( $column as $data ) {

            $facet_def->match_record_to_terms( $term_set_id, $record, $data );

          }
          // foreach

        }
        // foreach

      }
      // foreach


      $facet_def->count_term_matches();

    }
    // foreach


    return;

  }
  // count_matches


  /**
   * Extract a nested column from $record.
   *
   * @param obj $facet_def The facet that corresponds to the desired data.
   *
   * @param array $record
   *
   * @return array
   *
   */

  protected function extract_record_column( $facet_def, $record ) {

    // Treat data_field as an array of keys.

    $data_field = (array) $facet_def->data_field();

    // Retrieve the element of $record that corresponds to the first key in data_field, and treat it as an array.

    $data_array = (array) $record[ array_shift( $data_field ) ];


    $column = self::extract_array_column( $data_field, $data_array );


    return $column;

  }
  // extract_record_column


  /**
   * Extract a nested column from $top_level_array.
   *
   * @param array $keys Series of keys that leads to the desired data in each element of $top_level_array.
   *
   * @return array
   *
   */

  protected function extract_array_column( $keys, $top_level_array ) {

    $column = array();


    foreach ( $top_level_array as $descendent ) {

        $data = $descendent;


        // Drill down into $descendent to get to the correct sub-element

        foreach ( $keys as $key ) {

          if ( ! array_key_exists( $key, $data ) ) {

            break;

          }
          // if

          $data =& $data[ $key ];

        }
        // foreach


        // Add the value of the sub-element to $column

        $column[] = $data;

    }
    // foreach


    return $column;

  }
  // extract_array_column


  public static function facet_factory( $facet_def, $config ) {

    require_once ( dirname( __FILE__ ) . "/facet.php" );


    $facet_type_keyword = (

      in_array( $facet_def[ 'facet_type' ], array( 'range' ) ) ?

      $facet_def[ 'facet_type' ] :

      'simple'

    );


    require_once ( dirname( __FILE__ ) . "/facet_type_{$facet_type_keyword}.php" );

    require_once ( dirname( __FILE__ ) . "/array_facet_type_{$facet_type_keyword}.php" );


    $facet_class = ucfirst( $facet_type_keyword );

    $facet_class = "Array_Facet_Type_{$facet_class}";


    $instance = new $facet_class( $facet_def );


    return $instance;

  }
  // facet_factory


  public static function term_factory( $term_def, $config ) {

    require_once ( dirname( __FILE__ ) . "/term.php" );


    $facet_type_keyword = (

      in_array( $config[ 'facet_type' ], array( 'range' ) ) ?

      $config[ 'facet_type' ] :

      'simple'

    );


    require_once ( dirname( __FILE__ ) . "/term_type_{$facet_type_keyword}.php" );


    $term_class = ucfirst( $facet_type_keyword );

    $term_class = "Term_Type_{$term_class}";


    $instance = new $term_class( $term_def[ 'id' ], $term_def[ 'label' ] );


    return $instance;

  }
  // term_factory

}
// Array_Faceted_Browser


/* EOF */

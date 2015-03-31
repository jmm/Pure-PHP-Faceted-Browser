<?php

class Facet {

  protected $host_env_id;

  /// @property int|string
  protected $id;

  /// @property string
  protected $label;

  /// @property string
  protected $data_type;

  /// @property string|array
  protected $data_field;

  /// @property string
  protected $label_field;

  /// @property array
  protected $terms;

  /// @property bool
  protected $display_empty;

  /// @property bool
  protected $display_redundant_terms;

  /**
   *
   * @property string
   *
   * @brief ( any | none | all ).  case-insensitive.
   *
   */

  protected $multiple_match_mode;

  /// @property string
  protected $facet_type;

  /// @property bool
  protected $pre_defined_terms;

  /// @property bool
  protected $populate_dynamically;

  /// @property callback
  protected $label_format_callback;

  /// @property array
  protected $criteria;

  /// @property array
  protected $allowed_term_sets;

  /// @property bool
  protected $selected;

  /// @property string
  protected $query_string;


  /**
   * Instantiate.
   *
   * @param array $params Object properties.
   *
   * @return void
   *
   */

  public function __construct( $params ) {

    $terms = (array) $params[ 'terms' ];

    unset( $params[ 'terms' ] );


    foreach ( $params as $key => $value ) {

      $this->$key = $value;

    }
    // foreach


    foreach ( $terms as $term_set_id => $term_set ) {

      if ( ! in_array( $term_set_id, $this->allowed_term_sets ) ) {

        unset( $terms[ $term_set_id ] );

      }
      // if

    }
    // foreach


    foreach ( $this->allowed_term_sets as $term_set_id ) {

      if ( ! array_key_exists( $term_set_id, $terms ) ) {

        $terms[ $term_set_id ] = array();

      }
      // if

    }
    // foreach


    if ( $terms ) {

      $this->set_terms( NULL, NULL, $terms );

    }
    // if


    return;

  }
  // __construct


  public function __call( $name, $arguments ) {

    $return_val = NULL;


    if (

      property_exists( $this, $name ) &&

      ( ! in_array( $name, array( 'terms' ) ) )

    ) {

      if ( array_key_exists( 0, $arguments ) ) {

        $this->$name = $arguments[0];

      }
      // if


      $return_val = $this->$name;

    }
    // if


    return $return_val;

  }
  // __call


  /**
   *
   * Get / set terms.
   *
   * @param string $operation Indicates what operation ( get | set | unset ) to perform.
   *
   * @param string $term_set Identifies the set of terms to access.
   *
   * @param mixed $term_id Identifies the specific term to access
   *
   * @param mixed $value New value for the terms / term set / term.
   *
   * @return mixed
   *
   */

  function get_set_terms( $operation, $term_set = NULL, $term_id = NULL, $value = NULL ) {

    $this->terms = (array) $this->terms;

    $terms = $this->terms;

    if ( strlen( $term_set ) ) {

      if ( strlen( $term_id ) ) {

        if ( $operation == 'set' ) {

          $value->host_env_id( $this->host_env_id() );

          $value->set_facet_id( $this->id() );

          $value->set_term_set_id( $term_set );

          $this->terms[ $term_set ][ $term_id ] = $value;

        }
        // if

        elseif ( $operation == 'unset' ) {

          unset( $this->terms[ $term_set ][ $term_id ] );

        }
        // elseif


        $terms = $this->terms[ $term_set ][ $term_id ];

      }
      // if


      else {

        if ( ( $operation == 'set' ) && is_array( $value ) ) {

          $this->terms[ $term_set ] = array();

          foreach ( $value as $t_id => $term ) {

            $this->set_terms( $term_set, $t_id, $term );

          }
          // foreach

        }
        // if

        elseif ( $operation == 'unset' ) {

          unset( $this->terms[ $term_set ] );

        }
        // elseif


        $terms = $this->terms[ $term_set ];

      }
      // else

    }
    // if


    elseif ( $operation == 'unset' ) {

      foreach ( $this->terms as $current_term_set => $current_facest ) {

        $this->terms[ $current_term_set ] = array();

      }
      // foreach

    }
    // elseif


    elseif ( is_array( $value ) ) {

      $this->terms = array();

      foreach ( $value as $key => $element ) {

        if ( is_array( $element ) && in_array( $key, $this->allowed_term_sets ) ) {

          $this->set_terms( $key, NULL, $element );

        }
        // if

      }
      // foreach

    }
    // elseif


    return $terms;

  }
  // get_set_terms


  /**
   * Set new value for terms / term set / term.
   *
   * @param string $term_set Identifies the term set to set.
   *
   * @param mixed $term_id Identifies the specific term to set.
   *
   * @param mixed $value New value for the terms / term set / term.
   *
   * @param bool $unset Whether to unset if $value is NULL.
   *
   */

  function set_terms( $term_set = NULL, $term_id = NULL, $value = NULL, $unset = FALSE ) {

    $terms = $this->get_set_terms(

      ( ( $unset && is_null( $value ) ) ? 'unset' : 'set' ),

      $term_set,

      $term_id,

      $value

    );


    return $terms;

  }
  // set_terms


  /**
   * Get terms / term set / term.
   *
   * @param string $term_set Identifies the requested term set.
   *
   * @param mixed $term_id Identifies the requested term.
   *
   * @return mixed
   *
   */

  function get_terms( $term_set = NULL, $term_id = NULL ) {

    $terms = $this->get_set_terms( 'get', $term_set, $term_id );


    return $terms;

  }
  // get_terms


  /**
   * Get the count of records matched by the facet.
   *
   * @return int
   *
   */

  function count() {

    $count = 0;

    foreach ( $this->terms as $term_set_id => $terms ) {

      foreach ( $terms as $term ) {

        $count += $term->count();

      }
      // foreach

    }
    // foreach


    return $count;

  }
  // count


  public function count_term_matches() {

    return;

  }
  // count_term_matches


  /**
   * Get $this->criteria.
   *
   * @return mixed
   *
   */

  public function get_criteria() {

    return $this->criteria;

  }
  // get_criteria


  /**
   * Set an element of $this->criteria.
   *
   * @param mixed $param Name of the param to set.
   *
   * @param mixed $value New value.
   *
   * @param bool $unset Whether to unset if $value is NULL.
   *
   * @return void
   *
   */

  public function set_criteria_item( $param, $value, $unset = FALSE ) {

    $criteria = $this->criteria;


    if ( is_null( $value ) && $unset ) {

      unset( $criteria[ $param ] );

    }
    // if

    else {

      $criteria[ $param ] = $value;

    }
    // else


    $this->set_criteria( $criteria );


    return;

  }
  // set_criteria_item


  /**
   * Get a an element of $this->criteria.
   *
   * @param mixed $param Identifies the element to retrieve.
   *
   * @return mixed
   *
   */

  public function get_criteria_item( $param ) {

    return $this->criteria[ $param ];

  }
  // get_criteria_item


  /**
   * Generate data required to display the UI for this facet.
   *
   * @param array $request_params Request parameters.
   *
   * @return void
   *
   */

  public function generate_ui_data( $request_params ) {

    foreach ( $this->get_terms() as $term_set_id => $terms ) {

      foreach ( $terms as $t_id => $term ) {

        $term->generate_ui_data( $request_params );

      }
      // foreach

    }
    // foreach


    if (

      $this->facet_type == 'range' ||

      $this->multiple_match_mode() != 'none'

    ) {

      $this->generate_remove_query_string( $request_params );

    }
    // if


    return;

  }
  // generate_ui_data


  /**
   * Generate and store a query string that would result in the removal of this facet from the request.
   *
   * @param array $request_params Current request parameters.
   *
   * @return void
   *
   */

  protected function generate_remove_query_string( $request_params ) {

    $host_env = $this->host_env();

    /*

    The facet allows multiple matches or is a range type, and is specified in the request.  Since multiple values may be specified for the facet, generate a query string to remove the entire facet from the request, in one fell swoop.

    */

    if ( $request_params[ $host_env->facet_key() ][ $this->id() ] ) {

      $new_request = $request_params;

      unset( $new_request[ $host_env->facet_key() ][ $this->id() ] );

      $query_string = http_build_query( $new_request, '', $host_env->param_delim() );

      $this->query_string( $query_string );

      $this->selected( TRUE );

    }
    // if


    return;

  }
  // generate_remove_query_string


  /**
   * Indicates whether this facet should be displayed in the UI.
   *
   * @return bool
   *
   */

  public function visible() {

    $visible = $this->count() || $this->display_empty();


    return $visible;

  }
  // visible


  /**
   * Get the request parameters that represent this facets current criteria.
   *
   * @return mixed
   *
   */

  public function get_request_params() {

    $request_params = $this->get_criteria();

    if ( ! $request_params ) {

      $request_params = NULL;

    }
    // if


    return $request_params;

  }
  // get_request_params


  public function get_label_format_callback() {

    return $this->label_format_callback;

  }
  // get_label_format_callback


  public function host_env_id( $instance_id = NULL ) {

    if ( $instance_id ) {

      $this->host_env_id = $instance_id;


      foreach ( $this->terms as $term_set => $terms ) {

        foreach ( $terms as $term ) {

          $term->host_env_id( $instance_id );

        }
        // foreach

      }
      // foreach

    }
    // if


    return $this->host_env_id;

  }
  // host_env_id


  protected function host_env() {

    return Faceted_Browser::get_instance( $this->host_env_id );

  }
  // host_env


  public function get_allowed_term_sets() {

    return $this->allowed_term_sets;

  }
  // get_allowed_term_sets


  public function multiple_match_mode( $mode = NULL ) {

    $mode = strtolower( $mode );

    if ( in_array( $mode, array( 'none', 'any', 'all' ) ) ) {

      $this->multiple_match_mode = $mode;

    }
    // if


    return $this->multiple_match_mode;

  }
  // multiple_match_mode

}
// Facet


/* EOF */

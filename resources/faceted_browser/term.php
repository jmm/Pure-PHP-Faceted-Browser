<?php

/*

Copyright © 2009-2010 Jesse McCarthy <http://jessemccarthy.net/>

This file is part of PHP Faceted Browser (the "Software").

The Software is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The Software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the Software.  If not, see <http://www.gnu.org/licenses/>.

*/

/**
 * Term belonging to a facet.
 */

class Term {

  /** @property string */
  protected $host_env_id;

  /** @property string */
  protected $facet_id;

  /** @property string */
  protected $term_set_id;

  /** @property int|string */
  protected $id;

  /** @property string */
  protected $label;

  /** @property array Matching records. */
  protected $records;

  /** @property bool */
  protected $selected;

  /** @property string */
  protected $query_string;

  /** @property bool */
  protected $visible = FALSE;


  /**
   * Instantiate.
   *
   * @param integer|string $id Uniquely identifies the term within the term set.
   *
   * @param string $label Label that will be displayed in the UI.
   *
   * @return void
   */

  public function __construct( $id, $label ) {

    foreach ( array( 'id', 'label' ) as $param ) {

      $this->$param = $$param;

    }
    // foreach


    $this->records = array();

    $this->query_string = "";

    $this->selected = FALSE;


    return;

  }
  // __construct


  /**
   * Add a record to the array of matches.
   *
   * @param integer $id Record ID
   *
   * @return void
   */

  public function add_record( $id ) {

    $this->records[ $id ] = $id;


    return;

  }
  // add_record


  /**
   * Return the number of records that matched the term.
   *
   * @return integer
   */

  public function count() {

    $count = count( $this->records );


    return $count;

  }
  // count


  /**
   * Implement basic getter / setter functionality for properties.
   *
   * @param string $prop Property name
   *
   * @param mixed $value New value for $prop.
   *
   * @return mixed
   */

  protected function basic_get_set( $prop, $value = NULL ) {

    if ( isset( $value ) ) {

      $this->$prop = $value;

    }
    // if


    return $this->$prop;

  }
  // basic_get_set


  protected function __call( $name, $arguments ) {

    $return_value = NULL;

    $basic_get_set_bools = array( 'selected', 'visible' );

    $basic_get_set_vars = array_merge( array( 'id', 'query_string' ), $basic_get_set_bools );

    if ( in_array( $name, $basic_get_set_vars ) ) {

      if ( in_array( $name, $basic_get_set_bools ) && $arguments ) {

        $arguments[0] = (bool) $arguments[0];

      }
      // if


      $return_value = $this->basic_get_set( $name, $arguments[0] );

    }
    // if


    return $return_value;

  }
  // __call


  public function label( $formatted = FALSE ) {

    return $this->get_label( $formatted );

  }
  // label


  public function get_label( $formatted = FALSE ) {

    $label = $this->label;

    if (

      $formatted AND

      $callback = $this->get_facet()->get_label_format_callback()

    ) {

      $label = call_user_func( $callback, $this );

    }
    // if


    return $label;

  }
  // get_label


  public function set_label( $label ) {

    $this->label = $label;


    return;

  }
  // set_label


  /**
   * Generate data necessary to generate the UI output for a term.
   *
   * @param array $request_params Request parameters.
   *
   * @return void
   *
   */

  function generate_ui_data( $request_params ) {

    $host_env = $this->host_env();

    $facet = $this->get_facet();

    $term_set_id = $this->get_term_set_id();

    $key = array_search(

      $this->id(),

      $request_params[ $host_env->facet_key() ][ $facet->id() ]

    );


    if ( $key !== FALSE || $this->count() ) {

      // If the facet doesnt allow multiple matches, discard previously specified data

      if ( $facet->multiple_match_mode() == 'none' ) {

        $request_params[ $host_env->facet_key() ][ $facet->id() ] = array();

      }
      // if


      // The term was specified in the request

      if ( $key !== FALSE ) {

        // Remove the current term value from the new request

        unset( $request_params[ $host_env->facet_key() ][ $facet->id() ][ $key ] );


        // Indicate the terms selected status

        $this->selected( TRUE );

      }
      // if


      // The term wasnt specified in the request

      elseif ( $facet->multiple_match_mode() == 'none' ) {

        // Add the current term value to the new request

        $request_params[ $host_env->facet_key() ][ $facet->id() ] = $this->id();

      }
      // elseif


      // The term wasnt specified in the request

      else {

        $request_params[ $host_env->facet_key() ][ $facet->id() ][] = $this->id();

      }
      // else


      // Build the new query string.  It will either add or subtract the current term.

      $query_string = http_build_query( $request_params, '', $host_env->param_delim() );

      $this->query_string( $query_string );


      $this->visible( TRUE );

    }
    // if


    elseif ( $facet->display_empty() ) {

      $this->visible( TRUE );

    }
    // elseif


    return;

  }
  // generate_ui_data


  /**
   * Generate UI output for a term.
   *
   * @return string
   *
   */

  public function generate_ui() {

    $host_env = $this->host_env();


    $term_link_fragment_id = 'term-link-';

    $label = $host_env->gen_nav_frag( 'term-label', array(

      'label' => htmlspecialchars( $this->label( TRUE ) ),

      'count' => htmlspecialchars( $this->count() )

    ) );


    if ( $this->selected() || $this->query_string() ) {

      $fragment_id = $term_link_fragment_id . ( $this->selected() ? 'remove' : 'select' );

      $label = $host_env->gen_nav_frag( $fragment_id, array(

        'label' => $label,

        'base_url' => $host_env->base_url(),

        'query_string' => $this->query_string()

      ) );

    }
    // if


    $output = $host_env->gen_nav_frag( 'term', array(

      'label' => $label,

      'selected' => $this->selected()

    ) );


    return $output;

  }
  // generate_ui


  public function get_term_set_id() {

    return $this->term_set_id;

  }
  // get_term_set_id


  public function set_term_set_id( $term_set_id ) {

    $this->term_set_id = $term_set_id;


    return;

  }
  // set_term_set_id


  public function get_facet() {

    return $this->host_env()->get_facet( $this->facet_id );

  }
  // get_facet


  public function set_facet_id( $facet_id ) {

    $this->facet_id = $facet_id;


    return;

  }
  // set_facet_id


  function host_env_id( $instance_id = NULL ) {

    if ( $instance_id ) {

      $this->host_env_id = $instance_id;

    }
    // if


    return $this->host_env_id;

  }
  // host_env_id


  function host_env() {

    return Faceted_Browser::get_instance( $this->host_env_id );

  }
  // host_env

}
// Term


/* EOF */
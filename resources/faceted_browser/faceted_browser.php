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

abstract class Faceted_Browser {

  private static $instances;

  protected $instance_id;


  // The key to the facet parameters in the request parameters hash

  protected $facet_key;


  // The delimiter for request parameters.  Used when building query strings to propagate the facet selections contained in the request.

  protected $param_delim = "&";


  protected $request_params;


  // Request parameters that have been processed to conform to certain requirements.

  protected $canonical_request_params;


  // The facet definitions.

  protected $facet_defs;


  // The facets specified in the request.

  protected $criteria;


  protected $views_path;


  /**
   * Instantiate.
   *
   * Possible elements of $config include:
   * facet_key
   * views_path
   * base_url
   * param_delim
   *
   * @param string $instance_id Identifies this browser instance from others running concurrently.
   *
   * @param array $facet_defs Facet definition arrays.
   *
   * @param array $request_params Request parameters (criteria, etc.).
   *
   * @param array $config Configuration parameters.
   *
   * @return void
   *
   */

  public function __construct( $instance_id, $facet_defs, $request_params, $config = array() ) {

    $this->instance_id = $instance_id;

    self::$instances[ $instance_id ] = $this;


    if ( ! $config[ 'views_path' ] ) {

      $config[ 'views_path' ] = ( dirname( __FILE__ ) . "/views" );

    }
    // if


    foreach ( array( 'facet_key', 'views_path', 'base_url', 'param_delim' ) as $param ) {

      if ( array_key_exists( $param, $config ) ) {

        $this->$param = $config[ $param ];

      }
      // if

    }
    // foreach


    // Request params may be an array or a query string.  Convert to an array if necessary.

    if ( ! is_array( $request_params ) ) {

      $temp = $request_params;

      $request_params = array();

      parse_str( $temp, $request_params );

      unset( $temp );

    }
    // if


    $this->facet_defs = array();

    $this->construct_objects( $facet_defs );

    $this->process_criteria( $request_params );


    return;

  }
  // __construct



  /**
   * Instantiate any facet and term objects as necessary.
   *
   * @param array $facet_defs Array of mixed facet definition arrays or objects.
   *
   * @return void
   */

  protected function construct_objects( $facet_defs ) {

    foreach ( $facet_defs as $f_id => $facet_def ) {

      if ( is_array( $facet_def ) ) {

        $facet_def[ 'host_env_id' ] = $this->instance_id();

        foreach ( $facet_def[ 'terms' ] as $term_set_id => $terms ) {

          foreach ( $terms as $t_id => $term ) {

            if ( is_array( $term ) ) {

              $facet_def[ 'terms' ][ $term_set_id ][ $t_id ] = $this->term_factory(

                $term,

                array( 'facet_type' => $facet_def[ 'facet_type' ] )

              );

            }
            // if

          }
          // foreach

        }
        // foreach


        $facet_def = $this->facet_factory( $facet_def, array() );

      }
      // if


      else {

        $facet_def->host_env_id( $this->instance_id() );

      }
      // else


      $this->facet_defs[ $f_id ] = $facet_def;

    }
    // foreach


    return;

  }
  // construct_objects


  /**
   * Process criteria to conform to certain requirements and load it into facets.
   *
   * @param array $request_params
   *
   * @return void
   *
   */

  protected function process_criteria( $request_params ) {

    $this->criteria = array();

    $facet_key = $this->facet_key;

    $facet_defs = $this->facet_defs;

    $canonical_request_params = $request_params;

    $canonical_request_params[ $facet_key ] = array();


    // Clean up request params

    foreach ( $request_params[ $facet_key ] as $f_id => $facet_params ) {

      if ( ! ( $facet = $facet_defs[ $f_id ] ) ) {

        continue;

      }
      // if


      $facet->set_criteria( $facet_params );


      if ( ! is_null( $facet_params = $facet->get_request_params() ) ) {

        $canonical_request_params[ $facet_key ][ $f_id ] = $facet_params;

        $this->criteria[ $f_id ] = $facet;

      }
      // if

    }
    // foreach


    $this->canonical_request_params = $canonical_request_params;


    return;

  }
  // process_criteria


  /**
   * Generate data that's needed to display the UI.
   *
   * @return void
   */

  public function generate_ui_data() {

    $facet_key = $this->facet_key;


    $old_request = $this->canonical_request_params();


    foreach ( $this->facet_defs as $f_id => $facet_def ) {

      // Omit facets that are empty and not configured to display when empty

      if ( ! $facet_def->visible() ) {

        continue;

      }
      // if


      // Copy the previous request params into a template.  This is done at the facet level so that the following array cast only has to be performed once per facet.  For each term the template will be copied to $new request to define the params.

      $new_request = $old_request;


      // Make sure the facet entry in the new request params is an array

      $new_request[ $facet_key ][ $f_id ] = (array) $new_request[ $facet_key ][ $f_id ];


      $facet_def->generate_ui_data( $new_request );

    }
    // foreach


    return;

  }
  // generate_ui_data


  /**
   * Generate the UI.
   *
   * @return string The HTML representing the UI
   *
   */

  public function generate_ui() {

    $output = array();

    $this->generate_ui_data();

    $request_params = $this->canonical_request_params;

    foreach ( $this->facet_defs as $f_id => $facet_def ) {

      $facet_label = $this->gen_nav_frag( 'facet-label', array(

        'label' => htmlspecialchars( $facet_def->label() )

      ) );


      $output[ $f_id ] = $facet_def->generate_ui( array(

        'facet_label' => $facet_label,

        'facet_request_key' => $this->facet_key()

      ) );

    }
    // foreach


    $output = $this->gen_nav_frag( 'facets', array(

      'facets' => $output

    ) );


    return $output;

  }
  // generate_ui


  /**
   * Generate a fragment of the UI.
   *
   * @param string $fragment_id Identifies the fragment (template).
   *
   * @param array $data Data and content needed to generate the fragment.
   *
   * @return string The generated HTML.
   *
   */

  public function gen_nav_frag( $fragment_id, $data ) {

    extract( $data );

    if ( ! array_key_exists( 'data', $data ) ) {

      unset( $data );

    }
    // if


    ob_start();

    require "{$this->views_path}/{$fragment_id}.php";

    $nav_frag = ob_get_clean();


    return $nav_frag;

  }
  // gen_nav_frag


  /**
   * Generate output to persist the current request parameters (e.g. a query string converted to HTML hidden inputs).
   *
   * For facets that are presented using form controls instead of links, the existing request params need to be persisted along with the new value selected with the form control.  Generate hidden inputs to persist the existing params when the form is submitted.  Exclude the current facet so that the users new selection will set the value for it.
   *
   * @param obj $facet_def Facet to generate the output for.
   *
   * @return string
   *
   */

  public function get_request_persist_output( $facet_def ) {

    $output = "";

    $param_delim = htmlspecialchars( $this->param_delim );


    $request_persist_params = $this->canonical_request_params();

    unset( $request_persist_params[ $this->facet_key ][ $facet_def->id() ] );

    $query_string = http_build_query( $request_persist_params, '', $param_delim );


    if ( $query_string ) {

      $output = $this->convert_query_string_to_hidden_inputs( $query_string );

    }
    // if


    return $output;

  }
  // get_request_persist_output


  /**
   * Convert a query string to hidden HTML INPUT elements.
   *
   * @param array $params Request parameters.
   *
   * @return string
   *
   */

  protected function convert_query_string_to_hidden_inputs( $params ) {

    $output = $params;

    $output = explode( htmlspecialchars( $this->param_delim ), $output );

    foreach ( $output as $o_key => $param_pair ) {

      $param_pair = explode( "=", $param_pair );

      $param_pair = array_map( 'htmlspecialchars', array_map( 'urldecode', $param_pair ) );

      $output[ $o_key ] = <<<DOCHERE
<input type="hidden" name="{$param_pair[0]}" value="{$param_pair[1]}" />
DOCHERE;

    }
    // foreach


    $output = join( "\n\n", $output );


    return $output;

  }
  // convert_query_string_to_hidden_inputs


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function base_url() {

    return $this->base_url;

  }
  // base_url


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function facet_key() {

    return $this->facet_key;

  }
  // facet_key


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function param_delim() {

    return $this->param_delim;

  }
  // param_delim


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function request_params() {

    return $this->request_params;

  }
  // request_params


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function canonical_request_params() {

    return $this->canonical_request_params;

  }
  // canonical_request_params


  /**
   * Getter.
   *
   * @return string
   *
   */

  public function get_criteria() {

    return $this->criteria;

  }
  // get_criteria


  protected function instance_id() {

    return $this->instance_id;

  }
  // instance_id


  public static function get_instance( $instance_id ) {

    return self::$instances[ $instance_id ];

  }
  // get_instance


  public function get_facet( $facet_id ) {

    return $this->facet_defs[ $facet_id ];

  }
  // get_facet


  abstract public static function facet_factory( $facet_def, $config );

  abstract public static function term_factory( $term_def, $config );

}
// Faceted_Browser


/* EOF */
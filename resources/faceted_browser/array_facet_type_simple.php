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

class Array_Facet_Type_Simple extends Facet_Type_Simple {

  /**
   * Test for match of this facets criteria to the given record.
   *
   * @param array $record
   *
   * @param array $data_field_column A column of data extracted from $record.
   *
   * @return bool
   *
   */

  public function match_criteria( $record, $data_field_column ) {

    $matched = FALSE;


    $criteria = (array) $this->get_criteria();


    if ( ! count( $criteria ) ) {

      return $matched;

    }
    // if


    /*

    $record may contain an element that is a multi-dimensional array.  For each top-level element of that array, it may be necessary to extract a certain element from deep within the array.  For example, if $record[ 'example' ] is an array of arrays such as the following:

    $record[ 'example' ] = array(

      array( 'A' => array( 'B' => array( 'C' => "Honda" ) ) ),

      array( 'A' => array( 'B' => array( 'C' => "Toyota" ) ) ),

      array( 'A' => array( 'B' => array( 'C' => "Nissan" ) ) )

    )
    // array

    It may be necessary to generate an array of the 'C' elements from each element of $record[ 'example' ], e.g.

    array( "Honda", "Toyota", "Nissan" )

    */


    $intersection = array_intersect( $data_field_column, $criteria );

    // When multiple_match_mode == none, criteria has been reduced to a single value

    $multiple_match_mode = array( 'any' => 'any', 'all' => 'all', 'none' => 'all' );

    $multiple_match_mode = $multiple_match_mode[ $this->multiple_match_mode() ];


    // The record matches the required number of criteria values

    $matched = (

      $multiple_match_mode == 'any' &&

      count( $intersection )

      ||

      $multiple_match_mode == 'all' &&

      count( $intersection ) == count( $criteria )

    );
    // $matched


    return $matched;

  }
  // match_criteria


  /**
   * Match the given record to the terms.
   *
   * @param string $range_point Identifies the range point ( min | max )
   *
   * @param array $record
   *
   * @param mixed $data
   *
   * @return void
   *
   */

  public function match_record_to_terms( $terms_set, $record, $data ) {

    if ( $term = $this->get_terms( $terms_set, $data ) ) {

      $term->add_record( $record[ 'id' ] );

    }
    // if


    return;

  }
  // match_record_to_terms

}
// Array_Facet_Type_Simple


/* EOF */
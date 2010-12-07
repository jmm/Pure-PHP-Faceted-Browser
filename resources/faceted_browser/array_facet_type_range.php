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

class Array_Facet_Type_Range extends Facet_Type_Range {

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


    if ( ! count( $this->get_criteria() ) ) {

      return TRUE;

    }
    // if


    $min = $this->get_criteria_item( 'min' );

    $max = $this->get_criteria_item( 'max' );

    // Resolve $max < $min

    if ( strlen( $max ) && strlen( $min ) && $min > $max ) {

      $max = $min;

    }
    // if


    foreach ( $data_field_column as $data ) {

      // Determine if min and max criteria both match, or are not specified. Stop processing if a match is found.

      if ( $matched = (

        ! strlen( $min ) || $data >= $min

        AND

        ! strlen( $max ) || $data <= $max

      ) ) {

        break;

      }
      // if

    }
    // foreach


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

  public function match_record_to_terms( $range_point, $record, $data ) {

    if ( $this->pre_defined_terms() ) {

      $terms = $this->get_terms( $range_point );

      if ( $range_point == 'min' ) {

        $terms = array_reverse( $terms, TRUE );

      }
      // if


      foreach ( $terms as $t_id => $term ) {

        if (

          $range_point == 'min' && $t_id <= $data

          ||

          $range_point == 'max' && $t_id >= $data

        ) {

          $term->add_record( $record[ 'id' ] );

          break;

        }
        // if

      }
      // foreach

    }
    // if


    elseif ( $term = $this->get_terms( $range_point, $data ) ) {

      $term->add_record( $record[ 'id' ] );

    }
    // elseif


    return;

  }
  // match_record_to_terms

}
// Array_Facet_Type_Range


/* EOF */
<?php

class Term_Label_Formatters {

  public function thousands( $term ) {

    return number_format( $term->label() );

  }
  // thousands

}
// Term_Label_Formatters


/* EOF */
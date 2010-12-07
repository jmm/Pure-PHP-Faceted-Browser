<?php

$demo_resources_path = ( dirname( __FILE__ ) . "/resources" );

require_once "{$demo_resources_path}/demo_init.php";

?>

<!DOCTYPE html>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Pure PHP Faceted Browser / Array Faceting Demo</title>

<link rel="stylesheet" type="text/css" media="all" href="<?php echo $demo_dir_url; ?>/resources/demo.css" />

</head>


<body>

<div id="primary_layout">

<nav id="primary_nav">

<ul>

<li class="nav_item">
<a href="<?php echo $demo_dir_url; ?>/about.php">About this</a>
</li>

</ul>

</nav>


<?php

echo <<<DOCHERE

<div id="facet_nav">

<div class="facet">

<div class="term">
<a href="{$base_url}">View all</a>
</div>

</div>
<!-- .facet -->


{$facet_nav}

</div>
<!-- #facet_nav -->

DOCHERE;

?>


<div id="primary_content">

<?php

if ( $demo_def ) {

?>

<div id="demo_def">

<div class="description">

<?php echo $demo_def[ 'description' ]; ?>

<p>
<a href="<?php echo $demo_dir_url; ?>/demos.php">Other demo configurations</a>
</p>

</div>
<!-- .description -->

</div>
<!-- #demo_def -->

<?php

}
// if

?>


<div class="total_results">
Records Found: <?php echo count( $faceted_records ); ?>

</div>


<table id="records" >

<thead>

<tr>

<th class="id" scope="col">
ID
</th>

<th class="year" scope="col">
Year
</th>

<th class="make" scope="col">
Make
</th>

<th class="model" scope="col">
Model
</th>

<th class="form_factor" scope="col">
Style
</th>

<th class="mileage" scope="col">
Miles
</th>

<th class="color" scope="col">
Color
</th>

<th class="mpg" scope="col">
MPG
</th>

<th class="price" title="Peak Wavelength" scope="col">
Price
</th>

</tr>

</thead>


<tbody>

<?php

foreach ( $faceted_records as $record ) {

  $row_class = ( ( $row_class == 'odd' ) ? 'even' : 'odd' );

  $html = $record;

  foreach ( array( 'price', 'mileage' ) as $param ) {

    if ( $html[ $param ] ) {

      $html[ $param ] = number_format( $html[ $param ] );

    }
    // if

  }
  // foreach

  $html[ 'price' ] = ( strlen( $html[ 'price' ] ) ? "\${$html[ 'price' ]}" : "" );

  $html = array_map( 'htmlspecialchars', $html );

  echo <<<DOCHERE

<tr class="{$row_class}">

<td class="id">
{$html[ 'id' ]}
</td>

<td class="year">
{$html[ 'year' ]}
</td>

<td class="make">
{$html[ 'make' ]}
</td>

<td class="model">
{$html[ 'model' ]}
</td>

<td class="form_factor">
{$html[ 'form_factor' ]}
</td>

<td class="mileage">
{$html[ 'mileage' ]}
</td>

<td class="color">
{$html[ 'color' ]}
</td>

<td class="mpg">
{$html[ 'mpg' ]}
</td>

<td class="price">
{$html[ 'price' ]}
</td>

</tr>


DOCHERE;

}
// foreach

?>

</tbody>

</table>

</div>
<!-- #primary_content -->

</div>
<!-- #primary_layout -->

</body>

</html>

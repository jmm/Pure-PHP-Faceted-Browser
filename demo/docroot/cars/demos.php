<!DOCTYPE html>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Pure PHP Faceted Browser / Array Faceting Demos Index</title>

<link rel="stylesheet" type="text/css" media="all" href="resources/demos.css" />

</head>


<body>

<div id="primary_layout">

<div id="primary_content">

<h1 id="primary_content_heading">
Index of demos for pure PHP faceted browser / array faceting software
</h1>

<ul id="demos">

<?php

include "resources/demo_defs.php";


foreach ( $demo_defs as $demo_id => $demo_def ) {

  echo <<<DOCHERE

<li class="demo">

<p>
<a href="demo.php/{$demo_id}">{$demo_def[ 'label' ]}</a>
</p>

{$demo_def[ 'description' ]}

</li>
<!-- .demo -->

DOCHERE;

}
// foreach

?>


</ul>
<!-- #demos -->

</div>
<!-- #primary_content -->

</div>
<!-- #primary_layout -->

</body>

</html>
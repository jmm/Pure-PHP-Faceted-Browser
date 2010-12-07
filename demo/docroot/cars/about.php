<!DOCTYPE html>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>About the Pure PHP Faceted Browser / Array Faceting Demo</title>

<link rel="stylesheet" type="text/css" media="all" href="resources/about.css" />

</head>


<body>

<div id="primary_layout">

<div id="primary_content">

<p>
<a href="demo.php">Return to the demo</a>
</p>

<p>
This is a demo of a faceted browser implemented from scratch in pure PHP.  Not unexpectedly, the software doesn't have much potential for practical use, due to performance reasons.  It was just an experiment, and now it serves as a demo of my programming abilities and style.  Click here to <a href="https://github.com/jmm/Pure-PHP-Faceted-Browser">get the code</a>.
</p>

<p>
All of the data used in this demo is completely made up, except for the make and model names.  Because the data is randomly generated it may be non-sensical.  Some things that aren't particularly helpful are deliberately included in the UI to more fully demonstrate the sofware.  For example, it's not likely to be very helpful to choose a maximum MPG, or a minimum price.  The Color facet is configured to show empty terms to demonstrate that option.
</p>

<p>
Some notable things about this software are listed below.  There are some other <a href="demos.php">demo configurations</a> that illustrate some of those things.
</p>

<ul id="notable_features">

<li class="feature" id="feature-multi-match-mode">

<p>
Facets can be configured to match in 3 different modes when multiple criteria are provided for the facet, returning records that match:
</p>

<ol id="multi-match-modes">

<li class="mode">
Only the last item of criteria provided.
</li>

<li class="mode">
Any of the criteria provided.
</li>

<li class="mode">
All of the criteria provided.
</li>

</ol>
<!-- #multi-match-modes -->

</li>
<!-- #feature-multi-match-mode -->


<li class="feature">

<p>
The number of records that will match new criteria is provided even for range facets.  The counts are cumulative.  Empty and / or redundant values are (optionally) omitted from the UI.
</p>

</li>
<!-- .feature -->


<li class="feature">

<p>
Facet term values can be preconfigured, or extracted from the record set on-the-fly.
</p>

</li>
<!-- .feature -->


<li class="feature">

<p>
The UI for range facets can be populated with term values taken from the record set, or with selected values that the records will be grouped into.  E.g. the UI for a 'price' facet could include every specific price contained in the record set, or could be preconfigured to contain specific stops along the range of values contained in the record set, e.g.: $100, $200, $300, etc.
</p>

</li>
<!-- .feature -->


<li class="feature">

<p>
Some of this code may perhaps be useful to create a front-end to something like Sphinx &mdash; possibly a way to implement this functionality with a level of performance that would be useful.
</p>

</li>
<!-- .feature -->

</ul>
<!-- #notable_features -->


<div id="credit">

<p>
By Jesse McCarthy
</p>

</div>
<!-- #credit -->

</div>
<!-- #primary_content -->

</div>
<!-- #primary_layout -->

</body>

</html>
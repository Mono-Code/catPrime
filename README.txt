=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: monocode.com
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

# Description
Allows for Primary Categories to be applied & Queried

## How To Query
```
$example = new WP_Query( [
   'primary_term' => [
       'relation' => 'OR', // Any of the following conditions are met.
       'director' => [ 4, 5 ], // Post/CPT has taxonomy 'director' with a primary term id = 4 or 5
       'genre'    => 8  // Post/CPT has taxonomy 'genre' with a primary term id of 8
   ]
] );
```
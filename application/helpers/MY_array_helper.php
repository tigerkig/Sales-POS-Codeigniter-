<?php
function sort_assoc_array_by_label($a,$b)
{
   if ($a['label'] == $b['label']) {
          return 0;
      }
      return ($a['label'] < $b['label']) ? -1 : 1;
}

function sort_assoc_array_by_name($a,$b)
{
   if ($a['name'] == $b['name']) {
          return 0;
      }
      return ($a['name'] < $b['name']) ? -1 : 1;
}

function sort_cart_items($a, $b) 
{
	return $a['line'] - $b['line'];
}

?>

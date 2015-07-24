<?php
  $myArr = [1,2,3,4,5];
  $file = file_get_contents('http://localhost/fmagwp/wp-content/plugins/fantasticsimport/5216_nc.inc');
  eval("\$vars = $file;");
  //$b = serialize($myArr);
  var_dump($vars[0]["nid"]);
  //echo "\n".."\n";

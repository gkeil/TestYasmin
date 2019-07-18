<?php

require_once "ObjMap.php";

$map = new ObjMap();

$o1 = new stdClass();
$o1->id = 1;

$o2 = new stdClass();
$o2->id = 2;

$o3 = new stdClass();
$o3->id = 3;

echo $map->len()."\n";

$map->put( "uno", $o1 );
$map->put( "dos", $o2 );
$map->put( "tres", $o3 );

echo $map->len()."\n";

$pp = $map->get("dos");
var_dump($pp);

var_dump( $map->has("uno") );

var_dump( $map->has("xxx") );

$map->clear();
echo $map->len()."\n";
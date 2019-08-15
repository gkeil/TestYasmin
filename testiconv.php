<?php

//setlocale(LC_CTYPE , 'es_ES');
setlocale(LC_CTYPE , 'en_GB');

$seed = "San Martín";

$translated = iconv("UTF-8", "ASCII//TRANSLIT", $seed);
//$translated = iconv("UTF-8", "ASCII", $seed);

echo $seed.PHP_EOL;
echo $translated.PHP_EOL;
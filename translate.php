<?php

require 'src/Fetcher.php';

if (PHP_SAPI !== 'cli') {
    die("Please execute from command line\n");
}

if(count($argv) < 3) {
    die("Usage: php translate.php /path/to/cockpit /path/to/i18n\n");
}

try {

    (new Fetcher())->fetchFrom($argv[1])->writeTo($argv[2]);

} catch (Exception $e) {
    die("ERROR: ".$e->getMessage);
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/example_postgres_database.php';

use \IMSGlobal\LTI;

LTI\JWKS_Endpoint::new([
    'fad61007-e2dc-449e-88de-7947a55c156f' => file_get_contents(__DIR__ . '/src/db/private.key')
])->output_jwks();

?>

<?php
$yaml = file_get_contents('https://app.porego.com/merchant-api.openapi.yaml');
file_put_contents('porego_api.yaml', $yaml);
echo "Downloaded OpenAPI spec: " . strlen($yaml) . " bytes\n";

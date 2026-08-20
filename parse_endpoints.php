<?php
$content = file_get_contents('porego_js.js');
preg_match_all('#https://back\.porego\.com/[a-zA-Z0-9/\-]+#', $content, $matches);
print_r(array_unique($matches[0]));

preg_match_all('#/[a-zA-Z0-9/\-]+/tracking/[a-zA-Z0-9/\-]+#', $content, $matches);
print_r(array_unique($matches[0]));

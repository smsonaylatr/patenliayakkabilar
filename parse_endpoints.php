<?php
$content = file_get_contents('ShipmentsPage-DMRBgOg5.js');

$pos = strpos($content, 'function Gl');
if ($pos === false) $pos = strpos($content, 'Gl=');
if ($pos === false) $pos = strpos($content, 'Gl =');
echo "Gl at $pos:\n" . substr($content, $pos - 500, 2000) . "\n\n";

$pos2 = strpos($content, 'function Vr');
if ($pos2 === false) $pos2 = strpos($content, 'Vr=');
if ($pos2 === false) $pos2 = strpos($content, 'Vr =');
echo "Vr at $pos2:\n" . substr($content, $pos2 - 200, 1500) . "\n\n";
























<?php
$file = 'uploads-files/BVG-Prospectus.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="BVG-Prospectus.pdf"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>

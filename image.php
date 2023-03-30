<?php
// Get the image filename from the query string
$filename = $_GET['filename'];

// Set the content type to the correct MIME type
header('Content-Type: image/jpeg');

// Output the contents of the image file
readfile('./public/images/' . $filename);

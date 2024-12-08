<?php
// The URL you want to proxy
$target_url = "https://www.google.com";

// Initialize cURL session
$ch = curl_init();

// Set the target URL to fetch
curl_setopt($ch, CURLOPT_URL, $target_url);

// Enable the return of the transfer as a string of the return value
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set User-Agent header to mimic a browser request (optional)
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

// Execute the cURL session
$response = curl_exec($ch);

// Close the cURL session
curl_close($ch);

// Output the response (this will now be served from your server)
echo $response;
?>
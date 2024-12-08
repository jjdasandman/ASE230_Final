<?php
// Basic validation for URLs
function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) &&
           (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
}

// Get the target URL from the query parameter
if (isset($_GET['target'])) {
    $target_url = $_GET['target'];
    
    // Basic validation of URL
    if (validateURL($target_url)) {
        // Initialize cURL session
        $ch = curl_init();

        // Set the target URL
        curl_setopt($ch, CURLOPT_URL, $target_url);

        // Set the User-Agent to mimic a real browser
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36');

        // Follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Return the content as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Handle HTTPS requests
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for any cURL errors
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Rewrite URLs in the response to route through the proxy
            $response = preg_replace_callback(
                '/(href|src)=[\'"]?([^\'" >]+)[\'"]?/',
                function($matches) use ($target_url) {
                    $absolute_url = getAbsoluteURL($matches[2], $target_url);
                    return $matches[1] . '="proxy.php?target=' . urlencode($absolute_url) . '"';
                },
                $response
            );

            // Output the modified page content
            echo $response;
        }

        // Close cURL session
        curl_close($ch);

    } else {
        // Invalid URL format
        echo 'Invalid URL or unsupported protocol. Please use HTTP or HTTPS.';
    }
} else {
    echo 'No target URL provided.';
}

// Function to convert relative URLs to absolute URLs
function getAbsoluteURL($relative_url, $base_url) {
    // If the URL is already absolute, return it
    if (parse_url($relative_url, PHP_URL_SCHEME) != '') {
        return $relative_url;
    }

    // Parse the base URL and construct a full URL
    $base = parse_url($base_url);
    $base_path = isset($base['path']) ? rtrim(dirname($base['path']), '/') : '';

    // If the relative URL starts with a '/', append it to the domain
    if (substr($relative_url, 0, 1) == '/') {
        return $base['scheme'] . '://' . $base['host'] . $relative_url;
    } else {
        // Otherwise, append it to the current base path
        return $base['scheme'] . '://' . $base['host'] . $base_path . '/' . $relative_url;
    }
}
?>
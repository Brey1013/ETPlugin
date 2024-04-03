<?php

function paginate_array($data)
{
    $keys = array_keys($data);
    $currentKey = isset($_GET['key']) ? $_GET['key'] : '';

    // Get the current key index
    $currentIndex = array_search($currentKey, $keys);
    $totalKeys = count($keys);

    // Display pagination links
    echo '<div class="pagination">';
    // Previous page link
    if ($currentIndex !== false && $currentIndex > 0) {
        echo '<span class="page-item"><a href="?key=' . $keys[$currentIndex - 1] . '" class="page-link">Previous</a></span> ';
    }
    if ($currentIndex === false && $totalKeys > 0) {
        // If no key found in URL, show a link to the last element
        echo '<span class="page-item"><a href="?key=' . end($keys) . '" class="page-link">Previous</a></span>';
    }
    // Page number links
    for ($i = 0; $i < $totalKeys; $i++) {
        $isActive = ($keys[$i] === $currentKey) ? 'active' : '';
        echo '<span class="page-item ' . $isActive . '"><a href="?key=' . $keys[$i] . '" class="page-link">' . ($i + 1) . '</a></span> ';
    }
    // Next page link
    if ($currentIndex !== false && $currentIndex < $totalKeys - 1) {
        echo '<span class="page-item"><a href="?key=' . $keys[$currentIndex + 1] . '" class="page-link">Next</a></span>';
    } elseif ($currentIndex === false && $totalKeys > 0) {
        // do nothing
    } else {
        // Show a custom link if needed
        echo '<span class="page-item"><a href="' . get_permalink() . '" class="page-link">Next</a></span>';
    }
    echo '</div>';
}
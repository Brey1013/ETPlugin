<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function transform_object_for_frontend($object)
{
    $result = json_encode($object);

    return base64_encode($result);
}
<?php

require '../require.php';


$locationData = BertMaurau\URLShortener\Core\UrlTracker::getLocationData('78.23.45.92');
echo json_encode($locationData);

exit;

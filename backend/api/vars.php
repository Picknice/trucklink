<?php

$uri_api_type = '/' . explode('/', $uri)[2];
$limit = $_GET['limit'] ? (int) $_GET['limit'] : 10;
$offset = (int) $_GET['offset'];

$http_method = $_SERVER['REQUEST_METHOD'];
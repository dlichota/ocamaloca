<?php

require_once("curl_mock.php");
require_once(dirname(__DIR__) . "/vendor/autoload.php");

class TestCase extends \PHPUnit_Framework_TestCase {
    function setUp() {
        ShortPixel\setKey(NULL);
    }

    function tearDown() {
        ShortPixel\CurlMock::reset();
    }
}

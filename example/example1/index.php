<?php

/**
 * Bit&Black IDML-JSON Converter.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

/**
 * This example loads the `input.idml` file, converts it contents
 * into JSON and saves it as `output.json`. After that, the extracted
 * contents are also saved as `output.idml`, which should be identical
 * to the original input file.
 */

use BitAndBlack\IdmlJsonConverter\File\IDML;
use BitAndBlack\IdmlJsonConverter\File\JSON;

require_once dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$idmlFile = __DIR__ . DIRECTORY_SEPARATOR . 'input.idml';

$idml = new IDML($idmlFile);

$idmlContentAsArray = $idml->getContent();
$idmlContentAsJson = $idml->getJSON();

file_put_contents(
    __DIR__ . DIRECTORY_SEPARATOR . 'output.json',
    $idmlContentAsJson
);

$json = new JSON($idmlContentAsArray);
$idmlContent = $json->getIDML();

file_put_contents(
    __DIR__ . DIRECTORY_SEPARATOR . 'output.idml',
    $idmlContent
);

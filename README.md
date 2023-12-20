[![PHP from Packagist](https://img.shields.io/packagist/php-v/bitandblack/idml-json-converter)](http://www.php.net)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/bc9b41c3e415497aaf6dcf81f20d1351)](https://app.codacy.com/gh/BitAndBlack/idml-json-converter/dashboard)
[![Total Downloads](https://poser.pugx.org/bitandblack/idml-json-converter/downloads)](https://packagist.org/packages/bitandblack/idml-json-converter)
[![License](https://poser.pugx.org/bitandblack/idml-json-converter/license)](https://packagist.org/packages/bitandblack/idml-json-converter)

<p align="center">
    <a href="https://www.bitandblack.com" target="_blank">
        <img src="https://www.bitandblack.com/build/images/preview-default.jpg" alt="Bit&Black Logo" width="400">
    </a>
</p>

# Bit&Black IDML-JSON Converter

Convert Adobe InDesign Markup Language Files (IDML) into JSON and JSON into IDML.

## Motivation

Using this converter allows a __simple handling of IDML__ files in PHP.

-   __Extracting__ information is easy, because you only need to navigate through an array, that holds the whole content of an IDML file.
-   __Manipulating__ information is easy, because you can change all values by your needs. This allows handling of placeholders, that have been added in Adobe InDesign.

__Please note__ that the IDML-JSON Converter doesn't interpret the values inside an IDML. That means that you, for example, need to calculate positions of elements by your own.

## Example

If you want to have a quick look at how the JSON looks like, navigate to the [example](./example) folder and take the [output.json](./example/example1/output.json) file.

## Installation

This library is written in [PHP](https://www.php.net) and made for the use with [Composer](https://packagist.org/packages/bitandblack/idml-json-converter). Be sure to have both of them installed on your system.

Add the library then to your project by running `$ composer require bitandblack/idml-json-converter`.

## Usage

### From command line

This library comes with two commands that allow the conversion of IDML into JSON and JSON into IDML via CLI.

The CLI is located under [`bin/idml-json-converter`](bin/idml-json-converter) or, if you installed the library as Composer dependency, under `vendor/bin/idml-json-converter`.

Use the command

-    `idml:convert:json` to convert an IDML file into JSON.
-    `json:convert:idml` to convert a JSON file into IDML.

Add option `-h` to get more information about the usage of a command.

### Custom

Instead of using the CLI, it is also possible to converts the contents manually.

#### Converting an IDML file

Use the [IDML](./src/File/IDML.php) class and initialize it with the path to an IDML. Calling the `getContent()` method will return its content as an array.

```php 
<?php

use BitAndBlack\IdmlJsonConverter\File\IDML;

$idml = new IDML('/path/to/file.idml');
$idmlContent = $idml->getContent();
```

The array contains the name of each file and its content then. For example:

```text
[
    'mimetype' => 'application/vnd.adobe.indesign-idml-package',
    'designmap.xml' => [
        '@name' => 'Document',
        '@attributes' => [
            'DOMVersion' => 18.0,
            'Self' => 'd',
            'StoryList' => [
                0 => 'ufa',
                1 => 'u126',
                2 => 'u97',
            ],
            'Name' => 'file.indd',
[...]
```

You can use the `getJSON()` method to return the content converted into a JSON string.

#### Converting JSON content

Use the [JSON](./src/File/JSON.php) class and initialize it with an array of your content. The array needs to have the same structure a shown above. Calling the `getIDML()` method will return its content as an string, that can be saved as IDML file (for example by using `file_put_contents()`).

```php 
<?php

use BitAndBlack\IdmlJsonConverter\File\JSON;

$content = [
    'mimetype' => 'application/vnd.adobe.indesign-idml-package',
    'designmap.xml' => [
        '@name' => 'Document',
        '@attributes' => [
            'DOMVersion' => 18.0,
            'Self' => 'd',
            'StoryList' => [
                0 => 'ufa',
                1 => 'u126',
                2 => 'u97',
            ],
            'Name' => 'file.indd',
    [...]
];

$json = new JSON($content);
$idmlContent = $json->getIDML();

file_put_contents(
    '/path/to/file.idml',
    $idmlContent
);
```

## Other Tools

Bit&Black offers some more tools to handle IDML files:

-   The [IDML-Creator](https://www.idml.dev/en/idml-creator-php.html) library that allows creating IDML content natively in PHP in an object-oriented way. (A demo is available [here](https://bitbucket.org/wirbelwild/idml-creator-demo).)
-   The [IDML-Writer](https://www.idml.dev/en/idml-writer-php.html) library that can write IDML content into a valid IDML file.
-   The [IDML-Validator](https://www.idml.dev/en/idml-validator-php.html) library that allows validating IDML files against the official schema from Adobe.

Feel free to visit [www.idml.dev](https://www.idml.dev) for more information!

## Help

If you have any questions feel free to contact us under `hello@bitandblack.com`.

Further information about Bit&Black can be found under [www.bitandblack.com](https://www.bitandblack.com).
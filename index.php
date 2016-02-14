<?php

require_once('textrazor/TextRazor.php');

TextRazorSettings::setApiKey('ec4d485ae28034eea844c3dea57996a331e425ed896601b58d24418e');

$text = 'what does the fox say';

$textrazor = new TextRazor();

$textrazor->addExtractor('words');

$response = $textrazor->analyze($text);

echo "<pre>";
var_export($response);
echo "</pre>";

if (isset($response['response']['words'])) {
    foreach ($response['response']['words'] as $entity) {
        var_export($entity);
        print(PHP_EOL);
    }
}
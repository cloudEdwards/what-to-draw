<?php

$PRODUCTION = false;


ini_set('display_errors',1);  error_reporting(E_ALL);

require_once('textrazor/TextRazor.php');

require_once('twitter-api-php/TwitterAPIExchange.php');
include('random_text.php');
require('wordnik-php/wordnik/Swagger.php');



$myAPIKey = 'YOUR KEY GOES HERE';
$client = new APIClient($myAPIKey, 'http://api.wordnik.com/v4');



/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "2463626622-Na9ZpguldSb65yYPdMzQzASIE6OZdSyRXaw4xIz",
    'oauth_access_token_secret' => "H7ngiKbfXdTtpaR3iHTIiiLNP1vZXQ4FAq66lm5XE2QKJ",
    'consumer_key' => "rPs7XKYhQBjJDU9EPEI8C1XsP",
    'consumer_secret' => "WeLm8XK7cr3rkmvI4xuInPMBXUbRskz8CYmt80sSdXMz18NmPm"
);
 
$url = "https://api.twitter.com/1.1/search/tweets.json";
 
$requestMethod = "GET";

$getfield = '?q=#randomthoughts&result_type=recent&count=20000';
 

if ( $PRODUCTION ) {
    $twitter = new TwitterAPIExchange($settings);
    $response = $twitter
        ->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();
} else {
    $response = file_get_contents("test.json");
}


$parse_response = json_decode($response);

// echo "<pre>";
// var_export($parse_response);
// echo "</pre>";


$text_str = '';


foreach ($parse_response as $obj) {
    foreach ($obj as $value) {
        if ( ! is_object($value) ) continue; 
        $text_str = $text_str . $value->text;
    }
}

$text_str = preg_replace('/[^a-zA-Z]/s', ' ', $text_str);
$text_str = strtolower($text_str);

$remove_list = array(
    'randomthoughts',
    'shit',
    'fuck',
    'dildo',
    'iamjahblessed',
    'poop',
);

foreach ($remove_list as $value) {
    $text_str = str_replace($value, '', $text_str);
}

if ( $PRODUCTION ) {

    TextRazorSettings::setApiKey('ec4d485ae28034eea844c3dea57996a331e425ed896601b58d24418e');

    $textrazor = new TextRazor();

    $textrazor->addExtractor('words');

    $NLP_parsed = $textrazor->analyze($text_str);

    // echo "<pre>";
    // var_export($NLP_parsed);
    // echo "</pre>";

    $parts_of_speech = array();

    if (isset($NLP_parsed['response']['sentences'])) {
        foreach ($NLP_parsed['response']['sentences'] as $sentence) {

            if ( ! isset($sentence['words']) ) continue;
            foreach ($sentence['words'] as $word)  {

                $parts_of_speech[$word['partOfSpeech']][]= $word['token'];
            }
        }
    }


} else {

    include_once('text_array.php');
    $parts_of_speech = $parsed_text_array;


    $parts_of_speech_clean = array();
    foreach ($parts_of_speech as $key => $value) {
        $parts_of_speech_clean[$key] = array_unique($value);
        foreach ($parts_of_speech_clean as $index => $txt) {
            if ( count($txt) <= 2 ) {
                unset($parts_of_speech_clean[$index]);
            }
        }     
    } 
}


include_once('random_text.php');

var_dump($random_text_array);
exit;
// $counter = 10;
// while ($counter > 0){
//     get_sentence($parts_of_speech_clean);
//     $counter --;
// }

    get_sentence($parts_of_speech_clean);



function get_sentence($parts_of_speech_clean)
{    

    $nounA = get_word("NN", $random_text_array);
    $nounB = get_word("NN", $random_text_array);
    $verb = get_word("VBG", $parts_of_speech_clean);
    $adjective = get_word("JJ", $parts_of_speech_clean);
    $prep = get_word("IN", $parts_of_speech_clean);
    echo "<h2>A ".$adjective." ".$nounA." that is ".$verb."</h2>";
}

function get_word($key, $array){
    $length = count($array[$key]);
    $rand = get_rand($length);
    while ( empty($array[$key][$rand]) ) {
        $rand = get_rand($length);
    }
    return $array[$key][$rand]; 
}

function get_rand ($length) {
    return rand(0, $length);
}
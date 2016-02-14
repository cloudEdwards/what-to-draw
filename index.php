<?php

require_once('textrazor/TextRazor.php');

require_once('twitter-api-php/TwitterAPIExchange.php');
 


/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "2463626622-Na9ZpguldSb65yYPdMzQzASIE6OZdSyRXaw4xIz",
    'oauth_access_token_secret' => "H7ngiKbfXdTtpaR3iHTIiiLNP1vZXQ4FAq66lm5XE2QKJ",
    'consumer_key' => "rPs7XKYhQBjJDU9EPEI8C1XsP",
    'consumer_secret' => "WeLm8XK7cr3rkmvI4xuInPMBXUbRskz8CYmt80sSdXMz18NmPm"
);
 
$url = "https://api.twitter.com/1.1/search/tweets.json";
 
$requestMethod = "GET";
 
$getfield = '?q=#art&result_type=recent&count=20';
 
$twitter = new TwitterAPIExchange($settings);

/*
echo "<pre>";
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
echo "</pre>";

*/

$response = file_get_contents("test.json");
$parse_response = json_decode($response);

$text_str = '';


foreach ($parse_response as $obj) {
    foreach ($obj as $value) {
        if ( ! is_object($value) ) continue; 
        $text_str = $text_str . $value->text;
    }
}


TextRazorSettings::setApiKey('ec4d485ae28034eea844c3dea57996a331e425ed896601b58d24418e');

$textrazor = new TextRazor();

$textrazor->addExtractor('words');

$response = $textrazor->analyze($text_str);

// echo "<pre>";
// var_export($response);
// echo "</pre>";

$parts_of_speech = array();

if (isset($response['response']['sentences'])) {
    foreach ($response['response']['sentences'] as $sentence) {

        if ( ! isset($sentence['words']) ) continue;
        foreach ($sentence['words'] as $word)  {
            if ( $word['partOfSpeech'] == 'NNP' ) $parts_of_speech['nouns'][]= $word['token'];
            if ( $word['partOfSpeech'] == 'RB' ) $parts_of_speech['adverbs'][]= $word['token'];
            if ( $word['partOfSpeech'] == 'VB' ) $parts_of_speech['verbs'][]= $word['token'];
        }
    }
}


echo "<pre>";
var_export($parts_of_speech);
echo "</pre>";
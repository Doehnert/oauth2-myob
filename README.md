## MYOB Provider for OAuth 2.0 Client
This package provides MYOB OAuth 2.0 support for the PHP League's OAuth 2.0 Client.

## Installation
composer require doehnert/oauth2-myob
Obtaining an MYOB access key
To get a key you will need to be part of the MYOB Developer Program (https://developer.myob.com/program/become-a-myob-developer-partner/)
After you obtain an account, log in and click the "Developer" tab of my.myob.com.au
Click the Register App button to create a key
The redirect API must be exactly the same (including the http:// or https://) as the redirectUri below and is the URL of your application

## Usage
Usage is the same as The League's OAuth client, using \Doehnert\OAuth2\MYOBClient\Provider\MYOB as the provider

MYOB's APIs are throttled - the documented limit is 8 calls per second (and a large number per day) but the throttling appears to be buggy and you will likely find that you receive API Access Limit Exceeded errors no matter what limits you impose unfortunately. However you will be able to create an application that works fairly reliably if you follow the guidelines under Sample Application (below) qnd add a failsafe that detects the throttling, pauses and retries.

Instantiation
$provider = new \SprintDigital\OAuth2\MYOBClient\Provider\Myob([
    'clientId'                => 'yourId',          // The Key assigned to you by MYOB
    'clientSecret'            => 'yourSecret',      // The Secret assigned to you by MYOB
    'redirectUri'             => 'yourRedirectUri'  // The Redirect Uri you specified for your app on MYOB
]);
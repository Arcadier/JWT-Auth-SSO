<?php
require_once '../vendor/autoload.php'; // you can get this by downloading JWT PHP library using Composer
require_once '../sdk/ApiSdk.php'; //from Arcadier

$sdk       = new ApiSdk(); //class to facilitate usage of Arcadier APIs
$packageId = $sdk->getPackageID();
$baseUrl   = $sdk->getMarketplaceBaseUrl();

$token     = $_GET["ssoToken"]; //encrypted token from external platform, passed as GET query parameters
$returnUrl = $_GET["returnUrl"]; //optional

use Jose\Component\KeyManagement\JWKFactory;
use Jose\Easy\Load;

$jwk = JWKFactory::createFromKeyFile(
    '../key/secret_pem_file.pem'
);

$jwt = Load::jwe($token)               // We want to load and decrypt the token in the variable $token
    ->algs(['RSA-OAEP-256'])           // The key encryption algorithms allowed to be used
    ->encs(['A256GCM'])                // The content encryption algorithms allowed to be used
    ->exp()                            // Registered claim. Defined at encryption. Retrieves the expiration in NumericDate value. The expiration MUST be after the current date/time. 
    ->iat()                            // Registered claim. Defined at encryption. Retrieves the time the JWT was issued.
    ->nbf()                            // Registered claim. Defined at encryption. Retrieves the time before which the JWT MUST NOT be accepted for processing.
    ->aud($baseUrl)                    // Registered claim. Should be the same as what was defined at encryption
    ->iss('URL of External platform')  // Registered claim. Should be the same as what was defined at encryption
    ->sub('11111')                     // Registered claim. Should be the same as the one defined at encryption
    ->jti('11111')                     // Registered claim. Should be the same as the one defined at encryption
    ->key($jwk)                        // Key used to decrypt the token
    ->run()                            // Go!
;

echo json_encode($jwt->claims->all());

$exUserId  = $jwt->claims->get('user_code');   //private claim agreed to be used between Arcadier and external platform. Defined at encryption.
$userEmail = $jwt->claims->get('email');       //private claim agreed to be used between Arcadier and external platform. Defined at encryption.
$firstName = $jwt->claims->get('first_name');  //private claim agreed to be used between Arcadier and external platform. Defined at encryption.
$lastName  = $jwt->claims->get('last_name');   //private claim agreed to be used between Arcadier and external platform. Defined at encryption.

$sdk       = new ApiSdk();
$ssoToken  = $sdk->ssoToken($exUserId, $userEmail); //user identified or created using Arcadier SSO API

if (isset($ssoToken['AccessToken']) && !empty($ssoToken['AccessToken'])) {
    $userId = $ssoToken['AccessToken']['UserId'];
    $data   = ['FirstName' => $firstName, 'LastName' => $lastName];

    $sdk->updateUserInfo($userId, $data); //updates Arcadier User profile, if there's any new changes. Creates profile details if the user is new.

    error_log(json_encode($sdk->getUserInfo($userId, 'UserLogins')));
}

//in this case, a successful decryption & SSO will redirect the user to the an Arcadier URL while being authenticated with Arcadier
$code     = $ssoToken['SsoCode']; //from SSO API response
$location = $baseUrl . '/account/signintodomain?code=' . $code . '&returnUrl=' . $returnUrl;
header('Location: ' . $location);
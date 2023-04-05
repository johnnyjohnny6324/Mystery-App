<?php

namespace App\Controllers;

use App\TokenStore\TokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuthController extends BaseController
{
  public function signin()
  {
    $session = session();
	// Initialize the OAuth client
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => '',
        'clientSecret'            => '',
        'redirectUri'             => 'http://localhost:8000/callback',
        'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
        'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
        'urlResourceOwnerDetails' => '',
        'scopes'                  => 'openid profile offline_access user.read mailboxsettings.read calendars.readwrite',

        'response_type'         => 'code'
    ]);

    $authUrl = $oauthClient->getAuthorizationUrl();

    // Save client state so we can validate in callback
    $session->set(['oauthState' => $oauthClient->getState()]);

    // Redirect to AAD signin page
    return redirect()->to($authUrl);
        

	
  }

  public function callback()
  {
    $session = session();
	// Validate state
    $expectedState = $session->get('oauthState');
    $session->remove('oauthState');
    //$providedState = $request->getVar('state');

    if (!isset($expectedState)) {
      // If there is no expected state in the session,
      // do nothing and redirect to the home page.
      return redirect('/');
    }

    /*if (!isset($providedState) || $expectedState != $providedState) {
      return redirect('/')
        ->with('error', 'Invalid auth state')
        ->with('errorDetail', 'The provided auth state did not match the expected value');
    }*/

    // Authorization code should be in the "code" query param
    $authCode = $_GET['code'];
    if (isset($authCode)) {
      // Initialize the OAuth client
      $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => '',
        'clientSecret'            => '',
        'redirectUri'             => 'http://localhost:8000/callback',
        'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
        'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
        'urlResourceOwnerDetails' => '',
        'scopes'                  => 'openid profile offline_access user.read mailboxsettings.read calendars.readwrite',

        'response_type'         => 'code'
    ]);

      try {
	  // Make the token request
	  $accessToken = $oauthClient->getAccessToken('authorization_code', [
		'code' => $authCode
	  ]);

	  $graph = new Graph();
	  $graph->setAccessToken($accessToken->getToken());

	  $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
		->setReturnType(Model\User::class)
		->execute();

	  $tokenCache = new TokenCache();
	  $tokenCache->storeTokens($accessToken, $user);

	  return redirect('/');
	}
      catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        return redirect('/')
          ->with('error', 'Error requesting access token')
          ->with('errorDetail', $e->getMessage());
      }
    }

    return redirect('/')
      ->with('error', $request->query('error'))
      ->with('errorDetail', $request->query('error_description'));

  }
  

}

<?php 

/*
 * login.php file for the Individual Project (University of Sussex 2023) 
 * Author: William Moss (235319)
 *
 * Enables login via Microsoft Azure Directory
 */

    // Start PHP Session 
    session_start();

    require_once ('../vendor/autoload.php');
    require_once ('../src/inc/utilities.php');

    // Load .env file
    $dotenv = Dotenv\Dotenv::createImmutable('../config');
    $dotenv->load();
    $dotenv->required(['CLIENT_ID', 'CLIENT_SECRET', 'REDIRECT_URI', 'TENANT_ID']);

    echo $_ENV['CLIENT_ID'];
    echo $_ENV['CLIENT_SECRET'];
    echo $_ENV['REDIRECT_URI'];
    exit();
    
    $provider = new TheNetworg\OAuth2\Client\Provider\Azure([
        'clientId'          => $_ENV['CLIENT_ID'],
        'clientSecret'      => $_ENV['CLIENT_SECRET'],
        'redirectUri'       => $_ENV['REDIRECT_URI'],
        'scopes'            => ['openid'],
        'defaultEndPointVersion' => '2.0'
    ]);

    // Set to use v2 API, skip the line or set the value to Azure::ENDPOINT_VERSION_1_0 if willing to use v1 API
    $provider->defaultEndPointVersion = TheNetworg\OAuth2\Client\Provider\Azure::ENDPOINT_VERSION_2_0;

    $baseGraphUri = $provider->getRootMicrosoftGraphUri(null);
    $provider->scope = 'openid profile ' . $baseGraphUri . '/User.Read';

    if (isset($_GET['code']) && isset($_SESSION['OAuth2.state']) && isset($_GET['state'])) {
        if ($_GET['state'] == $_SESSION['OAuth2.state']) {
            unset($_SESSION['OAuth2.state']);

            // Try to get an access token (using the authorization code grant)
            /** @var AccessToken $token */
            $token = $provider->getAccessToken('authorization_code', [
                'scope' => $provider->scope,
                'code' => $_GET['code'],
            ]);

            // Verify token
            // Save it to local server session data
            $_SESSION['code'] =  $token->getToken();

            // Connect to database
            try {
                $con = mysqliConnect();
                if ($con->connect_error) {
                    header('Location: index.html?errorcode=3');
                    exit();
                } 
            } catch (Exception $e) {
                header('Location: index.html?errorcode=4');
                exit();
            }

            // Checks if user email is within the database
            $email = $provider->get($provider->getRootMicrosoftGraphUri($token) . '/v1.0/me', $token)['userPrincipalName'];

            if (userExistsByEmail($email, $con)) {

                // Logs user in and redirects to homepage.php
                $new_session_id = session_create_id();
                $_SESSION['new_session_id'] = $new_session_id;
                $_SESSION['destroyed'] = time();
                session_commit();
                session_id($new_session_id);
                session_start();
                unset($_SESSION['destroyed']);
                unset($_SESSION['new_session_id']);

                // Sets session variables to show a successful login
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['email'] = $email;
                session_write_close();

                header('Location: homepage.php');
                exit();

            } else {
                // User is not authorized to access this app. Redirect to index with message
                header('Location: index.html?errorcode=1');
                exit();
            }
        
            exit();

        } else {
            // OAuth2.0 State mismatch. Redirect to index with message
            header('Location: index.html?errorcode=2');
            exit();
        }
    } else {
        
        $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $provider->scope]);

        $_SESSION['OAuth2.state'] = $provider->getState();

        header('Location: ' . $authorizationUrl);

        exit();
    }

?>
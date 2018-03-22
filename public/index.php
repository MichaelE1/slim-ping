<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;

// Reroute dynamic post requests to a static path
$app->add(function ($request, $response, $next) {
	if ($request->isPost() && ($request->getUri()->getPath() != "/clear_data")) {
		  
		$uri = $request->getUri();
		$postData = $request->getUri()->getPath();
		$request = $request->withUri($uri->withPath('addentry'));
		$request = $request->withAttribute('url', $postData);	
	}

		$response = $next($request, $response);
		return $response;
});

require_once('/app/api/pingsolution.php');

$app->run();

?>
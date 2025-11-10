<?php

require_once dirname(__DIR__) . '/src/bootstrap.php';

include '../connection.php';
use app\controller\Snippet;

if (strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    $request_vars = $_GET;
} else {
    parse_str(file_get_contents("php://input"), $request_vars);
}

$sentData = @$request_vars['request'];

if (!isset($sentData) || empty($sentData)) {
    http_response_code(406);
    exit('Invalid request, missing parameter.');
}

try {
    $request = is_string($sentData) ? json_decode($sentData) : (object) $sentData;

    $path = explode('@', $request->handler);
    if (count($path) < 2) {
        throw new Exception('Class method not provided');
    }

    $class = trim($path[0]);
    $method = trim($path[1]);
    // $object = new $class($container->get('database')->conn);
    $object = new $class($link);

    $response = $object->$method(); // Call the method dynamically
    echo json_encode($response); // Return data as JSON

} catch (Error $e) {
    Snippet::file($e);
} catch (Exception $e) {
    Snippet::file($e);
}

// if (strtolower($_SERVER['REQUEST_METHOD']) === 'get')
// 	$request_vars = $_GET;
// else
// 	parse_str(file_get_contents("php://input"),$request_vars);

// $sentData = @$request_vars['request'];

// if ( ! isset($sentData) || empty($sentData) ) {
// 	http_response_code(406);
// 	exit('Invalid request, missing parameter.');
// }

// try {
// 	$request = is_string($sentData) ? json_decode($sentData) : (object) $sentData;

// 	$path = explode('@', $request->handler);
// 	if ( count($path) < 2 )
// 		throw new Exception('Class method not provided');

// 	$class = "Gds\\" . trim($path[0]);
// 	$method = trim($path[1]);
// 	$object = new $class($container->get('database')->conn);

// 	echo Snippet::output($object->caller($method, @$request->argument));

// } catch (Error $e) {
//     Snippet::file($e);
// }
//     catch (Exception $e) {
//         Snippet::file($e);
    // }

	
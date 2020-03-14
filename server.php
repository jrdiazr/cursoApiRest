<?php
// HTTP AUTHENTICATION
// $user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER']:'';
// $pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW']:'';

// if ($user !== 'mauro' || $pwd !== '1234'){
//   die;
// }

// if (
//   !array_key_exists('HTTP_X_HASH', $_SERVER) ||
//   !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
//   !array_key_exists('HTTP_X_UID', $_SERVER)
// ) {  
//   die;
// }
// list($hash, $uid, $timestamp) = [
//   $_SERVER['HTTP_X_HASH'],
//   $_SERVER['HTTP_X_UID'],
//   $_SERVER['HTTP_X_TIMESTAMP'],
// ];

// $secret = 'Sh!! No se lo cuentes a nadie!';

// $newHash = sha1($uid.$timestamp.$secret);

// if ($newHash!==$hash){
//   die;
// }

if (!array_key_exists('HTTP_X_TOKEN', $_SERVER)){
  die;
}

$url = 'http://localhost:8001';

$ch = curl_init($url);
curl_setopt(
  $ch,
  CURLOPT_HTTPHEADER,
  [
    "X-TOKEN: {$_SERVER['HTTP_X_TOKEN']}"
  ]
);
curl_setopt(
  $ch,
  CURLOPT_RETURNTRANSFER,
  true
);
$ret = curl_exec($ch);

if ($ret !== 'true') {
  die;
}
// Definimos los recursos disponibles
$allowedResourceTypes = [
  'books',
  'authors',
  'genres',
];

//validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];

if (!in_array($resourceType, $allowedResourceTypes)) {
  die;
}
// Defino los recursos

$books = [
  1 => [
    'titulo' => 'Libro 1',
    'id_autor' => 2,
    'id_genero' => 2,
  ],
  2 => [
    'titulo' => 'Libro 2',
    'id_autor' => 2,
    'id_genero' => 2,
  ],
  3 => [
    'titulo' => 'Libro 3',
    'id_autor' => 2,
    'id_genero' => 2,
  ],
];

header('Content-Type: application/json');

// Levantamos el ID del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id']: '';
// Generamos la respuesta asumiendo que el pedido es correcto
switch(strtoupper($_SERVER['REQUEST_METHOD'])) {
  case GET:
    if (empty($resourceId))
      echo json_encode($books);
    else {
      if (array_key_exists($resourceId, $books))
        echo json_encode($books[$resourceId]);
    }    
    break;
  case POST:
     $json = file_get_contents('php://input');

     $books[] = json_decode ($json, true);

     //echo array_keys($books) [ count($books) -1]; 
     echo json_encode($books);

    break;
  case PUT:
    // validamos que el recurso buscado exista
    if(!empty($resourceId) && array_key_exists($resourceId, $books)){
      $json = file_get_contents('php://input');
      $books[$resourceId] = json_decode ($json, true);
      echo json_encode($books);
    }
    break;
  case DELETE:
    if (!empty($resourceId && array_key_exists($resourceId, $books))){
      unset($books[$resourceId]) ;
      echo json_encode($books);
    }
    break;
}

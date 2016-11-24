<?php
ini_set('max_execution_time', 0);

require './vendor/autoload.php';
require './config.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$appEnv = getenv('appEnv');
if($appEnv !== 'production') $appEnv = 'development';
$config = $config[$appEnv];

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
  $db = new medoo($c['settings']['db']);
  return $db;
};

$container['cache'] = function ($c) {
  $m = new Memcached();
  $m->addServer('localhost', 11211);
  return $m;
};

$container['config'] = function($c){
  return $c;
};

//Middleware

$app->add(function ($request, $response, $next) {

  $response = $next($request, $response);

  return $response->withHeader('Access-Control-Allow-Origin', '*')->withAddedHeader('Access-Control-Allow-Headers', 'Content-Type')->withAddedHeader('Access-Control-Allow-Methods', 'GET,POST,DELETE');
});

//Routes goes here...

//When we create a new version, move old one in here.
/*$app->group('/v1', function(){

});*/

$app->get('/', function (Request $request, Response $response, $args) {
  $body = $response->getBody();
  $body->write('You have successfully authorized, now go have fun with the API :)');
  return $response;
});

$app->post('/user/register', function(Request $request, Response $response, $args){
  $data = $request->getParsedBody();

  if(isset($data['username']) && isset($data['email']) && isset($data['password1']) && isset($data['password2'])){
    if($data['password1'] != $data['password2']) return $response->withJson(array("error" => "The passwords does not match"));
    if($this->db->has('users', ['email' => $data['email']])) return $response->withJson(array("error" => "A user with that email already exists."));
    $uid = $this->db->insert('users', [
      'username' => $data['username'],
      'email' => $data['email'],
      'password' => hash('sha512', $data['password1'] . 'salt22')
    ]);
    if($uid){
      $token = md5(uniqid(rand(), true));
      $this->db->insert('sessions', ['token' => $token, 'user_id' => $uid]);

      return $response->withJson(['username' => $data['username'], 'id' => $uid, 'token' => $token]);
    }
    return $response->withJson(array("error" => "Something went wrong, please try again."));
  }else{
    return $response->withJson(array("error" => "Please fill in all fields."));
  }

});

$app->post('/user/login', function(Request $request, Response $response, $args){
  $data = $request->getParsedBody();

  if(isset($data['username']) && isset($data['password'])){
    $user = $this->db->get('users', ['id', 'username'], ['AND' => ['username' => $data['username'], 'password' => hash('sha512', $data['password'] . 'salt22')]]);
    if($user !== false){
      $token = md5(uniqid(rand(), true));
      $this->db->insert('sessions', ['token' => $token, 'user_id' => $user['id']]);

      return $response->withJson(['username' => $user['username'], 'id' => $user['id'], 'token' => $token]);
    }
    return $response->withJson(array("error" => "Wrong username or password"));
  }else{
    return $response->withJson(array("error" => "Please fill in all fields"));
  }

});

$app->get('/user/check/{token}', function(Request $request, Response $response, $args){

  $token = $this->db->get('sessions', ['token', 'user_id'], ['token' => $args['token']]);
  if($token !== false){
    return $response->withJson(array("user" => $this->db->get('users', ['id', 'username'])));
  }

  return $response->withJson(array("user" => false));
});

$app->get('/posts', function(Request $request, Response $response, $args){

  $result = $this->db->select('posts',
    ['[>]users' => ['user_id' => 'id']],
    ['posts.id', 'posts.content', 'posts.title', 'users.username']
  );

  return $response->withJson(array("result" => $result));
});

$app->post('/posts', function(Request $request, Response $response, $args){
  $data = $request->getParsedBody();

  if(isset($data['user_id']) && isset($data['title']) && isset($data['content'])){
    $this->db->insert('posts', ['user_id' => $data['user_id'], 'title' => $data['title'], 'content' => $data['content']]);
  }else{
    return $response->withJson(array("error" => "Please fill in all fields"));
  }

});

$app->run();
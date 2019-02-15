<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
Use \Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

//rota para home page
$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

//rota para pagina de Admin
$app->get('/admin', function() {

	User::verifyLogin(); //Verifica se a pessoa está logada
    
	$page = new PageAdmin();

	$page->setTpl("index");

});

//rota para pagina de Admin/login
$app->get('/admin/login', function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

//rota para o POST do login
$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]); //metodo p receber o post do formulario login, e post da senha

	header("Location: /admin");
	exit;

});

//rota que DESLOGA o usuario.
$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;
	
});

//Rota que lista todos os usuarios
$app->get("/admin/users", function(){

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(

		"users"=>$users
	));
});


//Rota que faz o CREATE do CRUD
$app->get("/admin/users/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

//rota que DELETA o usuário.
$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;
	
});

//Rota que faz o EDIT do CRUD
$app->get("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(

		"user"=>$user->getValues()
	));
});

//Rota que SALVA o CREATE do CRUD.
$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$user = new User();

//Se o inadmin for adminsitrador terá valor 1, caso não, será 0.
 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
 	exit;

});

//Rota que SALVA o CRUD do EDIT.
$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	//Mesma validação para saber se está checado ou não o checkbox do Admin.
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	//Pega da pagina o registro para fazer o UPDATE.
	$user->get((int)$iduser);
	
	//Seta os dados para preparar para fazer o UPDATE.
	$user->setData($_POST);
	//Executa o UPDATE.
	$user->update();

	header("Location: /admin/users");
 	exit;
});

//Rota da tela que faz o ESQUECI A SENHA.
$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");
	
});

//Envio do email de alteração de senha
$app->post("/admin/forgot", function() {

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

//Pagina do EMAIL DE ESQUECIMENTO ENVIADO COM SUCESSO.
$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(

		"name"=>$user["desperson"],
		"code"=>$_GET["code"]

	));
});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

$app->run();

 ?>
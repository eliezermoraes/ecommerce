<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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

?>
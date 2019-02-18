<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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

?>
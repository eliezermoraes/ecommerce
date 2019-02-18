<?php

use \Hcode\PageAdmin;

//rota para home page
$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

?>
<?php

use \Hcode\Page;
use \Hcode\Model\Product;

//rota para home page
$app->get('/', function() {

	$products = Product::listAll();
    
	$page = new Page();

	$page->setTpl("index", [
		'products'=>Product::checkList($products)
	]);

});

?>
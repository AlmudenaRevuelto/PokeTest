<?php

use PokeTest\Core\View;

get_header();

// No server-side data is needed here; all content is loaded from PokeAPI via JS.
$data = [];

$view = new View(get_template_directory() . '/views');
$view->render('front-page.twig', $data);


<?php
namespace PokeTest\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {

    private $twig;

    public function __construct($template_path = '') {
        $loader = new FilesystemLoader($template_path ?: get_template_directory() . '/views');
        $this->twig = new Environment($loader, [
            'cache' => false, // poner true en producción
            'debug' => true,
        ]);
    }

    public function render($template, $data = []) {
        echo $this->twig->render($template, $data);
    }
}
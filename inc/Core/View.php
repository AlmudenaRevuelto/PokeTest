<?php
namespace PokeTest\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * View
 *
 * Wrapper around Twig environment for rendering templates.
 */
class View {

    private $twig;

    /**
     * View constructor.
     *
     * @param string $template_path Directory path where Twig templates are located.
     */
    public function __construct($template_path = '') {
        $loader = new FilesystemLoader($template_path ?: get_template_directory() . '/views');
        $this->twig = new Environment($loader, [
            'cache' => false, // set to true in production environments
            'debug' => true,
        ]);
    }

    /**
     * Render a template with the given data.
     *
     * @param string $template Twig template filename.
     * @param array $data Data context passed into the template.
     */
    public function render($template, $data = []) {
        echo $this->twig->render($template, $data);
    }
}
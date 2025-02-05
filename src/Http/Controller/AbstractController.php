<?php

namespace Pachuli\Web\Http\Controller;

use Pachuli\Web\Core\Config;
use Pachuli\Web\Http\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    public ?string $layout = null;
    private ?Environment $loader = null;
    private string $parentView;
    private bool $useTwig;

    public function __construct()
    {

        try {

            $this->parentView = ROOT . "/app/views";

            $this->useTwig = Config::get("app")["useTwig"];

            if ($this->useTwig) {
                if (!$this->loader) {
                    $templates = new FilesystemLoader([ROOT . "/app/views", ROOT . "/app/layouts"]);
                    $this->loader = new Environment($templates);
                }
            }

        } catch (\Exception $e) {
            print $e->getMessage();
        }
    }

    public function render(string $nameView, ?array $params = []): Response
    {

        try {
            $content = ($this->useTwig) ? $this->loader->render($nameView, $params) : $this->renderPhp($nameView);
            return new Response($content);
        } catch (LoaderError|SyntaxError|RuntimeError $e) {
            print $e->getMessage();
        }

        return new Response();
    }

    public function renderPhp(string $nameView, ?array $params = []): Response
    {
        try {
            $content = $this->renderPlainView($nameView, $params);
            return new Response($content);
        } catch (\Exception $e) {
            print $e->getMessage();
        }

        return new Response();
    }

    private function renderPlainView(string $nameView, ?array $params = []): string
    {
        $viewPath = $this->parentView . DIRECTORY_SEPARATOR . ltrim($nameView, '/');

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("La vista '{$nameView}' no existe en {$this->parentView}");
        }

        // Extrae las variables del array asociativo para que estén disponibles en la vista
        extract($params);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}
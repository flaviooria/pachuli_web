<?php

namespace App\controllers\home;

use Pachuli\Web\Http\Controller\AbstractController;
use Pachuli\Web\Http\Response;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        return $this->render("home/index.html.twig");
    }
}
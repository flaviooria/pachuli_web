<?php

namespace App\controllers\profiles;

use Pachuli\Web\Http\Controller\AbstractController;
use Pachuli\Web\Http\Response;

class ProfilesController extends AbstractController
{
    public static function edit(int $id): void
    {
        echo "Edit Profile: $id";
    }

    public function index(): Response
    {
        return $this->render("/profiles/index.php");
    }
}
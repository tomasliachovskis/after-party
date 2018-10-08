<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthController.
 */
class AuthController extends Controller
{
    /**
     * @Route("/login", name="login")
     *
     * @return Response
     */
    public function loginAction()
    {
        return $this->render(
            '@app/login/login.html.twig'
        );
    }
}

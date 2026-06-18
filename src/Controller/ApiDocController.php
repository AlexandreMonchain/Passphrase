<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiDocController extends AbstractController
{
    #[Route('/api/documentation', name: 'api_documentation')]
    public function documentation(): Response
    {
        return $this->render('api/documentation.html.twig');
    }
}

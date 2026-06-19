<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show(string $slug): Response
    {
        $baseDir  = realpath(__DIR__ . '/../../content/blog');
        $filePath = realpath($baseDir . '/' . $slug . '.htm');

        if ($filePath === false || !str_starts_with($filePath, $baseDir . DIRECTORY_SEPARATOR)) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('blog/show.html.twig', [
            'content' => file_get_contents($filePath),
            'slug'    => $slug,
        ]);
    }
}

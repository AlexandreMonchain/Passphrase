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
        // Chemin vers l'article
        $filePath = __DIR__ . '/../../content/blog/' . $slug . '.htm';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Page not found');
        }

        // Lire le contenu du fichier
        $htmlContent = file_get_contents($filePath);

        // Rendre la page Twig avec le contenu HTML
        return $this->render('blog/show.html.twig', [
            'content' => $htmlContent,
            'slug' => $slug,
        ]);
    }
}

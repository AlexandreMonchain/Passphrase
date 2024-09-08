<?php

namespace App\Controller;

use League\CommonMark\CommonMarkConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show(string $slug): Response
    {
        // Chemin vers le fichier Markdown
        $filePath = __DIR__ . '/../../content/blog/' . $slug . '.md';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Page not found');
        }

        // Lire le contenu du fichier Markdown
        $markdownContent = file_get_contents($filePath);

        // Initialiser le convertisseur Markdown -> HTML
        $converter = new CommonMarkConverter();

        // Convertir le Markdown en HTML
        $htmlContent = $converter->convert($markdownContent);

        // Rendre la page Twig avec le contenu HTML
        return $this->render('blog/show.html.twig', [
            'content' => $htmlContent,
            'slug' => $slug,
        ]);
    }
}

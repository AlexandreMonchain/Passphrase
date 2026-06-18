<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HtmlSanitizeExtension extends AbstractExtension
{
    /**
     * Tags HTML sûrs autorisés pour affichage du contenu blog
     */
    private const ALLOWED_TAGS = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><a><img><div><span><code><pre>';

    public function getFilters(): array
    {
        return [
            new TwigFilter('sanitize_html', [$this, 'sanitizeHtml']),
        ];
    }

    /**
     * Sanitize HTML content en gardant seulement les tags sûrs
     * Élimine les scripts, event handlers et autres contenus dangereux
     */
    public function sanitizeHtml(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Supprimer les scripts et event handlers dangereux
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/on\w+\s*=\s*["\']?[^"\'>]*["\']?/i', '', $html);
        
        // 2. Garder seulement les tags sûrs
        $html = strip_tags($html, self::ALLOWED_TAGS);
        
        // 3. Nettoyer les URLs dans les liens (éviter javascript:)
        $html = preg_replace_callback('/<a\s+href=(["\']?)([^"\'>\s]+)\1/i', function($matches) {
            $url = $matches[2];
            if (stripos($url, 'javascript:') === 0 || stripos($url, 'data:') === 0) {
                return '<a href="#"';
            }
            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
        }, $html);
        
        // 4. Nettoyer les URLs dans les images
        $html = preg_replace_callback('/<img\s+src=(["\']?)([^"\'>\s]+)\1/i', function($matches) {
            $url = $matches[2];
            if (stripos($url, 'javascript:') === 0 || stripos($url, 'data:') === 0) {
                return '<img src="about:blank"';
            }
            return '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
        }, $html);
        
        return $html;
    }
}

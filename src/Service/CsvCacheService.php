<?php
    // src/Service/CsvCacheService.php

    namespace App\Service;

    use Symfony\Contracts\Cache\CacheInterface;
    use Symfony\Contracts\Cache\ItemInterface;

    class CsvCacheService
    {
        private $cache;
        private $csvPath;

        public function __construct(CacheInterface $cache, string $projectDir)
        {
            $this->cache = $cache;
            // Chemin vers le fichier CSV
            $this->csvPath = $projectDir . '/src/Data/db.csv';
        }

        public function getCsvData(): array
        {
            // Utiliser le cache pour �viter de lire le fichier � chaque fois
            return $this->cache->get('csv_data', function (ItemInterface $item) {
                // Charger le fichier CSV ici
                $item->expiresAfter(3600); // 1 heure d'expiration du cache
                return $this->loadCsv();
            });
        }

        private function loadCsv(): array
        {
            $mots = [];
            if (($handle = fopen($this->csvPath, 'r')) !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $mots[] = $row[0]; // Ajouter chaque mot dans le tableau
                }
                fclose($handle);
            }
            return $mots;
        }
    }
?>
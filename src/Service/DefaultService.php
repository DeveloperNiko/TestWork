<?php

namespace App\Service;

use App\Entity\Stats;
use App\Entity\URL;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultService
{
    private EntityManagerInterface $entityManager;
    private \DateTime $now;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->now = new \DateTime();
    }

    /**
     * @param string $currentUrl
     * @return string[]|void|null
     */
    public function checkShortUrl(string $currentUrl)
    {
        $url = $this->entityManager->getRepository(URL::class)
            ->findOneBy(['short_url' => $currentUrl]);
        $data = null;

        if ($url) {
            $stats = $url->getStats();

            if (!$stats) {
                $stats = new Stats();
                $url->setStats($stats);
                $stats->setUrl($url);
                $this->entityManager->persist($stats);
            }

            $url->incrementVisits();

            $this->entityManager->persist($url);
            $this->entityManager->flush();


            if ($url->getExpiryDate()->format('Y-m-d H:i:s') < $this->now->format('Y-m-d H:i:s')) {
                header('Location: '.$url->getOriginalUrl());
                exit;
            } else {
                $data = [
                    'title' => 'Успіх',
                    'h1' => 'Ура, такий короткий URL існує. Кількість переходів :  ' . $url->getStats()->getVisits() .' Час життя до ' . $url->getExpiryDate()->format('Y-m-d H:i:s'),
                ];
            }


        }

        return $data;

    }

    /**
     * @param string $currentUrl
     * @return string[]
     */
    public function checkOriginalUrl(string $currentUrl): array
    {
        $currentUrl = '/' . $currentUrl;
        $url = $this->entityManager->getRepository(URL::class)
            ->findOneBy(['original_url' => $currentUrl]);

        $result = [
            'title' => 'Помилка',
            'h1' => 'Для цього URL не існує короткий URL',
        ];

        if ($url) {
            $result = [
                'title' => 'Успіх',
                'h1' => 'Для цього URL існує короткий URL. Кількість переходів :  ' . $url->getStats()->getVisits() .' Час життя до ' . $url->getExpiryDate()->format('Y-m-d H:i:s'),
            ];
        }

        return $result;
    }

}
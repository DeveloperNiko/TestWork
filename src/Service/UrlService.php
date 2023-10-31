<?php

namespace App\Service;

use App\Entity\URL;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UrlService
{
    const  LENGTH_URL = 6;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Візьмемо основні дані для відтворювання сторінки
     * @return string[]
     */
    public function getIndexData(): array
    {
        return [
            'title' => 'URL minify',
            'h1' => 'Привіт. Заповни форму',
            'url_action' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/url/create'
        ];
    }


    /**
     * Генерація короткого посилання
     * @param Request $request
     * @return array
     */
    public function getShortLink(Request $request): array
    {
        $postData = $request->request->all();

        $timestamp = strtotime($postData['date']);
        $expiryDate = new \DateTime("@$timestamp");

        $url = $this->formatUrl($postData['url']);
        $hash = hash('crc32', $url);

        $shortUrl = substr($hash, 0, self::LENGTH_URL);
        $preparedData = [
            'original_url' => $url,
            'expiry_date' => $expiryDate,
            'short_url' => $shortUrl,
        ];


        return $this->setData($preparedData);
    }

    /**
     * Зберігаємо посилання до БД
     * @param $data
     * @return array
     */
    public function setData($data): array
    {
        $url = $this->entityManager->getRepository(URL::class)
            ->findOneBy(['original_url' => $data['original_url'], 'short_url' => $data['short_url']]);

        $message = 'Ви успішно оновили дату для ' . $data['original_url'];
        if (!$url) {
            $url = new URL();
            $message = 'Ви успішно додали новий URL ' . $data['original_url'];
        }

        $url->setOriginalUrl($data['original_url']);
        $url->setExpiryDate($data['expiry_date']);
        $url->setShortUrl($data['short_url']);

        $this->entityManager->persist($url);
        $this->entityManager->flush();

        return [
            'short_url' => $url->getShortUrl(),
            'expiry_date' => $url->getExpiryDate(),
            'message' => $message,
        ];
    }

    /**
     * Форматуємо посилання у зручний формат
     * @param string $url
     * @return array|string
     */
    public function formatUrl(string $url): array|string
    {
        $clearUrl = str_replace($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '', $url);
        $clearUrl = rtrim($clearUrl, '/');
        if (!str_starts_with($clearUrl, '/')) {
            $clearUrl = '/' . $clearUrl;
        }

        return $clearUrl;
    }
}
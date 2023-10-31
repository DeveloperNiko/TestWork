<?php

namespace App\Controller;

use App\Service\DefaultService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends AbstractController
{

    private $service;

    public function __construct(DefaultService $service)
    {
        $this->service = $service;
    }

    public function index(string $url): Response
    {
        $data = $this->service->checkShortUrl($url);
        if (!$data) {
            $data = $this->service->checkOriginalUrl($url);
        }
        return $this->render('base.html.twig', $data);
    }

    public function catchAll(string $url): Response
    {
        $data = 'Some data';

        // Примените шаблон с вашими данными
        return $this->render('base.html.twig', [
            'data' => $data
        ]);
    }
}
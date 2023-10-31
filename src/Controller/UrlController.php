<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UrlService;
use Doctrine\ORM\EntityManagerInterface;

class UrlController extends AbstractController
{
    private $service;

    public function __construct(UrlService $service)
    {
        $this->service = $service;
    }

    #[Route('/', name: 'app_url')]
    public function index(): Response
    {
        return $this->render('url/index.html.twig', $this->service->getIndexData());
    }

    #[Route('/url/create', name: 'url_create', methods: ['POST'])]
    public function create(Request $request)
    {
        $result = $this->service->getShortLink($request);

        return new JsonResponse($result);
    }

}

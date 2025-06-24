<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function MongoDB\BSON\toJSON;


final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/api/categorie/{id}', methods: ['GET', 'HEAD'])]
    public function getCategorie(EntityManagerInterface $entityManager, int $id): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if(!$categorie){
            throw $this->createNotFoundException(
                'No categorie found for id '.$id
            );
        }
        return new Response('Categorie : '.$categorie->getNom(), 200, []);
    }
    #[Route('/api/categories', methods: ['GET', 'HEAD'])]
    public function getAllCategorie(EntityManagerInterface $entityManager): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->findAll();
        if(!$categorie) {
            throw $this->createNotFoundException(
                'No categories found  '
            );
        }
        $resp = new JsonResponse();
        //dd($categorie);
        //return new Response('Categories : '.$categorie, 200, []);
        if (is_array($categorie)) {
            ->setResponse(new JsonResponse($categorie);
        }
        //return new JsonResponse($categorie, 200, []);
    }
}

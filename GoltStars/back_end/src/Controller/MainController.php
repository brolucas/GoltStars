<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
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
    public function getAllCategorie(EntityManagerInterface $entityManager): JsonResponse
    {
        $categorie = $entityManager->getRepository(Categorie::class)->findAll();
        if(!$categorie) {
            throw $this->createNotFoundException(
                'No categories found  '
            );
        }
        $arr = array();
        foreach ($categorie as $c) {
           $Nom= $c->getNom();
           $id = $c->getId();
           $arr[$id] = $Nom;
        }
        //dd($categorie);
        //return $categorie;
        return new JsonResponse($arr);
    }

    #[Route('/api/products', methods: ['GET', 'HEAD'])]
    public function getAllProduct(EntityManagerInterface $entityManager): JsonResponse
    {
        $produit = $entityManager->getRepository(Produit::class)->findAll();
        if(!$produit) {
            throw $this->createNotFoundException(
                'No categories found  '
            );
        }
        $arr = array();
        foreach ($produit as $p) {
            $Nom= $p->getNom();
            $id = $p->getId();
            $prix = $p->getPrix();
            $url = $p->getUrl();
            $arr2=array();
            $arr2["Id"] = $id;
            $arr2["Nom"] = $Nom;
            $arr2["Prix"] = $prix;
            $arr2["Url"] = $url;
            $arr[] = $arr2;
        }
        return new JsonResponse($arr);
    }
}

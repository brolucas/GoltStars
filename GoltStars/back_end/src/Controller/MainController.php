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
        $produit = $entityManager->getRepository(\App\Entity\Produit::class)->findAll();
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
            $categories = [];
            foreach ($p->getCategories() as $cat) {
                $categories[] = [
                    'id' => $cat->getId(),
                    'nom' => $cat->getNom()
                ];
            }
            $arr2=array();
            $arr2["Id"] = $id;
            $arr2["Nom"] = $Nom;
            $arr2["Prix"] = $prix;
            $arr2["Url"] = $url;
            $arr2["Categories"] = $categories;
            $arr[] = $arr2;
        }
        return new JsonResponse($arr);
    }

    #[Route('/api/product', name: 'add_product', methods: ['POST'])]
    public function addProduct(EntityManagerInterface $entityManager, \Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || empty($data['nom']) || !isset($data['prix']) || empty($data['url'])) {
            return new JsonResponse(['error' => 'Champs nom, prix ou url manquants'], 400);
        }
        $produit = new \App\Entity\Produit();
        $produit->setNom($data['nom']);
        $produit->setPrix((float)$data['prix']);
        $produit->setUrl($data['url']);
        // Ajout des catégories si présentes
        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $catId) {
                $categorie = $entityManager->getRepository(\App\Entity\Categorie::class)->find($catId);
                if ($categorie) {
                    $produit->addCategorie($categorie);
                }
            }
        }
        $entityManager->persist($produit);
        $entityManager->flush();
        return new JsonResponse([
            'message' => 'Produit ajouté',
            'id' => $produit->getId()
        ], 201);
    }

    #[Route('/api/product/{id}', name: 'update_product', methods: ['PATCH'])]
    public function updateProduct(int $id, EntityManagerInterface $entityManager, \Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }
        if (!empty($data['nom'])) $produit->setNom($data['nom']);
        if (isset($data['prix'])) $produit->setPrix((float)$data['prix']);
        if (!empty($data['url'])) $produit->setUrl($data['url']);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Produit modifié']);
    }

    #[Route('/api/product/{id}', name: 'get_product', methods: ['GET'])]
    public function getProductById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }
        $categories = [];
        foreach ($produit->getCategories() as $cat) {
            $categories[] = [
                'id' => $cat->getId(),
                'nom' => $cat->getNom()
            ];
        }
        return new JsonResponse([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'url' => $produit->getUrl(),
            'categories' => $categories
        ]);
    }

    #[Route('/api/categorie', name: 'add_categorie', methods: ['POST'])]
    public function addCategorie(EntityManagerInterface $entityManager, \Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || empty($data['nom'])) {
            return new JsonResponse(['error' => 'Champ nom manquant'], 400);
        }
        $categorie = new \App\Entity\Categorie();
        $categorie->setNom($data['nom']);
        $entityManager->persist($categorie);
        $entityManager->flush();
        return new JsonResponse([
            'message' => 'Catégorie ajoutée',
            'id' => $categorie->getId()
        ], 201);
    }
}

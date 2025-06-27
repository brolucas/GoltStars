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
use OpenApi\Annotations as OA;


final class ProduitController extends AbstractController
{

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
        return new JsonResponse($arr);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Lister tous les produits avec leurs catégories",
     *     @OA\Response(response=200, description="Liste des produits")
     * )
     */
    #[Route('/api/products', methods: ['GET', 'HEAD'])]
    public function getAllProduct(EntityManagerInterface $entityManager): JsonResponse
    {
        $produit = $entityManager->getRepository(\App\Entity\Produit::class)->findAll();
        if(!$produit) {
            throw $this->createNotFoundException(
                'No products found  '
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

    /**
     * @OA\Post(
     *     path="/api/product",
     *     summary="Ajouter un produit avec catégories",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="prix", type="number"),
     *                 @OA\Property(property="url", type="string"),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Produit ajouté"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/product/{id}",
     *     summary="Modifier un produit",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="prix", type="number"),
     *                 @OA\Property(property="url", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Produit modifié"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/product/{id}",
     *     summary="Récupérer un produit par son id avec ses catégories",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Produit trouvé"),
     *     @OA\Response(response=404, description="Produit non trouvé")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/categorie",
     *     summary="Ajouter une catégorie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="nom", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie ajoutée"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
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
    #[Route('/api/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id, EntityManagerInterface $em): JsonResponse
    {
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $em->remove($produit);
        $em->flush();

        return new JsonResponse(['message' => 'Produit supprimé avec succès'], 204);
    }

    #[Route('/api/products/categorie/{id}', name: 'get_products_by_categorie', methods: ['GET'])]
    public function getProductsByCategorie(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            return new JsonResponse(['error' => 'Catégorie non trouvée'], 404);
        }
        $produits = $categorie->getProduits();
        $result = [];
        foreach ($produits as $p) {
            $cats = [];
            foreach ($p->getCategories() as $cat) {
                $cats[] = [
                    'id' => $cat->getId(),
                    'nom' => $cat->getNom()
                ];
            }
            $result[] = [
                'Id' => $p->getId(),
                'Nom' => $p->getNom(),
                'Prix' => $p->getPrix(),
                'Url' => $p->getUrl(),
                'Categories' => $cats
            ];
        }
        return new JsonResponse($result);
    }
}

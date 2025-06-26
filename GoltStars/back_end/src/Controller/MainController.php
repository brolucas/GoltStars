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

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Lister tous les produits",
     *     @OA\Response(response=200, description="Liste des produits")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/product",
     *     summary="Ajouter un produit",
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
        $produit = new Produit();
        $produit->setNom($data['nom']);
        $produit->setPrix((float)$data['prix']);
        $produit->setUrl($data['url']);
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
     *     summary="Récupérer un produit par son id",
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
        return new JsonResponse([
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'url' => $produit->getUrl()
        ]);
    }
}

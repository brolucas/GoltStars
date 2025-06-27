<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Client;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CommandeController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/api/commande",
     *     summary="Créer une commande",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="dateLivraison", type="string"),
     *                 @OA\Property(property="clientId", type="integer"),
     *                 @OA\Property(property="produits", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Commande créée avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    #[Route('/api/commande', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }

        // Vérification des champs requis (on retire dateLivraison du check obligatoire)
        foreach (["id", "produits"] as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Champ manquant : $field"], 400);
            }
        }

        // Gestion du client
        $client = null;
        if (!empty($data['client']['nom'])) {
            $c = $data['client'];
            foreach (["nom", "prenom", "adresseLivraison"] as $field) {
                if (empty($c[$field])) {
                    return new JsonResponse(['error' => "Champ client manquant : $field"], 400);
                }
            }
            $numeroTelephone = $c['numeroTelephone'] ?? null;
            $client = new \App\Entity\Client(
                $c['nom'],
                $c['prenom'],
                $c['adresseLivraison'],
                $numeroTelephone
            );
            // Création de la carte bancaire si présente
            if (!empty($c['carteBancaire'])) {
                $cb = $c['carteBancaire'];
                foreach (["numeroCarte", "dateExpiration", "nomTitulaire", "adresseFacturation", "cryptogramme"] as $field) {
                    if (empty($cb[$field])) {
                        return new JsonResponse(['error' => "Champ carte bancaire manquant : $field"], 400);
                    }
                }
                $carte = new \App\Entity\CarteBancaire(
                    $cb['numeroCarte'],
                    $cb['dateExpiration'],
                    $cb['adresseFacturation'],
                    $cb['cryptogramme'],
                    $client
                );
                $carte->setNomTitulaire($cb['nomTitulaire']);
                $client->getCartesBancaires()->add($carte);
                $entityManager->persist($carte);
            }
            $entityManager->persist($client);
        } elseif (!empty($data['clientId'])) {
            $client = $entityManager->getRepository(\App\Entity\Client::class)->find($data['clientId']);
            if (!$client) {
                return new JsonResponse(['error' => 'Client non trouvé'], 404);
            }
        } else {
            return new JsonResponse(['error' => 'Informations client manquantes'], 400);
        }

        // Gestion de la date de livraison
        if (!empty($data['dateLivraison'])) {
            try {
                $dateLivraison = new \DateTime($data['dateLivraison']);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], 400);
            }
        } else {
            // Date aléatoire entre 7 et 14 jours après aujourd'hui
            $days = random_int(7, 14);
            $dateLivraison = (new \DateTime())->modify("+{$days} days");
        }

        $commande = new \App\Entity\Commande($data['id'], $dateLivraison, $client);

        // Ajout des produits
        foreach ($data['produits'] as $produitId) {
            $produit = $entityManager->getRepository(\App\Entity\Produit::class)->find($produitId);
            if (!$produit) {
                return new JsonResponse(['error' => "Produit non trouvé : $produitId"], 404);
            }
            $commande->addProduit($produit);
        }

        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Commande créée avec succès',
            'commande_id' => $commande->getId(),
            'client_id' => $client->getId(),
            'dateLivraison' => $commande->getDateLivraison()->format('Y-m-d')
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/commande/{id}",
     *     summary="Récupérer une commande par son id",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Commande trouvée"),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
    #[Route('/api/commande/{id}', name: 'get_commande', methods: ['GET'])]
    public function getCommande(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande) {
            return new JsonResponse(['error' => 'Commande non trouvée'], 404);
        }
        $produits = [];
        foreach ($commande->getProduits() as $produit) {
            $produits[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'url' => $produit->getUrl()
            ];
        }
        return new JsonResponse([
            'id' => $commande->getId(),
            'dateLivraison' => $commande->getDateLivraison()->format('Y-m-d'),
            'clientId' => $commande->getClient() ? $commande->getClient()->getId() : null,
            'produits' => $produits
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/commandes",
     *     summary="Lister toutes les commandes",
     *     @OA\Response(response=200, description="Liste des commandes")
     * )
     */
    #[Route('/api/commandes', name: 'get_all_commandes', methods: ['GET'])]
    public function getAllCommandes(EntityManagerInterface $entityManager): JsonResponse
    {
        $commandes = $entityManager->getRepository(Commande::class)->findAll();
        $result = [];
        foreach ($commandes as $commande) {
            $result[] = [
                'id' => $commande->getId(),
                'dateLivraison' => $commande->getDateLivraison()->format('Y-m-d'),
                'clientId' => $commande->getClient() ? $commande->getClient()->getId() : null,
                'nbProduits' => count($commande->getProduits())
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @OA\Delete(
     *     path="/api/commande/{id}",
     *     summary="Supprimer une commande",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Commande supprimée"),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
    #[Route('/api/commande/{id}', name: 'delete_commande', methods: ['DELETE'])]
    public function deleteCommande(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande) {
            return new JsonResponse(['error' => 'Commande non trouvée'], 404);
        }
        $entityManager->remove($commande);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Commande supprimée avec succès']);
    }

    /**
     * @OA\Patch(
     *     path="/api/commande/{id}/date",
     *     summary="Mettre à jour la date de livraison d'une commande",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="dateLivraison", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Date de livraison mise à jour"),
     *     @OA\Response(response=404, description="Commande non trouvée")
     * )
     */
    #[Route('/api/commande/{id}/date', name: 'update_date_livraison', methods: ['PATCH'])]
    public function updateDateLivraison(string $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande) {
            return new JsonResponse(['error' => 'Commande non trouvée'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (empty($data['dateLivraison'])) {
            return new JsonResponse(['error' => 'Nouveau champ dateLivraison manquant'], 400);
        }
        try {
            $nouvelleDate = new \DateTime($data['dateLivraison']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Format de date invalide'], 400);
        }
        $commande->setDateLivraison($nouvelleDate);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Date de livraison mise à jour']);
    }
} 
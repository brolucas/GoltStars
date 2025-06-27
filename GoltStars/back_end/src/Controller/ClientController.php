<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\CarteBancaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ClientController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/api/client",
     *     summary="Créer un client avec carte bancaire",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="prenom", type="string"),
     *                 @OA\Property(property="adresseLivraison", type="string"),
     *                 @OA\Property(property="numeroTelephone", type="string"),
     *                 @OA\Property(property="carteBancaire", type="object",
     *                     @OA\Property(property="numeroCarte", type="string"),
     *                     @OA\Property(property="dateExpiration", type="string"),
     *                     @OA\Property(property="nomTitulaire", type="string"),
     *                     @OA\Property(property="adresseFacturation", type="string"),
     *                     @OA\Property(property="cryptogramme", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Client et carte bancaire créés avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    #[Route('/api/client', name: 'create_client', methods: ['POST'])]
    public function createClient(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }

        // Vérification des champs requis pour le client
        foreach (["nom", "prenom", "adresseLivraison", "numeroTelephone"] as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Champ client manquant : $field"], 400);
            }
        }
        // Vérification des champs requis pour la carte bancaire
        if (empty($data['carteBancaire'])) {
            return new JsonResponse(['error' => 'Données carte bancaire manquantes'], 400);
        }
        $cb = $data['carteBancaire'];
        foreach (["numeroCarte", "dateExpiration", "nomTitulaire", "adresseFacturation", "cryptogramme"] as $field) {
            if (empty($cb[$field])) {
                return new JsonResponse(['error' => "Champ carte bancaire manquant : $field"], 400);
            }
        }

        // Création du client
        $client = new Client(
            $data['nom'],
            $data['prenom'],
            $data['adresseLivraison'],
            $data['numeroTelephone']
        );

        // Création de la carte bancaire
        $carte = new CarteBancaire(
            $cb['numeroCarte'],
            $cb['dateExpiration'],
            $cb['adresseFacturation'],
            $cb['cryptogramme'],
            $client
        );
        $carte->setNomTitulaire($cb['nomTitulaire']);

        // Lien entre client et carte
        $client->getCartesBancaires()->add($carte);

        // Persistance
        $entityManager->persist($client);
        $entityManager->persist($carte);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Client et carte bancaire créés avec succès',
            'client_id' => $client->getId(),
            'carte_id' => $carte->getId()
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/client/{id}",
     *     summary="Récupérer un client par son id",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Client trouvé"),
     *     @OA\Response(response=404, description="Client non trouvé")
     * )
     */
    #[Route('/api/client/{id}', name: 'get_client', methods: ['GET'])]
    public function getClient(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé'], 404);
        }
        $cartes = [];
        foreach ($client->getCartesBancaires() as $carte) {
            $cartes[] = [
                'id' => $carte->getId(),
                'numeroCarte' => $carte->getNumeroCarte(),
                'dateExpiration' => $carte->getDateExpiration(),
                'nomTitulaire' => $carte->getNomTitulaire(),
                'adresseFacturation' => $carte->getAdresseFacturation()
            ];
        }
        return new JsonResponse([
            'id' => $client->getId(),
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'adresseLivraison' => $client->getAdresseLivraison(),
            'numeroTelephone' => $client->getNumeroTelephone(),
            'cartesBancaires' => $cartes
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Lister tous les clients",
     *     @OA\Response(response=200, description="Liste des clients")
     * )
     */
    #[Route('/api/clients', name: 'get_all_clients', methods: ['GET'])]
    public function getAllClients(EntityManagerInterface $entityManager): JsonResponse
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $result = [];
        foreach ($clients as $client) {
            $result[] = [
                'id' => $client->getId(),
                'nom' => $client->getNom(),
                'prenom' => $client->getPrenom(),
                'adresseLivraison' => $client->getAdresseLivraison(),
                'numeroTelephone' => $client->getNumeroTelephone(),
                'nbCartes' => count($client->getCartesBancaires())
            ];
        }
        return new JsonResponse($result);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/{id}",
     *     summary="Supprimer un client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Client supprimé"),
     *     @OA\Response(response=404, description="Client non trouvé")
     * )
     */
    #[Route('/api/client/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé'], 404);
        }
        $entityManager->remove($client);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Client supprimé avec succès']);
    }

    /**
     * @OA\Patch(
     *     path="/api/client/{id}",
     *     summary="Mettre à jour un client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="prenom", type="string"),
     *                 @OA\Property(property="adresseLivraison", type="string"),
     *                 @OA\Property(property="numeroTelephone", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Client mis à jour"),
     *     @OA\Response(response=404, description="Client non trouvé")
     * )
     */
    #[Route('/api/client/{id}', name: 'update_client', methods: ['PATCH'])]
    public function updateClient(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }
        if (!empty($data['nom'])) $client->setNom($data['nom']);
        if (!empty($data['prenom'])) $client->setPrenom($data['prenom']);
        if (!empty($data['adresseLivraison'])) $client->setAdresseLivraison($data['adresseLivraison']);
        if (!empty($data['numeroTelephone'])) $client->setNumeroTelephone($data['numeroTelephone']);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Client mis à jour']);
    }

    /**
     * @OA\Get(
     *     path="/api/client/{id}/cartes",
     *     summary="Lister les cartes bancaires d'un client",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des cartes bancaires")
     * )
     */
    #[Route('/api/client/{id}/cartes', name: 'get_client_cartes', methods: ['GET'])]
    public function getClientCartes(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé'], 404);
        }
        $cartes = [];
        foreach ($client->getCartesBancaires() as $carte) {
            $cartes[] = [
                'id' => $carte->getId(),
                'numeroCarte' => $carte->getNumeroCarte(),
                'dateExpiration' => $carte->getDateExpiration(),
                'nomTitulaire' => $carte->getNomTitulaire(),
                'adresseFacturation' => $carte->getAdresseFacturation()
            ];
        }
        return new JsonResponse($cartes);
    }
} 
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

class CommandeController extends AbstractController
{
    #[Route('/api/commande', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }

        // Vérification des champs requis
        foreach (["id", "dateLivraison", "clientId", "produits"] as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Champ manquant : $field"], 400);
            }
        }

        // Récupération du client
        $client = $entityManager->getRepository(Client::class)->find($data['clientId']);
        if (!$client) {
            return new JsonResponse(['error' => 'Client non trouvé'], 404);
        }

        // Création de la commande
        try {
            $dateLivraison = new \DateTime($data['dateLivraison']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Format de date invalide'], 400);
        }
        $commande = new Commande($data['id'], $dateLivraison, $client);

        // Ajout des produits
        foreach ($data['produits'] as $produitId) {
            $produit = $entityManager->getRepository(Produit::class)->find($produitId);
            if (!$produit) {
                return new JsonResponse(['error' => "Produit non trouvé : $produitId"], 404);
            }
            $commande->addProduit($produit);
        }

        // Persistance
        $entityManager->persist($commande);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Commande créée avec succès',
            'commande_id' => $commande->getId()
        ], 201);
    }

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
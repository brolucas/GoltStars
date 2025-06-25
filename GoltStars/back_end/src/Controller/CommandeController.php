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
} 
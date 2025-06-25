<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\CarteBancaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
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
} 
import { useLocation } from "react-router-dom";

export default function Confirmation() {
  const { state } = useLocation();
  const message = state?.message || "Merci pour votre achat !";
  const commande = state?.commande || {};
  const produit = commande.produit || {};
  const client = commande.client || {};
  const dateLivraison = commande.dateLivraison;
  const numeroCommande = commande.numeroCommande;

  return (
    <div className="container py-4">
      <h2 className="mb-4">Confirmation de commande</h2>
      <div className="alert alert-success">{message}</div>
      <div className="card shadow p-4 mb-4">
        <h4 className="mb-3">Récapitulatif de la commande</h4>
        <ul className="list-group list-group-flush mb-3">
          <li className="list-group-item">
            <strong>Produit :</strong> {produit.nom || produit.Nom || "-"}
          </li>
          <li className="list-group-item">
            <strong>Prix :</strong> {produit.prix || produit.Prix || "-"} €
          </li>
          <li className="list-group-item">
            <strong>Client :</strong> {client.nom} {client.prenom}
          </li>
          <li className="list-group-item">
            <strong>Adresse de livraison :</strong> {client.adresseLivraison}
          </li>
          <li className="list-group-item">
            <strong>Date de livraison prévue :</strong> {dateLivraison}
          </li>
          <li className="list-group-item">
            <strong>Numéro de suivi :</strong> {numeroCommande}
          </li>
        </ul>
      </div>
      <p className="text-center">Merci pour votre confiance !</p>
    </div>
  );
}

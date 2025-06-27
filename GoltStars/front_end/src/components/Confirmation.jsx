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
        <div className="row align-items-center">
          {/* Colonne produit */}
          <div className="col-md-6 text-center mb-4 mb-md-0">
            <img
              src={produit.Url || produit.url || "/images/LogoGoltStars.png"}
              alt={produit.nom || produit.Nom || "Produit"}
              style={{
                maxWidth: "250px",
                maxHeight: "350px",
                borderRadius: "12px",
                boxShadow: "0 4px 24px #0002",
              }}
            />
            <h5 className="mt-3">{produit.nom || produit.Nom || "-"}</h5>
            <p className="text-muted mb-1">
              <strong>Prix :</strong> {produit.prix || produit.Prix || "-"} €
            </p>
          </div>
          {/* Colonne infos client */}
          <div className="col-md-6">
            <ul className="list-group list-group-flush mb-3">
              <li className="list-group-item">
                <strong>Client :</strong> {client.nom} {client.prenom}
              </li>
              <li className="list-group-item">
                <strong>Adresse de livraison :</strong>{" "}
                {client.adresseLivraison}
              </li>
              <li className="list-group-item">
                <strong>Date de livraison prévue :</strong> {dateLivraison}
              </li>
              <li className="list-group-item">
                <strong>Numéro de suivi :</strong> {numeroCommande}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <p className="text-center">Merci pour votre confiance !</p>
    </div>
  );
}

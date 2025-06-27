import { useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import Step1Form from "./Step1Form";
import Step2Form from "./Step2Form";

export default function Paiement() {
  const { state } = useLocation();
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [clientInfo, setClientInfo] = useState({});
  const [message, setMessage] = useState("");

  const product = state?.product;

  if (!product) return <p>Erreur : aucun produit sélectionné.</p>;

  // Nouvelle fonction pour gérer le résultat du paiement et passer toutes les infos à la confirmation
  const handleResult = (success, msg, extra = {}) => {
    setMessage(msg);
    if (success) {
      navigate("/confirmation", {
        state: {
          message: msg,
          commande: {
            produit: product,
            client: clientInfo,
            dateLivraison: extra.dateLivraison,
            numeroCommande: extra.numeroCommande,
          },
        },
      });
    } else {
      navigate("/confirmation", { state: { message: msg } });
    }
  };

  return (
    <div className="container py-4">
      {step === 1 && (
        <Step1Form
          onNext={(data) => {
            setClientInfo(data);
            setStep(2);
          }}
        />
      )}
      {step === 2 && (
        <Step2Form
          clientData={clientInfo}
          product={product}
          onResult={handleResult}
        />
      )}
    </div>
  );
}

import { useState } from "react";
import axios from "axios";

export default function Step2Form({ clientData, onResult }) {
  const [payment, setPayment] = useState({ numeroCarte: "", codeSecu: "" });

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await axios.post("http://localhost/api/paiement", {
        client: clientData,
        payment
      });
      onResult(true, "Paiement réussi !");
    } catch (err) {
      onResult(false, "Erreur lors du paiement.");
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Infos de paiement</h2>
      <input placeholder="Numéro de carte" onChange={e => setPayment({ ...payment, numeroCarte: e.target.value })} required />
      <input placeholder="Code de sécurité" onChange={e => setPayment({ ...payment, codeSecu: e.target.value })} required />
      <button type="submit">Payer</button>
    </form>
  );
}

import { useState } from "react";
import axios from "axios";

export default function Step2Form({ clientData, product, onResult }) {
  const [payment, setPayment] = useState({
    numeroCarte: "",
    dateExpiration: "",
    nomTitulaire: "",
    adresseFacturation: "",
    cryptogramme: "",
  });

  const [isProcessing, setIsProcessing] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsProcessing(true);

    // Construction du payload pour l'API
    const payload = {
      id: `CMD${Date.now()}`,
      client: {
        nom: clientData.nom,
        prenom: clientData.prenom,
        adresseLivraison: clientData.adresse,
        numeroTelephone: clientData.numeroTelephone?.trim()
          ? clientData.numeroTelephone
          : null,
        carteBancaire: {
          numeroCarte: payment.numeroCarte,
          dateExpiration: payment.dateExpiration,
          nomTitulaire: payment.nomTitulaire,
          adresseFacturation: payment.adresseFacturation,
          cryptogramme: payment.cryptogramme,
        },
      },
      produits: [product.Id || product.id],
      // dateLivraison: ... // Optionnel, laissé vide pour date auto côté back
    };

    try {
      const response = await axios.post(
        "https://localhost/api/commande",
        payload
      );
      // On transmet la date de livraison et le numéro de commande à la confirmation
      onResult(true, "Paiement réussi !", {
        dateLivraison: response.data?.dateLivraison,
        numeroCommande: response.data?.commande_id,
      });
    } catch (err) {
      onResult(false, "Erreur lors du paiement.");
    } finally {
      setIsProcessing(false);
    }
  };

  // Styles simples et efficaces
  const styles = {
    container: {
      maxWidth: "500px",
      margin: "2rem auto",
      padding: "2rem",
      borderRadius: "10px",
      backgroundColor: "#f8f9fa",
      boxShadow: "0 2px 10px rgba(0,0,0,0.1)",
    },
    title: {
      color: "#2c3e50",
      textAlign: "center",
      marginBottom: "1.5rem",
      fontSize: "1.5rem",
    },
    inputContainer: {
      marginBottom: "1.5rem",
    },
    label: {
      display: "block",
      marginBottom: "0.5rem",
      fontWeight: "500",
      color: "#495057",
    },
    input: {
      width: "100%",
      padding: "0.75rem",
      border: "1px solid #ced4da",
      borderRadius: "5px",
      fontSize: "1rem",
      boxSizing: "border-box",
    },
    button: {
      width: "100%",
      padding: "0.75rem",
      backgroundColor: "#28a745",
      color: "white",
      border: "none",
      borderRadius: "5px",
      fontSize: "1rem",
      fontWeight: "500",
      cursor: "pointer",
      transition: "background-color 0.2s",
    },
    buttonHover: {
      backgroundColor: "#218838",
    },
    buttonDisabled: {
      backgroundColor: "#6c757d",
      cursor: "not-allowed",
    },
    cardPreview: {
      backgroundColor: "#6c757d",
      color: "white",
      padding: "1rem",
      borderRadius: "5px",
      marginBottom: "1.5rem",
      fontSize: "0.9rem",
      fontFamily: "monospace",
    },
  };

  return (
    <div style={styles.container}>
      <h2 style={styles.title}>Paiement sécurisé</h2>

      <div style={styles.cardPreview}>
        Carte: **** **** **** {payment.numeroCarte.slice(-4)}
      </div>

      <form onSubmit={handleSubmit}>
        <div style={styles.inputContainer}>
          <label style={styles.label}>Numéro de carte</label>
          <input
            style={styles.input}
            placeholder="4242 4242 4242 4242"
            type="text"
            value={payment.numeroCarte}
            onChange={(e) =>
              setPayment({
                ...payment,
                numeroCarte: e.target.value.replace(/\D/g, "").slice(0, 16),
              })
            }
            required
          />
        </div>

        <div style={styles.inputContainer}>
          <label style={styles.label}>Date d'expiration (MM/YY)</label>
          <input
            style={styles.input}
            placeholder="12/29"
            type="text"
            value={payment.dateExpiration}
            onChange={(e) =>
              setPayment({
                ...payment,
                dateExpiration: e.target.value.slice(0, 5),
              })
            }
            required
          />
        </div>

        <div style={styles.inputContainer}>
          <label style={styles.label}>Nom du titulaire</label>
          <input
            style={styles.input}
            placeholder="Jean Dupont"
            type="text"
            value={payment.nomTitulaire}
            onChange={(e) =>
              setPayment({ ...payment, nomTitulaire: e.target.value })
            }
            required
          />
        </div>

        <div style={styles.inputContainer}>
          <label style={styles.label}>Adresse de facturation</label>
          <input
            style={styles.input}
            placeholder="12 rue de Paris"
            type="text"
            value={payment.adresseFacturation}
            onChange={(e) =>
              setPayment({ ...payment, adresseFacturation: e.target.value })
            }
            required
          />
        </div>

        <div style={styles.inputContainer}>
          <label style={styles.label}>Cryptogramme</label>
          <input
            style={styles.input}
            placeholder="123"
            type="text"
            value={payment.cryptogramme}
            onChange={(e) =>
              setPayment({
                ...payment,
                cryptogramme: e.target.value.replace(/\D/g, "").slice(0, 4),
              })
            }
            required
          />
        </div>

        <button
          type="submit"
          style={{
            ...styles.button,
            ...(isProcessing ? styles.buttonDisabled : styles.buttonHover),
          }}
          disabled={isProcessing}
        >
          {isProcessing ? "Traitement en cours..." : "Confirmer le paiement"}
        </button>
      </form>
    </div>
  );
}

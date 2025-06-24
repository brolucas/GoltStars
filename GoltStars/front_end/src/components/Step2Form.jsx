import { useState } from "react";
import axios from "axios";

export default function Step2Form({ clientData, onResult }) {
  const [payment, setPayment] = useState({ 
    numeroCarte: "5555", 
    codeSecu: "555" 
  });

  const [isProcessing, setIsProcessing] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsProcessing(true);
    
    try {
      const response = await axios.post("http://localhost/api/paiement", {
        client: clientData,
        payment
      });
      onResult(true, "Paiement réussi !");
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
      boxShadow: "0 2px 10px rgba(0,0,0,0.1)"
    },
    title: {
      color: "#2c3e50",
      textAlign: "center",
      marginBottom: "1.5rem",
      fontSize: "1.5rem"
    },
    inputContainer: {
      marginBottom: "1.5rem"
    },
    label: {
      display: "block",
      marginBottom: "0.5rem",
      fontWeight: "500",
      color: "#495057"
    },
    input: {
      width: "100%",
      padding: "0.75rem",
      border: "1px solid #ced4da",
      borderRadius: "5px",
      fontSize: "1rem",
      boxSizing: "border-box"
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
      transition: "background-color 0.2s"
    },
    buttonHover: {
      backgroundColor: "#218838"
    },
    buttonDisabled: {
      backgroundColor: "#6c757d",
      cursor: "not-allowed"
    },
    cardPreview: {
      backgroundColor: "#6c757d",
      color: "white",
      padding: "1rem",
      borderRadius: "5px",
      marginBottom: "1.5rem",
      fontSize: "0.9rem",
      fontFamily: "monospace"
    }
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
            onChange={e => setPayment({ 
              ...payment, 
              numeroCarte: e.target.value.replace(/\D/g, '').slice(0, 16) 
            })}
            required
          />
        </div>
        
        <div style={styles.inputContainer}>
          <label style={styles.label}>Code de sécurité</label>
          <input
            style={styles.input}
            placeholder="123"
            type="text"
            value={payment.codeSecu}
            onChange={e => setPayment({ 
              ...payment, 
              codeSecu: e.target.value.replace(/\D/g, '').slice(0, 3) 
            })}
            required
          />
        </div>
        
        <button 
          type="submit"
          style={{
            ...styles.button,
            ...(isProcessing ? styles.buttonDisabled : styles.buttonHover)
          }}
          disabled={isProcessing}
        >
          {isProcessing ? "Traitement en cours..." : "Confirmer le paiement"}
        </button>
      </form>
    </div>
  );
}
import { useState, useEffect } from "react";

export default function Step1Form({ onNext }) {
  const [form, setForm] = useState({
    nom: "",
    prenom: "",
    email: "",
    adresse: "",
    numeroTelephone: "",
  });
  const [shake, setShake] = useState(false);
  const [glowIndex, setGlowIndex] = useState(-1);

  useEffect(() => {
    // Animation d'entrée
    const timer = setTimeout(() => setGlowIndex(0), 300);
    return () => clearTimeout(timer);
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();

    // Vérification rapide
    if (!form.nom || !form.email) {
      setShake(true);
      setTimeout(() => setShake(false), 500);
      return;
    }

    // Effet de confirmation avant passage
    setGlowIndex(4);
    setTimeout(() => onNext(form), 300);
  };

  // Styles dynamiques
  const styles = {
    container: {
      maxWidth: "500px",
      margin: "40px auto",
      padding: "30px",
      borderRadius: "12px",
      background: "linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%)",
      boxShadow: "0 10px 30px rgba(0, 0, 0, 0.15)",
      transform: shake ? "translateX(10px)" : "translateX(0)",
      transition: "all 0.3s ease, transform 0.5s cubic-bezier(.36,.07,.19,.97)",
    },
    input: (index) => ({
      width: "100%",
      padding: "16px 20px",
      marginBottom: "25px",
      border: "none",
      borderRadius: "8px",
      fontSize: "16px",
      boxShadow:
        glowIndex === index
          ? "0 0 15px rgba(52, 152, 219, 0.6)"
          : "0 2px 5px rgba(0,0,0,0.1)",
      transition: "all 0.4s ease-out",
      transform: glowIndex === index ? "scale(1.02)" : "scale(1)",
      background: "rgba(255,255,255,0.9)",
    }),
    button: {
      background: "linear-gradient(90deg, #3498db, #2ecc71)",
      backgroundSize: "200% auto",
      color: "white",
      border: "none",
      padding: "16px",
      fontSize: "18px",
      fontWeight: "bold",
      borderRadius: "8px",
      cursor: "pointer",
      boxShadow: "0 4px 15px rgba(46, 204, 113, 0.4)",
      transition: "all 0.5s ease",
      width: "100%",
      ":hover": {
        backgroundPosition: "right center",
        transform: "translateY(-3px)",
        boxShadow: "0 7px 20px rgba(46, 204, 113, 0.6)",
      },
    },
  };

  return (
    <div style={styles.container}>
      <h2
        style={{
          textAlign: "center",
          marginBottom: "30px",
          fontSize: "28px",
          color: "#2c3e50",
          textShadow: "1px 1px 3px rgba(0,0,0,0.1)",
          position: "relative",
          display: "inline-block",
          width: "100%",
        }}
      >
        <span
          style={{
            position: "absolute",
            bottom: "-10px",
            left: "50%",
            transform: "translateX(-50%)",
            width: "50px",
            height: "4px",
            background: "linear-gradient(90deg, #3498db, #2ecc71)",
            borderRadius: "2px",
          }}
        ></span>
        Informations Client
      </h2>

      <form onSubmit={handleSubmit}>
        {["nom", "prenom", "email", "adresse", "numeroTelephone"].map(
          (field, index) => (
            <input
              key={field}
              style={styles.input(index)}
              placeholder={
                field === "nom"
                  ? "Votre nom"
                  : field === "prenom"
                  ? "Votre prénom"
                  : field === "email"
                  ? "email@exemple.com"
                  : field === "adresse"
                  ? "Adresse complète"
                  : "Numéro de téléphone (optionnel)"
              }
              type={field === "email" ? "email" : "text"}
              value={form[field]}
              onChange={(e) => {
                setForm({ ...form, [field]: e.target.value });
                setGlowIndex(index);
                setTimeout(() => setGlowIndex(-1), 1000);
              }}
              onFocus={() => setGlowIndex(index)}
              onBlur={() => setGlowIndex(-1)}
              required={field !== "numeroTelephone"}
            />
          )
        )}

        <button type="submit" style={styles.button}>
          Suivant ➔
        </button>
      </form>
    </div>
  );
}

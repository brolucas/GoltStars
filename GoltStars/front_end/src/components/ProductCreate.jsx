import { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

export default function ProductCreate() {
  const [form, setForm] = useState({
    nom: "",           // 🔄 "name" → "nom"
    prix: "",          // 🔄 "price" → "prix"
    url: "",           // 🔄 "image" → "url"
  });

  const [message, setMessage] = useState("");
  const navigate = useNavigate();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const payload = {
      nom: form.nom,
      prix: parseFloat(form.prix),
      url: form.url
    };

    try {
      await axios.post("https://localhost/api/product", payload);
      setMessage("Produit créé avec succès !");
      setTimeout(() => navigate("/"), 1500);
    } catch (err) {
      console.error(err);
      setMessage("Erreur lors de la création du produit.");
    }
  };

  return (
    <div className="container py-4">
      <button className="btn btn-secondary mb-4" onClick={() => navigate('/')}>
        ← Retour
      </button>
      <h2 className="mb-4">Créer un nouveau produit</h2>

      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label>Nom</label>
          <input
            type="text"
            name="nom"
            className="form-control"
            required
            onChange={handleChange}
          />
        </div>

        <div className="mb-3">
          <label>Prix (€)</label>
          <input
            type="number"
            name="prix"
            className="form-control"
            required
            onChange={handleChange}
          />
        </div>

        <div className="mb-3">
          <label>Image (URL)</label>
          <input
            type="text"
            name="url"
            className="form-control"
            required
            onChange={handleChange}
          />
        </div>

        <button className="btn btn-primary" type="submit">
          Créer
        </button>
      </form>

      {message && <p className="mt-3">{message}</p>}
    </div>
  );
}

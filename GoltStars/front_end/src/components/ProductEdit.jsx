import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

export default function ProductEdit() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [form, setForm] = useState({ nom: "", prix: "", url: "" });
  const [original, setOriginal] = useState({ nom: "", prix: "", url: "" });
  const [message, setMessage] = useState("");

  useEffect(() => {
  axios.get(`https://localhost/api/product/${id}`)
    .then((res) => {
      const p = res.data;
      // Accepte les deux formats de clés
      setForm({
        nom: p.nom || p.Nom || "",
        prix: p.prix || p.Prix || "",
        url: p.url || p.Url || ""
      });
      setOriginal({
        nom: p.nom || p.Nom || "",
        prix: p.prix || p.Prix || "",
        url: p.url || p.Url || ""
      });
    })
    .catch(() => setMessage("❌ Erreur lors du chargement du produit."));
}, [id]);


  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const updates = {};
    if (form.nom !== original.nom) updates.nom = form.nom;
    if (form.prix !== original.prix) updates.prix = parseFloat(form.prix);
    if (form.url !== original.url) updates.url = form.url;

    if (Object.keys(updates).length === 0) {
      setMessage("Aucune modification à enregistrer.");
      return;
    }

    try {
      await axios.patch(`https://localhost/api/product/${id}`, updates);
      setMessage("✅ Produit mis à jour !");
      setTimeout(() => navigate(`/produit/${id}`), 1500);
    } catch (err) {
      console.error(err);
      setMessage("❌ Erreur lors de la mise à jour.");
    }
  };

  return (
    <div className="container py-4">
      <h2 className="mb-4">Modifier le produit</h2>

      {/* Affichage des infos actuelles */}
      <div className="bg-light border rounded p-3 mb-4">
        <p><strong>Nom actuel :</strong> {original.nom}</p>
        <p><strong>Prix actuel :</strong> {original.prix} €</p>
        <p><strong>Image actuelle :</strong> {original.url}</p>
      </div>

      <form onSubmit={handleSubmit}>
        <input
          type="text"
          name="nom"
          value={form.nom}
          onChange={handleChange}
          className="form-control mb-3"
          placeholder="Nom du produit"
        />
        <input
          type="number"
          name="prix"
          value={form.prix}
          onChange={handleChange}
          className="form-control mb-3"
          placeholder="Prix (€)"
        />
        <input
          type="text"
          name="url"
          value={form.url}
          onChange={handleChange}
          className="form-control mb-3"
          placeholder="URL de l'image"
        />
        <button type="submit" className="btn btn-warning">Mettre à jour</button>
      </form>

      {message && <p className="mt-3">{message}</p>}
    </div>
  );
}

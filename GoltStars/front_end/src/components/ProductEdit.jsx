import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

export default function ProductEdit() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [form, setForm] = useState(null);
  const [message, setMessage] = useState("");

  useEffect(() => {
    axios.get(`https://localhost/api/product/${id}`)
      .then((res) => {
        const p = res.data;
        setForm({
          nom: p.Nom || "",
          prix: p.Prix || 0,
          url: p.Url || ""
        });
      })
      .catch(() => setMessage("Erreur lors du chargement du produit."));
  }, [id]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      await axios.patch(`https://localhost/api/product/${id}`, {
        nom: form.nom,
        prix: parseFloat(form.prix),
        url: form.url
      });
      setMessage("✅ Produit mis à jour !");
      setTimeout(() => navigate(`/produit/${id}`), 1500);
    } catch (err) {
      console.error(err);
      setMessage("❌ Erreur lors de la mise à jour.");
    }
  };

  if (!form) return <p className="text-center">Chargement...</p>;

  return (
    <div className="container py-5">
      <button className="btn btn-secondary mb-4" onClick={() => navigate(-1)}>
        ← Retour
      </button>

      <h2 className="mb-4 text-primary">Modifier le produit</h2>

      {/* Aperçu du produit actuel */}
      <div className="card mb-5 shadow-sm">
        <div className="row g-0">
          <div className="col-md-4">
            <img
              src={form.url || "https://via.placeholder.com/400x300?text=Image"}
              alt={form.nom}
              className="img-fluid rounded-start"
              style={{ objectFit: "cover", height: "100%" }}
            />
          </div>
          <div className="col-md-8">
            <div className="card-body">
              <h5 className="card-title">{form.nom}</h5>
              <p className="card-text">
                <strong>Prix :</strong> {form.prix} €
              </p>
              <p className="card-text text-muted">
                <small>Prévisualisation actuelle</small>
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Formulaire de modification */}
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label className="form-label">Nom du produit</label>
          <input
            type="text"
            name="nom"
            value={form.nom}
            onChange={handleChange}
            className="form-control"
            required
          />
        </div>

        <div className="mb-3">
          <label className="form-label">Prix (€)</label>
          <input
            type="number"
            name="prix"
            value={form.prix}
            onChange={handleChange}
            className="form-control"
            required
          />
        </div>

        <div className="mb-3">
          <label className="form-label">URL de l'image</label>
          <input
            type="text"
            name="url"
            value={form.url}
            onChange={handleChange}
            className="form-control"
          />
        </div>

        <button type="submit" className="btn btn-warning">✅ Enregistrer les modifications</button>
      </form>

      {message && <div className="alert alert-info mt-3">{message}</div>}
    </div>
  );
}

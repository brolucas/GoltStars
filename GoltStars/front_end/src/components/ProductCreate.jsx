import { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

export default function ProductCreate() {
  const [form, setForm] = useState({
    nom: "",
    prix: "",
    url: "",
    categories: [],
  });

  const [availableCategories, setAvailableCategories] = useState([]);
  const [message, setMessage] = useState("");
  const navigate = useNavigate();

  // Charger les catégories depuis l'API
  useEffect(() => {
    axios.get("https://localhost/api/categories")
      .then((res) => {
        // res.data est un objet { id: nom }
        const array = Object.entries(res.data).map(([id, nom]) => ({
          id: parseInt(id),
          nom,
        }));
        setAvailableCategories(array);
      })
      .catch((err) => console.error("Erreur chargement catégories :", err));
  }, []);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const toggleCategory = (id) => {
    setForm((prev) => {
      const alreadySelected = prev.categories.includes(id);
      return {
        ...prev,
        categories: alreadySelected
          ? prev.categories.filter((catId) => catId !== id)
          : [...prev.categories, id],
      };
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const payload = {
      nom: form.nom,
      prix: parseFloat(form.prix),
      url: form.url,
      categories: form.categories,
    };

    try {
      await axios.post("https://localhost/api/product", payload);
      setMessage("✅ Produit créé avec succès !");
      setTimeout(() => navigate("/"), 1500);
    } catch (err) {
      console.error(err);
      setMessage("❌ Erreur lors de la création.");
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
          <input type="text" name="nom" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Prix (€)</label>
          <input type="number" name="prix" className="form-control" required onChange={handleChange} step="0.01" />
        </div>

        <div className="mb-3">
          <label>Image (URL)</label>
          <input type="text" name="url" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Catégories</label>
          <div className="d-flex flex-wrap gap-2">
            {availableCategories.map((cat) => (
              <button
                key={cat.id}
                type="button"
                className={`btn btn-sm ${form.categories.includes(cat.id) ? "btn-primary" : "btn-outline-secondary"}`}
                onClick={() => toggleCategory(cat.id)}
              >
                {cat.nom}
              </button>
            ))}
          </div>
        </div>

        <button className="btn btn-success" type="submit">Créer</button>
      </form>

      {message && <p className="mt-3">{message}</p>}
    </div>
  );
}

import { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

export default function ProductCreate() {
  const [form, setForm] = useState({
    name: "",
    price: "",
    quantity: "",
    description: "",
    categories: "",
    image: ""
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
      ...form,
      price: parseFloat(form.price),
      quantity: parseInt(form.quantity),
      categories: form.categories.split(",").map((cat) => cat.trim()),
    };

    try {
      await axios.post("http://localhost:8000/api/products", payload);
      setMessage("Produit créé avec succès !");
      setTimeout(() => navigate("/"), 1500);
    } catch (err) {
      console.error(err);
      setMessage("Erreur lors de la création.");
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
          <input type="text" name="name" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Prix (€)</label>
          <input type="number" name="price" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Quantité</label>
          <input type="number" name="quantity" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Description</label>
          <textarea name="description" className="form-control" required onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Catégories (séparées par des virgules)</label>
          <input type="text" name="categories" className="form-control" onChange={handleChange} />
        </div>

        <div className="mb-3">
          <label>Image (URL pour l’instant)</label>
          <input type="text" name="image" className="form-control" onChange={handleChange} />
        </div>

        <button className="btn btn-primary" type="submit">Créer</button>
      </form>

      {message && <p className="mt-3">{message}</p>}
    </div>
  );
}

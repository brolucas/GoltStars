import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";

export default function ProductEdit() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [form, setForm] = useState(null);
  const [message, setMessage] = useState("");

/*  useEffect(() => {
    axios.get(`http://localhost:8000/api/products/${id}`)
      .then((res) => {
        const p = res.data;
        setForm({
          name: p.name,
          price: p.price,
          quantity: p.quantity,
          description: p.description,
          categories: p.categories.join(", "),
          image: p.image
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
    const payload = {
      ...form,
      price: parseFloat(form.price),
      quantity: parseInt(form.quantity),
      categories: form.categories.split(",").map((c) => c.trim())
    };

    try {
      await axios.put(`http://localhost:8000/api/products/${id}`, payload);
      setMessage("Produit mis à jour !");
      setTimeout(() => navigate("/"), 1500);
    } catch (err) {
      setMessage("Erreur lors de la mise à jour.");
    }
  };*/

  if (!form) return <p className="text-center">Chargement...</p>;

  return (
    <div className="container py-4">
      <h2 className="mb-4">Modifier le produit</h2>
      <form onSubmit={handleSubmit}>
        <input type="text" name="name" value={form.name} onChange={handleChange} className="form-control mb-3" placeholder="Nom" required />
        <input type="number" name="price" value={form.price} onChange={handleChange} className="form-control mb-3" placeholder="Prix" required />
        <input type="number" name="quantity" value={form.quantity} onChange={handleChange} className="form-control mb-3" placeholder="Quantité" required />
        <textarea name="description" value={form.description} onChange={handleChange} className="form-control mb-3" placeholder="Description" required />
        <input type="text" name="categories" value={form.categories} onChange={handleChange} className="form-control mb-3" placeholder="Catégories (séparées par virgule)" />
        <input type="text" name="image" value={form.image} onChange={handleChange} className="form-control mb-3" placeholder="URL de l'image" />
        <button type="submit" className="btn btn-warning">Mettre à jour</button>
      </form>
      {message && <p className="mt-3">{message}</p>}
    </div>
  );
}

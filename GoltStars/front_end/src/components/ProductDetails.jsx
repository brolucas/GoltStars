import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import axios from "axios";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [deleting, setDeleting] = useState(false);

  useEffect(() => {
    axios
      .get(`https://localhost/api/product/${id}`)
      .then((res) => {
        setProduct(res.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setError("Produit introuvable ou erreur serveur.");
        setLoading(false);
      });
  }, [id]);

  const handleDelete = async () => {
    const confirm = window.confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");
    if (!confirm) return;

    try {
      setDeleting(true);
      await axios.delete(`https://localhost/api/product/${id}`);
      alert("Produit supprimé avec succès.");
      navigate("/");
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la suppression.");
      setDeleting(false);
    }
  };

  const handleEdit = () => {
    navigate(`/admin/edit/${id}`);
  };

  if (loading) return <p className="text-center mt-5">Chargement...</p>;
  if (error) return <p className="text-center mt-5 text-danger">{error}</p>;
  if (!product) return <p className="text-center mt-5">Produit introuvable</p>;

  return (
    <div className="container py-5">
      <button className="btn btn-secondary mb-4" onClick={() => navigate("/")}>
        ← Retour
      </button>

      <div className="card border-0 shadow-lg">
        <div
          className="position-relative"
          style={{
            height: "100%",
            overflow: "hidden",
            borderTopLeftRadius: "0.5rem",
            borderTopRightRadius: "0.5rem",
          }}
        >
          <img
            src={product.url || "https://via.placeholder.com/300x400?text=Image"}
            className="w-100 h-100"
            alt={product.Nom}
            style={{ objectFit: "cover" }}
          />
        </div>

        <div className="card-body">
          <h2 className="card-title text-center text-primary fw-bold">
            {product.nom}
          </h2>

          {product.categories && Array.isArray(product.categories) && (
            <div className="text-center mb-3">
              {product.categories.map((cat) => (
                <span key={cat} className="badge bg-light text-dark me-2 mb-2 border">
                  {cat}
                </span>
              ))}
            </div>
          )}

          <div className="text-center mb-4">
            <p className="fs-5 mb-1">
              <strong>Prix :</strong> {product.prix} €
            </p>
          </div>

          <div className="d-grid gap-2 col-md-6 mx-auto mt-4">
            <button
              className="btn btn-primary"
              onClick={() => navigate("/paiement", { state: { product } })}
            >
              Acheter maintenant
            </button>

            <button className="btn btn-outline-secondary" onClick={handleEdit}>
              ✏️ Modifier (Admin)
            </button>

            <button
              className="btn btn-danger"
              onClick={handleDelete}
              disabled={deleting}
            >
              {deleting ? "Suppression..." : "🗑️ Supprimer"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

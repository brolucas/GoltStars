import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import axios from "axios";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

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
            src={product.Url || "https://via.placeholder.com/300x400?text=Image"}
            className="w-100 h-100"
            alt={product.Nom}
            style={{ objectFit: "cover" }}
          />
        </div>

        <div className="card-body">
          <h2 className="card-title text-center text-primary fw-bold">
            {product.Nom}
          </h2>

          <p className="text-center text-muted mb-4" style={{ fontSize: "1.1rem" }}>
            {product.Description || "Pas de description fournie."}
          </p>

          {/* Exemple si tu ajoutes les catégories plus tard */}
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
              <strong>Prix :</strong> {product.Prix} €
            </p>
          </div>

          <div className="d-grid col-md-4 mx-auto">
            <button
              className="btn btn-primary btn-lg"
              onClick={() => navigate("/paiement", { state: { product } })}
            >
              Acheter maintenant
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

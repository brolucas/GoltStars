import { useParams, useNavigate } from "react-router-dom";
import { products } from "../data/products";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const product = products.find((p) => p.id === parseInt(id));

  if (!product) return <p className="text-center mt-5">Produit introuvable</p>;

  return (
    <div className="container py-5">
      <button className="btn btn-secondary mb-4" onClick={() => navigate("/")}>
        ← Retour
      </button>

      <div className="card border-0 shadow-lg">
        {/* IMAGE */}
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
            src={product.image}
            className="w-100 h-100"
            alt={product.name}
            style={{ objectFit: "cover" }}
          />
        </div>

        {/* CONTENU */}
        <div className="card-body">
          <h2 className="card-title text-center text-primary fw-bold">
            {product.name}
          </h2>

          <p className="text-center text-muted mb-4" style={{ fontSize: "1.1rem" }}>
            {product.description}
          </p>

          {/* Catégories */}
          {product.categories && (
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
              <strong>Prix :</strong> {product.price} €
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

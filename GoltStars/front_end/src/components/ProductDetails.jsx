import { useParams, useNavigate } from "react-router-dom";
import { products } from "../data/products";

export default function ProductDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const product = products.find((p) => p.id === parseInt(id));

  if (!product) return <p>Produit introuvable</p>;

  return (
    <div className="container py-4">
      <button className="btn btn-secondary mb-3" onClick={() => navigate(-1)}>
        ← Retour
      </button>
      <div className="card">
        <img
          src={product.image}
          className="card-img-top"
          alt={product.name}
          style={{ maxHeight: "500PX", objectFit: "cover" }}
        />
        <div className="card-body">
          <h3 className="card-title">{product.name}</h3>
          <p>Prix : {product.price} €</p>
          <p>Quantité : {product.quantity}</p>
          <button
            className="btn btn-primary"
            onClick={() => navigate("/paiement", { state: { product } })}
          >
            Acheter
          </button>
        </div>
      </div>
    </div>
  );
}

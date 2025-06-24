import { products } from "../data/products";
import { useNavigate } from "react-router-dom";

export default function ProductList() {
  const navigate = useNavigate();

  return (
    <div className="container">
      <div className="row">
        {products.map((product) => (
          <div className="col-md-6 col-lg-4 mb-4" key={product.id}>
            <div
              className="card shadow-sm h-100 border-0"
              style={{ transition: "transform 0.2s" }}
              onMouseEnter={(e) => e.currentTarget.style.transform = "scale(1.02)"}
              onMouseLeave={(e) => e.currentTarget.style.transform = "scale(1)"}
            >
              <img
                src={product.image}
                alt={product.name}
                className="card-img-top"
                style={{
                  height: "100%",
                  objectFit: "cover",
                  borderTopLeftRadius: "0.5rem",
                  borderTopRightRadius: "0.5rem",
                }}
              />
              <div className="card-body d-flex flex-column justify-content-between">
                <h5 className="card-title text-primary fw-bold text-center">{product.name}</h5>
                <p className="card-text text-muted text-center">
                  <strong>Prix :</strong> {product.price} € <br />
                  <strong>Stock :</strong> {product.quantity}
                </p>
                <div className="d-grid mt-3">
                  <button
                    className="btn btn-outline-primary"
                    onClick={() => navigate(`/produit/${product.id}`)}
                  >
                    Voir le produit
                  </button>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

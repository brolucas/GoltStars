import { products } from "../data/products";
import { useNavigate } from "react-router-dom";

export default function ProductList() {
  const navigate = useNavigate();

  return (
    <div className="row">
      {products.map((product) => (
        <div className="col-md-4 mb-4" key={product.id}>
          <div className="card h-100">
            <img
              src={product.image}
              className="card-img-top"
              alt={product.name}
              style={{ objectFit: "cover", height: "200px", cursor: "pointer" }}
              onClick={() => navigate(`/produit/${product.id}`)}
            />
            <div className="card-body">
              <h5 className="card-title">{product.name}</h5>
              <p>Prix : {product.price} €</p>
              <p>Quantité : {product.quantity}</p>
              <button
                className="btn btn-outline-primary"
                onClick={() => navigate(`/produit/${product.id}`)}
              >
                Voir
              </button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}

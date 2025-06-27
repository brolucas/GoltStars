import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";

export default function ProductList() {
  const navigate = useNavigate();
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [categorySearch, setCategorySearch] = useState("");

  useEffect(() => {
    axios.get("https://localhost/api/products")
      .then((res) => {
        setProducts(res.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setError("Erreur de chargement des produits");
        setLoading(false);
      });

    axios.get("https://localhost/api/categories")
      .then((res) => {
        setCategories(res.data); // Format : { 1: "Maillots", 2: "Cartes" }
      })
      .catch((err) => {
        console.error(err);
        setError("Erreur de chargement des catégories");
      });
  }, []);

  const allCategories = Object.values(categories);

  const filteredCategories = allCategories.filter((cat) =>
    cat.toLowerCase().includes(categorySearch.toLowerCase())
  );

  const toggleCategory = (cat) => {
    setSelectedCategories((prev) =>
      prev.includes(cat)
        ? prev.filter((c) => c !== cat)
        : [...prev, cat]
    );
  };

  const filteredProducts =
    selectedCategories.length === 0
      ? products
      : products.filter((p) =>
          p.categories &&
          p.categories.some((catId) => selectedCategories.includes(categories[catId]))
        );

  return (
    <div className="container py-5">
      <button
        className="btn btn-outline-primary"
        onClick={() => navigate(`/admin/create`)}
        style={{ position: "absolute", top: "20px", right: "20px" }}
      >
        Administrateur
      </button>

      <section className="text-center mb-5">
        <h1 className="display-4 fw-bold">Le prestige sportif</h1>
        <p className="lead text-muted mx-auto" style={{ maxWidth: "700px" }}>
          Découvrez une sélection unique de cartes, maillots et objets de collection,
          mêlant innovation, beauté et rareté. Chaque pièce raconte une histoire. La vôtre commence ici.
        </p>
        <hr className="w-25 mx-auto my-4" />
        <p className="fst-italic text-secondary">
          "Là où le sport devient légende. Là où le style devient collector."
        </p>
      </section>

      {/* Filtrage par catégories */}
      <div className="mb-4">
        <div className="mb-2 d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center">
          <h6 className="mb-0 text-muted">Filtrer par catégorie :</h6>
          <input
            type="search"
            className="form-control form-control-sm w-100 w-md-auto"
            placeholder="Rechercher une catégorie..."
            value={categorySearch}
            onChange={(e) => setCategorySearch(e.target.value)}
            style={{ maxWidth: "300px" }}
          />
        </div>

        <div className="d-flex flex-wrap gap-2 mt-2">
          {filteredCategories.map((cat) => (
            <button
              key={cat}
              className={`btn btn-sm ${
                selectedCategories.includes(cat)
                  ? "btn-primary"
                  : "btn-outline-secondary"
              }`}
              onClick={() => toggleCategory(cat)}
            >
              {cat}
            </button>
          ))}
          {filteredCategories.length === 0 && (
            <span className="text-muted">Aucune catégorie trouvée</span>
          )}
        </div>
      </div>

      {loading && <p className="text-center">Chargement des produits...</p>}
      {error && <p className="text-center text-danger">{error}</p>}

      <div className="row">
        {filteredProducts.map((product) => (
          <div className="col-md-6 col-lg-4 mb-4" key={product.Id}>
            <div className="card shadow-sm h-100 border-0">
              <div
                className="position-relative"
                style={{
                  height: "500px",
                  width: "100%",
                  overflow: "hidden",
                  borderTopLeftRadius: "0.5rem",
                  borderTopRightRadius: "0.5rem",
                }}
              >
                <img
                  src={product.Url
                    ? product.Url
                    : "https://via.placeholder.com/300x400?text=Image+non+disponible"}
                  alt={product.Nom}
                  className="w-100 h-100"
                  style={{ objectFit: "cover" }}
                />
              </div>
              <div className="card-body d-flex flex-column justify-content-between">
                <h5 className="card-title text-primary text-center">{product.Nom}</h5>

                {product.categories && (
                  <div className="text-center mb-2">
                    {product.categories.map((catId) => (
                      <span key={catId} className="badge bg-light text-dark me-2 border">
                        {categories[catId] || `Catégorie ${catId}`}
                      </span>
                    ))}
                  </div>
                )}

                <p className="text-center text-muted mb-2">
                  <strong>Prix :</strong> {product.Prix} €
                </p>
                <div className="d-grid mt-auto">
                  <button
                    className="btn btn-outline-primary"
                    onClick={() => navigate(`/produit/${product.Id}`)}
                  >
                    Voir le produit
                  </button>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>

      {filteredProducts.length === 0 && !loading && !error && (
        <p className="text-muted text-center mt-4">
          Aucun produit ne correspond aux filtres sélectionnés.
        </p>
      )}
    </div>
  );
}

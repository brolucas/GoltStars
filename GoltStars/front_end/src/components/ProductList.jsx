import { useState } from "react";
import { products } from "../data/products";
import { useNavigate } from "react-router-dom";

export default function ProductList() {
  const navigate = useNavigate();
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [categorySearch, setCategorySearch] = useState("");

  // Toutes les catégories (dédiquées à la recherche + affichage boutons)
  const allCategories = Array.from(
    new Set(products.flatMap((p) => p.categories))
  );

  // Liste filtrée selon la recherche
  const filteredCategories = allCategories.filter((cat) =>
    cat.toLowerCase().includes(categorySearch.toLowerCase())
  );

  // Toggle (ajouter/enlever) une catégorie
  const toggleCategory = (cat) => {
    setSelectedCategories((prev) =>
      prev.includes(cat)
        ? prev.filter((c) => c !== cat)
        : [...prev, cat]
    );
  };

  // Filtrage des produits selon sélection
  const filteredProducts =
    selectedCategories.length === 0
      ? products
      : products.filter((p) =>
          selectedCategories.every((cat) => p.categories.includes(cat))
        );

  return (
    <div className="container py-5">
      {/* Intro */}
       <button
                    className="btn btn-outline-primary"
                    onClick={() => navigate(`/admin/create`)}
                    style={{ position: "absolute", top: "20px", right: "20px" }}
                  >
                   Administrateur
                  </button>
      {/* SECTION HERO / INTRO */}
      <section className="text-center mb-5">
        <h1 className="display-4 fw-bold">✨ GoltStars – Le prestige sportif</h1>
        <p className="lead text-muted mx-auto" style={{ maxWidth: "700px" }}>
          Découvrez une sélection unique de cartes, maillots et objets de collection,
          mêlant innovation, beauté et rareté. Chaque pièce raconte une histoire. La vôtre commence ici.
        </p>
        <hr className="w-25 mx-auto my-4" />
        <p className="fst-italic text-secondary">
          "Là où le sport devient légende. Là où le style devient collector."
        </p>
      </section>

      {/* --- BARRE DE RECHERCHE + FILTRES --- */}
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

      {/* LISTE DES PRODUITS */}
      <div className="row">
        {filteredProducts.map((product) => (
          <div className="col-md-6 col-lg-4 mb-4" key={product.id}>
            <div className="card shadow-sm h-100 border-0">
              <div
                className="position-relative"
                style={{
                  height: "500PX",
                  width: "100%",
                  overflow: "hidden",
                  borderTopLeftRadius: "0.5rem",
                  borderTopRightRadius: "0.5rem",
                }}
              >
                <img
                  src={product.image}
                  alt={product.name}
                  className="w-100 h-100"
                  style={{ objectFit: "cover" }}
                />
              </div>
              <div className="card-body d-flex flex-column justify-content-between">
                <h5 className="card-title text-primary text-center">{product.name}</h5>
                <p className="text-secondary text-center" style={{ fontSize: "0.9rem" }}>
                  {product.description}
                </p>
                <p className="text-center text-muted mb-2">
                  <strong>Prix :</strong> {product.price} €<br />
                  
                </p>
                <div className="d-grid mt-auto">
                  <button
                    className="btn btn-outline-primary"
                    onClick={() => navigate(`/produit/${product.id}`)}
                  >
                    Voir le produit
                  </button>
                  <button 
                    className="btn btn-outline-secondary mt-2"
                    onClick={() => navigate(`/admin/edit/${product.id}`)}
                    >
                     ✏️ Modifier
                  </button>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>

      {filteredProducts.length === 0 && (
        <p className="text-muted text-center mt-4">
          Aucun produit ne correspond aux filtres sélectionnés.
        </p>
      )}
    </div>
  );
}

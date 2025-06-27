import { Routes, Route, useNavigate } from "react-router-dom";
import ProductList from "./components/ProductList";
import ProductDetails from "./components/ProductDetails";
import Paiement from "./components/Paiement";
import Confirmation from "./components/Confirmation";
import ProductCreate from "./components/ProductCreate";
import ProductEdit from "./components/ProductEdit";

function App() {
  const navigate = useNavigate();
  return (
    <div className="container py-4">
      <header style={{ textAlign: "center", marginBottom: "2rem" }}>
        <img
          src="/images/LogoGoltStars.png"
          alt="Logo GoltStars"
          style={{ height: "150px", cursor: "pointer" }}
          onClick={() => navigate("/")}
        />
      </header>
      <Routes>
        <Route path="/" element={<ProductList />} />
        <Route path="/produit/:id" element={<ProductDetails />} />
        <Route path="/paiement" element={<Paiement />} />
        <Route path="/confirmation" element={<Confirmation />} />
        <Route path="/admin/create" element={<ProductCreate />} />
        <Route path="/admin/edit/:id" element={<ProductEdit />} />
      </Routes>
    </div>
  );
}

export default App;

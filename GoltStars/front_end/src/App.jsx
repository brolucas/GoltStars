import { Routes, Route } from 'react-router-dom'
import ProductList from './components/ProductList'
import ProductDetails from './components/ProductDetails'
import Paiement from './components/Paiement'
import Confirmation from './components/Confirmation'
import ProductCreate from './components/ProductCreate'
import ProductEdit from './components/ProductEdit'

function App() {
  return (
    <div className="container py-4">
    
      <Routes>
        <Route path="/" element={<ProductList />} />
        <Route path="/produit/:id" element={<ProductDetails />} />
        <Route path="/paiement" element={<Paiement />} />
        <Route path="/confirmation" element={<Confirmation />} />
        <Route path="/admin/create" element={<ProductCreate />} />
        <Route path="/admin/edit/:id" element={<ProductEdit />} />
      </Routes>
    </div>
  )
}

export default App

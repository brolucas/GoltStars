import { Routes, Route } from 'react-router-dom'
import ProductList from './components/ProductList'
import ProductDetails from './components/ProductDetails'
import Paiement from './components/Paiement'
import Confirmation from './components/Confirmation'

function App() {
  return (
    <div className="container py-4">
      <h1 className="text-center mb-4">GoltStars</h1>
      <Routes>
        <Route path="/" element={<ProductList />} />
        <Route path="/produit/:id" element={<ProductDetails />} />
        <Route path="/paiement" element={<Paiement />} />
        <Route path="/confirmation" element={<Confirmation />} />
      </Routes>
    </div>
  )
}

export default App

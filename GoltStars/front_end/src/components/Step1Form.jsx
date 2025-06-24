import { useState } from "react";

export default function Step1Form({ onNext }) {
  const [form, setForm] = useState({ nom: "", prenom: "", email: "", adresse: "" });

  const handleSubmit = (e) => {
    e.preventDefault();
    onNext(form); // on passe au step suivant
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Infos client</h2>
      <input placeholder="Nom" onChange={e => setForm({ ...form, nom: e.target.value })} required />
      <input placeholder="Prénom" onChange={e => setForm({ ...form, prenom: e.target.value })} required />
      <input placeholder="Email" type="email" onChange={e => setForm({ ...form, email: e.target.value })} required />
      <input placeholder="Adresse de livraison" onChange={e => setForm({ ...form, adresse: e.target.value })} required />
      <button type="submit">Suivant</button>
    </form>
  );
}

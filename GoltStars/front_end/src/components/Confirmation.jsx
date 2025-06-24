import { useLocation } from "react-router-dom";

export default function Confirmation() {
  const { state } = useLocation();
  const message = state?.message || "Merci pour votre achat !";

  return (
    <div className="container py-4">
      <h2>Confirmation</h2>
      <p>{message}</p>
    </div>
  );
}

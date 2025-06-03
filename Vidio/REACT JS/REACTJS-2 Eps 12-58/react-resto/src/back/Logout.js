import { useEffect } from "react";
import { useNavigate, Link } from "react-router-dom";

export default function Logout() {
  const navigate = useNavigate();

  useEffect(() => {
    sessionStorage.clear();
    navigate("/login");
  }, [navigate]);

  return null;
}

// back/Logout component
import Logout from "./back/Logout";

// In your routing configuration
<Route path="/logout" element={<Logout />} />;

<Link className="btn btn-danger btn-sm" to="/logout">
  Logout
</Link>;

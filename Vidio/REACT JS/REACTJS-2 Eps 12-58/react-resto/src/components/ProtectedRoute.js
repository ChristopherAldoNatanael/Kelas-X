import React from "react";
import { Navigate } from "react-router-dom";

const ProtectedRoute = ({ children }) => {
  const token = sessionStorage.getItem("token");
  const level = sessionStorage.getItem("level");
  const allowedLevels = ["admin", "kasir", "koki"];

  if (!token || !allowedLevels.includes(level)) {
    console.log("Access denied:", { token, level });
    return <Navigate to="/login" replace />;
  }

  return children;
};

export default ProtectedRoute;

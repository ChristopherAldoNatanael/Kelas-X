import React from "react";
import { Navigate } from "react-router-dom";

const CustomerProtectedRoute = ({ children }) => {
  const isAuthenticated = sessionStorage.getItem("customer_token");
  const idpelanggan = sessionStorage.getItem("customer_idpelanggan");

  if (!isAuthenticated || !idpelanggan) {
    return <Navigate to="/login-customer" replace />;
  }

  return children;
};

export default CustomerProtectedRoute;

import React from "react";
import { Link, useNavigate } from "react-router-dom";

export default function AdminNavbar() {
  const navigate = useNavigate();
  const email = sessionStorage.getItem("email");
  const level = sessionStorage.getItem("level");
  const name = sessionStorage.getItem("name"); // Tambahkan ini

  const handleLogout = () => {
    sessionStorage.clear();
    navigate("/login");
  };

  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div className="container-fluid">
        <Link className="navbar-brand" to="/admin">
          Dashboard
        </Link>
        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span className="navbar-toggler-icon"></span>
        </button>
        <div className="collapse navbar-collapse" id="navbarNav">
          <ul className="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
            {/* Menu Kategori: hanya admin */}
            {level === "admin" && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/kategori">
                  Kategori
                </Link>
              </li>
            )}
            {/* Menu Menu: hanya admin */}
            {level === "admin" && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/menu">
                  Menu
                </Link>
              </li>
            )}
            {/* Menu Pelanggan: hanya admin */}
            {level === "admin" && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/pelanggan">
                  Pelanggan
                </Link>
              </li>
            )}
            {/* Menu Order: admin & kasir */}
            {(level === "admin" || level === "kasir") && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/order">
                  Order
                </Link>
              </li>
            )}
            {/* Menu Order Detail: admin, kasir, koki */}
            {(level === "admin" || level === "kasir" || level === "koki") && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/order-detail">
                  Order Detail
                </Link>
              </li>
            )}
            {/* Menu User: hanya admin */}
            {level === "admin" && (
              <li className="nav-item">
                <Link className="nav-link" to="/admin/user">
                  User
                </Link>
              </li>
            )}
          </ul>

          {/* User Info & Logout Button */}
          <ul className="navbar-nav ms-auto align-items-center">
            <li className="nav-item me-3">
              <span className="nav-link text-white">
                <i className="fas fa-user-circle me-2"></i>
                {name || email} ({level})
              </span>
            </li>
            <li className="nav-item">
              <button className="btn btn-danger btn-sm" onClick={handleLogout}>
                <i className="fas fa-sign-out-alt me-2"></i>
                Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  );
}

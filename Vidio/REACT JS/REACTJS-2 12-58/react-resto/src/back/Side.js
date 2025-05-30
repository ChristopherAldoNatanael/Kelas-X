import React from "react";
import { Link } from "react-router-dom";

const Side = () => {
  return (
    <div className="card">
      <div className="card-body">
        <nav className="nav flex-column">
          <h5 className="p-2">Menu Aplikasi</h5>
          <Link to="/admin/kategori" className="nav-link">
            Kategori
          </Link>
          <Link to="/admin/menu" className="nav-link">
            Menu
          </Link>
          <Link to="/admin/pelanggan" className="nav-link">
            Pelanggan
          </Link>
          <Link to="/admin/order" className="nav-link">
            Order
          </Link>
          <Link to="/admin/order-detail" className="nav-link">
            OrderDetail
          </Link>
          <Link to="/admin/user" className="nav-link">
            Admin
          </Link>
        </nav>
      </div>
    </div>
  );
};

export default Side;

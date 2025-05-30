import React from "react";
import { Link } from "react-router-dom";

export default function Nav() {
  return (
    <div className="navbar navbar-light bg-light">
      <div className="container-fluid">
        <Link to="/admin" className="navbar-brand">
          Admin Panel Resto
        </Link>
        <ul className="navbar-nav">
          <li className="nav-item">
            <Link className="nav-link" to="/order-detail">
              Order Detail
            </Link>
          </li>
        </ul>
      </div>
    </div>
  );
}

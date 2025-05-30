import React, { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import AdminNavbar from "./AdminNavbar";
import axios from "axios";
import { link } from "../axios/link";

export default function Main() {
  const token = sessionStorage.getItem("token");

  const [counts, setCounts] = useState({
    kategori: 0,
    menu: 0,
    pelanggan: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchCounts = async () => {
      try {
        const [kategoriRes, menuRes, pelangganRes] = await Promise.all([axios.get(`${link}/kategori`), axios.get(`${link}/menu`), axios.get(`${link}/pelanggan`)]);
        setCounts({
          kategori: Array.isArray(kategoriRes.data) ? kategoriRes.data.length : Array.isArray(kategoriRes.data.data) ? kategoriRes.data.data.length : Array.isArray(kategoriRes.data.result) ? kategoriRes.data.result.length : 0,
          menu: Array.isArray(menuRes.data) ? menuRes.data.length : Array.isArray(menuRes.data.data) ? menuRes.data.data.length : Array.isArray(menuRes.data.result) ? menuRes.data.result.length : 0,
          pelanggan: Array.isArray(pelangganRes.data) ? pelangganRes.data.length : Array.isArray(pelangganRes.data.data) ? pelangganRes.data.data.length : Array.isArray(pelangganRes.data.result) ? pelangganRes.data.result.length : 0,
        });
      } catch (err) {
        setCounts({ kategori: 0, menu: 0, pelanggan: 0 });
      } finally {
        setLoading(false);
      }
    };
    fetchCounts();
  }, []);

  if (!token || token === "null" || token === "undefined") {
    return <Navigate to="/login" replace />;
  }

  return (
    <div>
      <AdminNavbar />
      <h2>Dashboard Admin</h2>
      <div className="row mt-4">
        <div className="col-md-4">
          <div className="card text-white bg-primary">
            <div className="card-header">Kategori</div>
            <div className="card-body">
              <h5 className="card-title">{loading ? "..." : counts.kategori} Kategori</h5>
              <p className="card-text">Kategori makanan dan minuman</p>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card text-white bg-success">
            <div className="card-header">Menu</div>
            <div className="card-body">
              <h5 className="card-title">{loading ? "..." : counts.menu} Menu</h5>
              <p className="card-text">Menu makanan dan minuman</p>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card text-white bg-warning">
            <div className="card-header">Pelanggan</div>
            <div className="card-body">
              <h5 className="card-title">{loading ? "..." : counts.pelanggan} Pelanggan</h5>
              <p className="card-text">Pelanggan terdaftar</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

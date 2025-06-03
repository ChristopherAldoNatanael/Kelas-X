import React, { useEffect, useState } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

export default function CartComponent() {
  const [cart, setCart] = useState([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState("");
  const navigate = useNavigate();

  const customerEmail = sessionStorage.getItem("customer_email");
  const [idpelanggan, setIdpelanggan] = useState(null);

  // Ambil idpelanggan dari backend berdasarkan email
  useEffect(() => {
    if (!customerEmail) {
      navigate("/login-customer");
      return;
    }
    const fetchId = async () => {
      try {
        const res = await axios.get(`${link}/pelanggan/email/${customerEmail}`);
        // Cek response backend
        if (res.data && res.data.data && res.data.data.idpelanggan) {
          setIdpelanggan(res.data.data.idpelanggan);
          setMessage("");
        } else {
          setMessage("Pelanggan tidak ditemukan. Silakan login ulang.");
        }
      } catch (err) {
        setMessage("Gagal mendapatkan data pelanggan. Pastikan email sudah terdaftar.");
      }
    };
    fetchId();
  }, [customerEmail, navigate]);

  // Ambil data cart dari backend
  useEffect(() => {
    if (!idpelanggan) return;
    setLoading(true);
    const fetchCart = async () => {
      try {
        const res = await axios.get(`${link}/cart/${idpelanggan}`);
        if (res.data && Array.isArray(res.data.data)) {
          setCart(res.data.data);
        } else if (Array.isArray(res.data)) {
          setCart(res.data);
        } else {
          setCart([]);
        }
        setMessage("");
      } catch (err) {
        setMessage("Gagal mengambil data keranjang.");
        setCart([]);
      } finally {
        setLoading(false);
      }
    };
    fetchCart();
  }, [idpelanggan]);

  // Hapus item dari cart
  const handleDelete = async (idcart) => {
    if (!window.confirm("Yakin ingin menghapus item ini dari keranjang?")) return;
    try {
      await axios.delete(`${link}/cart/item/${idcart}`);
      setCart((prev) => prev.filter((item) => item.idcart !== idcart));
    } catch (err) {
      setMessage("Gagal menghapus item.");
    }
  };

  // Kosongkan cart
  const handleClearCart = async () => {
    if (!window.confirm("Yakin ingin mengosongkan keranjang?")) return;
    try {
      await axios.delete(`${link}/cart/clear/${idpelanggan}`);
      setCart([]);
    } catch (err) {
      setMessage("Gagal mengosongkan keranjang.");
    }
  };

  // Hitung total harga
  const total = cart.reduce((sum, item) => sum + (item.menu?.harga || 0) * item.qty, 0);

  return (
    <div className="container py-4">
      <h2 className="mb-4 text-center fw-bold">
        <i className="bi bi-cart"></i> Keranjang Belanja
      </h2>
      {message && <div className="alert alert-danger text-center">{message}</div>}
      {loading ? (
        <div className="text-center my-5">
          <div className="spinner-border text-primary" role="status"></div>
        </div>
      ) : cart.length === 0 ? (
        <div className="alert alert-warning text-center">Keranjang Anda kosong.</div>
      ) : (
        <>
          <div className="table-responsive">
            <table className="table table-bordered align-middle">
              <thead className="table-dark">
                <tr>
                  <th>No</th>
                  <th>Menu</th>
                  <th>Gambar</th>
                  <th>Harga</th>
                  <th>Qty</th>
                  <th>Subtotal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {cart.map((item, idx) => (
                  <tr key={item.idcart}>
                    <td>{idx + 1}</td>
                    <td>{item.menu?.menu || "-"}</td>
                    <td>
                      <img
                        src={item.menu?.gambar ? `http://localhost:8000/upload/${item.menu.gambar}` : "https://via.placeholder.com/80x80?text=No+Image"}
                        alt={item.menu?.menu}
                        width={80}
                        height={80}
                        style={{ objectFit: "cover", borderRadius: 8 }}
                      />
                    </td>
                    <td>Rp. {Number(item.menu?.harga || 0).toLocaleString("id-ID")}</td>
                    <td>{item.qty}</td>
                    <td>Rp. {Number((item.menu?.harga || 0) * item.qty).toLocaleString("id-ID")}</td>
                    <td>
                      <button className="btn btn-danger btn-sm" onClick={() => handleDelete(item.idcart)}>
                        <i className="bi bi-trash"></i> Hapus
                      </button>
                    </td>
                  </tr>
                ))}
                <tr>
                  <td colSpan={5} className="text-end fw-bold">
                    Total
                  </td>
                  <td colSpan={2} className="fw-bold text-primary">
                    Rp. {Number(total).toLocaleString("id-ID")}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div className="d-flex justify-content-between mt-3">
            <button className="btn btn-outline-danger" onClick={handleClearCart}>
              <i className="bi bi-x-circle"></i> Kosongkan Keranjang
            </button>
            <button className="btn btn-success" onClick={() => alert("Fitur checkout belum diimplementasikan.")}>
              <i className="bi bi-cash-stack"></i> Checkout
            </button>
          </div>
        </>
      )}
    </div>
  );
}

import React, { useState } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

export default function RegisterComponent() {
  const [form, setForm] = useState({
    pelanggan: "",
    email: "",
    alamat: "",
    telp: "",
    password: "",
  });
  const navigate = useNavigate();

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(`${link}/register-customer`, {
        pelanggan: form.pelanggan,
        email: form.email,
        alamat: form.alamat,
        telp: form.telp,
        password: form.password,
      });

      if (response.data.status === "success") {
        alert("Registrasi berhasil!");
        navigate("/login-customer");
      }
    } catch (error) {
      console.error("API Error:", error);
      alert(error.response?.data?.message || "Registrasi gagal, silakan coba lagi.");
    }
  };

  return (
    <div className="container">
      <div className="row justify-content-center mt-5">
        <div className="col-md-5">
          <div className="card shadow">
            <div className="card-body">
              <h3 className="text-center mb-4">Register Pelanggan</h3>
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label">Nama Lengkap</label>
                  <input type="text" className="form-control" name="pelanggan" value={form.pelanggan} onChange={handleChange} placeholder="Nama lengkap" required />
                </div>
                <div className="mb-3">
                  <label className="form-label">Email</label>
                  <input type="email" className="form-control" name="email" value={form.email} onChange={handleChange} placeholder="Email aktif" required />
                </div>
                <div className="mb-3">
                  <label className="form-label">Alamat</label>
                  <input type="text" className="form-control" name="alamat" value={form.alamat} onChange={handleChange} placeholder="Alamat lengkap" required />
                </div>
                <div className="mb-3">
                  <label className="form-label">No. Telepon</label>
                  <input type="text" className="form-control" name="telp" value={form.telp} onChange={handleChange} placeholder="Nomor telepon" required />
                </div>
                <div className="mb-3">
                  <label className="form-label">Password</label>
                  <input type="password" className="form-control" name="password" value={form.password} onChange={handleChange} placeholder="Password" required />
                </div>
                <button type="submit" className="btn btn-primary w-100">
                  Register
                </button>
              </form>
              <div className="mt-3 text-center">
                Sudah punya akun?{" "}
                <a href="/login-customer" className="text-decoration-none">
                  Login di sini
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

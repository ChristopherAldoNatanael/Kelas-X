import React, { useState } from "react";
import axios from "axios";
import { link } from "../axios/link";

export default function LoginCustomer() {
  const [form, setForm] = useState({ email: "", password: "" });
  const [message, setMessage] = useState("");

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage("");

    try {
      const response = await axios.post(`${link}/login-customer`, {
        email: form.email,
        password: form.password,
      });

      console.log("Response data:", response.data);

      // Check for status instead of success
      if (response.data.status === "success" && response.data.data) {
        const userData = response.data.data;
        console.log("Login successful, preparing to redirect...");

        // Store user data from the correct response structure
        sessionStorage.setItem("customer_idpelanggan", userData.customer_id);
        sessionStorage.setItem("customer_email", userData.customer_email);
        sessionStorage.setItem("customer_name", userData.customer_name);

        console.log("Session data saved");
        setMessage("Login berhasil!");

        // Add small delay before redirect to ensure state updates
        setTimeout(() => {
          window.location.href = "/";
        }, 500);
      } else {
        setMessage(response.data.message || "Login gagal");
      }
    } catch (err) {
      console.error("Login error:", err);
      setMessage("Terjadi kesalahan saat login. Periksa koneksi Anda.");
    }
  };

  return (
    <div className="container">
      <div className="row justify-content-center mt-5">
        <div className="col-md-4">
          <div className="card shadow">
            <div className="card-body">
              <h3 className="text-center mb-4">Login</h3>
              {message && <div className={`alert ${message === "Login berhasil!" ? "alert-success" : "alert-danger"}`}>{message}</div>}
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label">Email</label>
                  <input type="email" className="form-control" name="email" value={form.email} onChange={handleChange} placeholder="Masukkan email" required />
                </div>
                <div className="mb-3">
                  <label className="form-label">Password</label>
                  <input type="password" className="form-control" name="password" value={form.password} onChange={handleChange} placeholder="Masukkan password" required />
                </div>
                <button type="submit" className="btn btn-primary w-100">
                  Login
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

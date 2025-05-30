import React, { useState } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

const Login = () => {
  const [form, setForm] = useState({ email: "", password: "" });
  const [message, setMessage] = useState("");
  const navigate = useNavigate();

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
      const response = await axios.post(`${link}/login`, form);
      if (response.data.status === "success") {
        // Simpan token, email, dan level ke sessionStorage
        sessionStorage.setItem("token", response.data.token);
        sessionStorage.setItem("email", response.data.data.email);
        sessionStorage.setItem("level", response.data.data.level);
        setMessage("Login berhasil!");
        navigate("/admin");
      } else {
        setMessage(response.data.message || "Login gagal");
      }
    } catch (err) {
      setMessage(err.response?.data?.message || "Login gagal: Email atau password salah / user dibanned");
    }
  };

  return (
    <div className="container">
      <div className="row justify-content-center mt-5">
        <div className="col-md-4">
          <div className="card shadow">
            <div className="card-body">
              <h3 className="text-center mb-4">Login Admin</h3>
              {message && <div className={`alert ${message.includes("berhasil") ? "alert-success" : "alert-danger"}`}>{message}</div>}
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
};

export default Login;

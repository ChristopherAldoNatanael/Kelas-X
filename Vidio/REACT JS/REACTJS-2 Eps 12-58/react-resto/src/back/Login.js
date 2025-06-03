import React, { useState, useEffect } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

const Login = () => {
  const [form, setForm] = useState({ email: "", password: "" });
  const [message, setMessage] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();

  // Check if already logged in
  useEffect(() => {
    const token = sessionStorage.getItem("token");
    const level = sessionStorage.getItem("level");
    if (token && level) {
      navigate("/admin");
    }
  }, [navigate]);

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage("");
    setIsLoading(true);

    try {
      const response = await axios.post(`${link}/login`, form);
      console.log("Login Response:", response.data);

      if (response.data.status === "success") {
        // Store session data
        sessionStorage.clear();
        sessionStorage.setItem("token", response.data.token);
        sessionStorage.setItem("email", response.data.data.email);
        sessionStorage.setItem("level", response.data.data.level);
        sessionStorage.setItem("name", response.data.data.name);
        sessionStorage.setItem("user", JSON.stringify(response.data.data));

        setMessage("Login berhasil!");

        // Navigate based on user level
        const userLevel = response.data.data.level;
        if (["admin", "kasir", "koki"].includes(userLevel)) {
          navigate("/admin", { replace: true });
        } else {
          setMessage("Level akses tidak valid");
          sessionStorage.clear();
        }
      } else {
        setMessage(response.data.message || "Login gagal");
      }
    } catch (err) {
      console.error("Login Error:", err);
      setMessage(err.response?.data?.message || "Login gagal: Email atau password salah");
      sessionStorage.clear();
    } finally {
      setIsLoading(false);
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
                  <input type="email" className="form-control" name="email" value={form.email} onChange={handleChange} placeholder="Masukkan email" required disabled={isLoading} />
                </div>
                <div className="mb-3">
                  <label className="form-label">Password</label>
                  <input type="password" className="form-control" name="password" value={form.password} onChange={handleChange} placeholder="Masukkan password" required disabled={isLoading} />
                </div>
                <button type="submit" className="btn btn-primary w-100" disabled={isLoading}>
                  {isLoading ? (
                    <span>
                      <span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                    </span>
                  ) : (
                    "Login"
                  )}
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

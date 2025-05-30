import React, { useState } from "react";
import useGet from "../hooks/useGet";
import axios from "axios";
import { link } from "../axios/link";

const User = () => {
  const { isi: users, loading, error, fetchData } = useGet("user");
  const [message, setMessage] = useState("");
  const [modalOpen, setModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    level: "admin",
  });

  const openModal = () => setModalOpen(true);
  const closeModal = () => setModalOpen(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(`${link}/register`, formData);
      if (response?.data?.status === "success") {
        setMessage("User berhasil ditambahkan!");
        setFormData({
          name: "",
          email: "",
          password: "",
          level: "admin",
        });
        closeModal();
        fetchData();
      } else {
        setMessage("Terjadi kesalahan: Format response tidak sesuai");
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || "Terjadi kesalahan saat menambahkan user";
      setMessage(`Error: ${errorMessage}`);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm("Yakin ingin menghapus data ini?")) {
      try {
        const response = await axios.delete(`${link}/user/${id}`);
        if (response.data.status === "success") {
          setMessage("Data berhasil dihapus!");
          fetchData();
        }
      } catch (err) {
        setMessage("Gagal menghapus data: " + (err.response?.data?.message || err.message));
      }
    }
  };

  // Fungsi untuk toggle status user (aktif <-> banned)
  const handleToggleStatus = async (id) => {
    // Cari user yang akan diubah statusnya
    const user = users.find((u) => u.id === id);
    if (!user) return;

    // Toggle status: jika 1 jadi 0, jika 0 jadi 1
    const newStatus = user.status === 1 ? 0 : 1;

    try {
      const response = await axios.put(`${link}/user/${id}`, { status: newStatus });
      if (response.data.status === "success") {
        setMessage("Status user berhasil diubah!");
        fetchData();
      } else {
        setMessage("Gagal mengubah status user");
      }
    } catch (err) {
      setMessage("Gagal mengubah status user: " + (err.response?.data?.message || err.message));
    }
  };

  let no = 1;

  return (
    <div className="container mt-4">
      <div className="d-flex justify-content-between align-items-center mb-3">
        <h2>Menu User</h2>
        <button className="btn btn-primary" onClick={openModal}>
          Tambah
        </button>
      </div>

      {/* Modal */}
      {modalOpen && (
        <div className="modal fade show" style={{ display: "block", background: "rgba(0,0,0,0.5)" }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <form onSubmit={handleSubmit}>
                <div className="modal-header">
                  <h5 className="modal-title">Input Data User</h5>
                  <button type="button" className="btn-close" onClick={closeModal}></button>
                </div>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label">Nama</label>
                    <input type="text" className="form-control" name="name" value={formData.name} onChange={handleChange} required />
                  </div>
                  <div className="mb-3">
                    <label className="form-label">Email</label>
                    <input type="email" className="form-control" name="email" value={formData.email} onChange={handleChange} required />
                  </div>
                  <div className="mb-3">
                    <label className="form-label">Password</label>
                    <input type="password" className="form-control" name="password" value={formData.password} onChange={handleChange} required />
                  </div>
                  <div className="mb-3">
                    <label className="form-label">Level</label>
                    <select className="form-select" name="level" value={formData.level} onChange={handleChange} required>
                      <option value="admin">Admin</option>
                      <option value="koki">Koki</option>
                      <option value="kasir">Kasir</option>
                    </select>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-secondary" onClick={closeModal}>
                    Tutup
                  </button>
                  <button type="submit" className="btn btn-success">
                    Simpan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {modalOpen && <div className="modal-backdrop fade show" style={{ zIndex: 1040 }} onClick={closeModal}></div>}

      {message && <div className={`alert ${message.includes("berhasil") ? "alert-success" : "alert-danger"} mt-2`}>{message}</div>}
      {error && <div className="alert alert-danger">{error}</div>}

      <div className="table-responsive">
        <table className="table table-striped table-hover">
          <thead className="table-dark">
            <tr>
              <th>No</th>
              <th>Email</th>
              <th>Level</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan={5} className="text-center">
                  Loading...
                </td>
              </tr>
            ) : users && users.length > 0 ? (
              users.map((user) => (
                <tr key={user.id}>
                  <td>{no++}</td>
                  <td>{user.email}</td>
                  <td>
                    <span className={`badge ${user.level === "admin" ? "bg-primary" : user.level === "koki" ? "bg-success" : "bg-info"}`}>{user.level}</span>
                  </td>
                  <td>
                    <button className={`btn btn-sm ${user.status === 1 ? "btn-success" : "btn-danger"}`} onClick={() => handleToggleStatus(user.id)}>
                      {user.status === 1 ? "Aktif" : "Banned"}
                    </button>
                  </td>
                  <td>
                    <button className="btn btn-sm btn-danger" onClick={() => handleDelete(user.id)}>
                      Hapus
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={5} className="text-center">
                  Tidak ada data user
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default User;

import React, { useState } from "react";
import { link } from "../axios/link";
import axios from "axios";
import { useForm } from "react-hook-form";
import useGet from "../hooks/useGet";
import useDelete from "../hooks/useDelete";

export default function Kategori() {
  const { isi, loading, error, fetchData: refreshData } = useGet("kategori");
  const { hapusData } = useDelete();

  const [showForm, setShowForm] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [currentId, setCurrentId] = useState(null);

  const {
    register,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm();

  const save = async (data) => {
    try {
      let response;

      if (editMode) {
        response = await axios.put(`${link}/kategori/${currentId}`, data);
      } else {
        response = await axios.post(`${link}/kategori`, data);
      }

      if (response.data && response.data.message) {
        alert(response.data.message);
      }

      // Refresh data after save
      refreshData();

      reset();
      setShowForm(false);
      setEditMode(false);
      setCurrentId(null);
    } catch (error) {
      alert("Gagal menyimpan data: " + error.message);
    }
  };

  const handleEdit = (kategori) => {
    // Set form values
    setValue("kategori", kategori.kategori);
    setValue("keterangan", kategori.keterangan);

    // Set edit mode
    setEditMode(true);
    setCurrentId(kategori.idkategori);
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm("Apakah Anda yakin ingin menghapus kategori ini?")) {
      try {
        await hapusData("kategori", id);
        alert("Data berhasil dihapus");
        refreshData();
      } catch (error) {
        console.error("Delete error:", error);
        alert(error.message || "Gagal menghapus data");
      }
    }
  };

  if (loading) return <div>Loading data...</div>;
  if (error) return <div className="alert alert-danger">{error}</div>;

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Kategori</h2>
        <button
          className="btn btn-primary"
          onClick={() => {
            setShowForm(!showForm);
            if (!showForm) {
              reset();
              setEditMode(false);
              setCurrentId(null);
            }
          }}
        >
          {showForm ? "Tutup Form" : "Tambah Kategori"}
        </button>
      </div>

      {showForm && (
        <div className="card mb-4">
          <div className="card-header bg-primary text-white">
            <h5 className="m-0">{editMode ? "Edit Kategori" : "Form Kategori Baru"}</h5>
          </div>
          <div className="card-body">
            <form onSubmit={handleSubmit(save)}>
              <div className="mb-3">
                <label htmlFor="kategori" className="form-label">
                  Nama Kategori
                </label>
                <input type="text" className={`form-control ${errors.kategori ? "is-invalid" : ""}`} id="kategori" {...register("kategori", { required: "Nama kategori harus diisi" })} />
                {errors.kategori && <div className="invalid-feedback">{errors.kategori.message}</div>}
              </div>

              <div className="mb-3">
                <label htmlFor="keterangan" className="form-label">
                  Keterangan
                </label>
                <textarea className={`form-control ${errors.keterangan ? "is-invalid" : ""}`} id="keterangan" rows="3" {...register("keterangan", { required: "Keterangan harus diisi" })}></textarea>
                {errors.keterangan && <div className="invalid-feedback">{errors.keterangan.message}</div>}
              </div>

              <div className="d-flex gap-2">
                <button type="submit" className="btn btn-success">
                  {editMode ? "Update" : "Simpan"}
                </button>
                <button type="button" className="btn btn-secondary" onClick={() => reset()}>
                  Reset
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <p>Jumlah data: {isi.length}</p>
      <table className="table table-striped table-hover">
        <thead className="table-dark">
          <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {isi && isi.length > 0 ? (
            isi.map((val, index) => (
              <tr key={index}>
                <td>{index + 1}</td>
                <td>{val.kategori}</td>
                <td>{val.keterangan}</td>
                <td>
                  <button className="btn btn-warning btn-sm me-1" onClick={() => handleEdit(val)}>
                    Edit
                  </button>
                  <button className="btn btn-danger btn-sm" onClick={() => handleDelete(val.idkategori)}>
                    Hapus
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="4" className="text-center">
                Tidak ada data
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}

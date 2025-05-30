import React, { useState, useEffect } from "react";
import { link } from "../axios/link";
import axios from "axios";
import useGet from "../hooks/useGet";

const Pelanggan = () => {
  const { isi, loading, error, fetchData: refreshData } = useGet("pelanggan");

  const handleDelete = async (id) => {
    if (window.confirm("Apakah Anda yakin ingin menghapus pelanggan ini?")) {
      try {
        const response = await axios.delete(`${link}/pelanggan/${id}`);
        if (response.status === 200) {
          alert("Data berhasil dihapus");
          refreshData(); // Refresh the data after successful deletion
        }
      } catch (error) {
        console.error("Delete error:", error);
        alert(error.response?.data?.message || "Gagal menghapus data");
      }
    }
  };

  if (loading) return <div>Loading data...</div>;
  if (error) return <div className="alert alert-danger">{error}</div>;

  return (
    <div>
      <h2>Data Pelanggan</h2>
      <table className="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Pelanggan</th>
            <th>Alamat</th>
            <th>Telp</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {isi.map((val, index) => (
            <tr key={val.idpelanggan || index}>
              <td>{index + 1}</td>
              <td>{val.pelanggan}</td>
              <td>{val.alamat}</td>
              <td>{val.telp}</td>
              <td>
                <button onClick={() => handleDelete(val.idpelanggan)} className="btn btn-danger btn-sm">
                  Hapus
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default Pelanggan;

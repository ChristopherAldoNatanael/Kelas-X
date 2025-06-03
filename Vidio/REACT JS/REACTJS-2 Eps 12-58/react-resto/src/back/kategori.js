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
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Data Kategori</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola kategori menu restoran Anda</p>
        </div>
        <button
          className={`px-4 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200 ${
            showForm ? "bg-gray-200 hover:bg-gray-300 text-gray-700" : "bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white"
          }`}
          onClick={() => {
            setShowForm(!showForm);
            if (!showForm) {
              reset();
              setEditMode(false);
              setCurrentId(null);
            }
          }}
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {showForm ? <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /> : <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />}
          </svg>
          <span>{showForm ? "Tutup Form" : "Tambah Kategori"}</span>
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h3 className="text-lg font-semibold text-white">{editMode ? "Edit Kategori" : "Tambah Kategori Baru"}</h3>
          </div>
          <div className="p-6">
            <form onSubmit={handleSubmit(save)} className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
                <input
                  type="text"
                  className={`w-full rounded-lg border ${
                    errors.kategori ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                  } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                  {...register("kategori", { required: "Nama kategori harus diisi" })}
                />
                {errors.kategori && <p className="mt-1 text-sm text-red-600">{errors.kategori.message}</p>}
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea
                  rows="3"
                  className={`w-full rounded-lg border ${
                    errors.keterangan ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                  } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                  {...register("keterangan", { required: "Keterangan harus diisi" })}
                ></textarea>
                {errors.keterangan && <p className="mt-1 text-sm text-red-600">{errors.keterangan.message}</p>}
              </div>

              <div className="flex space-x-3">
                <button
                  type="submit"
                  className="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                >
                  {editMode ? "Update Kategori" : "Simpan Kategori"}
                </button>
                <button
                  type="button"
                  onClick={() => reset()}
                  className="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                >
                  Reset
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="p-4 border-b border-gray-200">
          <p className="text-sm text-gray-500">
            Total Kategori: <span className="font-medium text-gray-900">{isi.length}</span>
          </p>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50">
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {isi && isi.length > 0 ? (
                isi.map((val, index) => (
                  <tr key={index} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{val.kategori}</td>
                    <td className="px-6 py-4 text-sm text-gray-500">{val.keterangan}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                      <div className="flex space-x-2">
                        <button
                          onClick={() => handleEdit(val)}
                          className="px-3 py-1 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                          Edit
                        </button>
                        <button
                          onClick={() => handleDelete(val.idkategori)}
                          className="px-3 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4" className="px-6 py-4 text-center text-gray-500">
                    {loading ? (
                      <div className="flex justify-center items-center space-x-2">
                        <div className="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <span>Loading...</span>
                      </div>
                    ) : (
                      "Tidak ada data kategori"
                    )}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

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
          refreshData();
        }
      } catch (error) {
        console.error("Delete error:", error);
        alert(error.response?.data?.message || "Gagal menghapus data");
      }
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="flex items-center space-x-2">
          <div className="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
          <span className="text-gray-600">Loading data...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return <div className="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">{error}</div>;
  }

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Data Pelanggan</h1>
            <p className="text-sm text-gray-500 mt-1">Kelola data pelanggan restoran Anda</p>
          </div>
          <div className="bg-blue-50 rounded-lg px-4 py-2">
            <p className="text-sm text-blue-600 font-medium">Total Pelanggan: {isi.length}</p>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50">
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telp</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {isi.length > 0 ? (
                isi.map((val, index) => (
                  <tr key={val.idpelanggan || index} className="hover:bg-gray-50 transition-colors duration-200">
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                          <span className="text-blue-600 font-medium">{val.pelanggan.charAt(0).toUpperCase()}</span>
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{val.pelanggan}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{val.alamat}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{val.telp}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        onClick={() => handleDelete(val.idpelanggan)}
                        className="px-3 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                      >
                        Hapus
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="5" className="px-6 py-4 text-center text-gray-500">
                    Tidak ada data pelanggan
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default Pelanggan;

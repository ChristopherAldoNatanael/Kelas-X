import React, { useState } from "react";
import useGet from "../hooks/useGet";
import Side from "./Side";
import axios from "axios";
import { link } from "../axios/link";

const OrderDetail = () => {
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [filteredDetails, setFilteredDetails] = useState(null);
  const { isi: details, loading, error } = useGet("detail");

  const handleFilter = async () => {
    try {
      const response = await axios.get(`${link}/detail/filter`, {
        params: {
          start_date: startDate,
          end_date: endDate,
        },
      });
      setFilteredDetails(response.data);
    } catch (err) {
      console.error("Error filtering details:", err);
    }
  };

  const displayDetails = filteredDetails || details;

  return (
    <div className="flex">
      <Side />
      <div className="flex-1 p-6 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          {/* Header Section */}
          <div className="mb-6">
            <div className="flex justify-between items-center">
              <div>
                <h1 className="text-2xl font-bold text-gray-900">Detail Penjualan</h1>
                <p className="text-sm text-gray-500 mt-1">Lihat detail transaksi penjualan restoran Anda</p>
              </div>
            </div>
          </div>

          {/* Filter Section */}
          <div className="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                <input type="date" className="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500" value={startDate} onChange={(e) => setStartDate(e.target.value)} />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" className="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500" value={endDate} onChange={(e) => setEndDate(e.target.value)} />
              </div>
              <div>
                <button
                  className="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                  onClick={handleFilter}
                >
                  Filter Data
                </button>
              </div>
            </div>
          </div>

          {error && <div className="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">{error}</div>}

          {/* Table Section */}
          <div className="bg-white rounded-xl shadow-lg overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-gray-50">
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faktur</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Order</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {loading ? (
                    <tr>
                      <td colSpan={7} className="px-6 py-4 text-center">
                        <div className="flex justify-center items-center space-x-2">
                          <div className="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                          <span className="text-gray-500">Loading...</span>
                        </div>
                      </td>
                    </tr>
                  ) : displayDetails && displayDetails.length > 0 ? (
                    displayDetails.map((detail, idx) => (
                      <tr key={detail.iddetail} className="hover:bg-gray-50 transition-colors duration-200">
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{idx + 1}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{detail.idorder}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{detail.tglorder}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{detail.menu}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{detail.jumlah}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {Number(detail.hargajual).toLocaleString("id-ID")}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">Rp {Number(detail.hargajual * detail.jumlah).toLocaleString("id-ID")}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={7} className="px-6 py-4 text-center text-gray-500">
                        Tidak ada data detail order
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OrderDetail;

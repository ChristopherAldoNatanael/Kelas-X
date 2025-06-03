import React, { useState } from "react";
import useGet from "../hooks/useGet";
import axios from "axios";
import { link } from "../axios/link";
import Modal from "react-modal";

// Set modal app element for accessibility
Modal.setAppElement("#root");

const Order = () => {
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [filteredOrders, setFilteredOrders] = useState(null);
  const [modalIsOpen, setModalIsOpen] = useState(false);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [paymentAmount, setPaymentAmount] = useState("");
  const [kembalian, setKembalian] = useState(0);
  const [isProcessing, setIsProcessing] = useState(false);
  const { isi: orders, loading, error } = useGet("order");

  // Modal styles
  const customStyles = {
    content: {
      top: "50%",
      left: "50%",
      right: "auto",
      bottom: "auto",
      marginRight: "-50%",
      transform: "translate(-50%, -50%)",
      width: "500px",
      backgroundColor: "#fff",
      borderRadius: "1rem",
      padding: "0",
      border: "none",
      boxShadow: "0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
    },
    overlay: {
      backgroundColor: "rgba(0, 0, 0, 0.75)",
      zIndex: 1000,
    },
  };

  const handleFilter = async () => {
    try {
      const response = await axios.get(`${link}/order/filter`, {
        params: {
          start_date: startDate,
          end_date: endDate,
        },
      });
      setFilteredOrders(response.data);
    } catch (err) {
      console.error("Error filtering orders:", err);
    }
  };

  const openModal = (order) => {
    setSelectedOrder(order);
    setModalIsOpen(true);
  };

  const closeModal = () => {
    setModalIsOpen(false);
    setSelectedOrder(null);
    setPaymentAmount("");
    setKembalian(0);
    setIsProcessing(false);
  };

  const handlePaymentChange = (e) => {
    const amount = parseInt(e.target.value) || 0;
    setPaymentAmount(amount);
    if (selectedOrder) {
      setKembalian(amount - selectedOrder.total);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsProcessing(true);

    try {
      // Validate payment amount
      if (paymentAmount < selectedOrder.total) {
        alert("Pembayaran kurang dari total order!");
        return;
      }

      const hasil = {
        bayar: paymentAmount,
        kembali: kembalian,
        status: 1,
      };

      // Log for debugging
      console.log("Data yang akan dikirim:", hasil);

      const response = await axios.put(`${link}/order/${selectedOrder.idorder}`, hasil);

      if (response.data.status === "success") {
        alert(response.data.message);
        closeModal();
        window.location.reload();
      } else {
        throw new Error(response.data.message);
      }
    } catch (err) {
      console.error("Error processing payment:", err);
      alert(err.response?.data?.message || "Gagal memproses pembayaran!");
    } finally {
      setIsProcessing(false);
    }
  };

  const displayOrders = filteredOrders || orders;

  return (
    <div className="p-6 max-w-7xl mx-auto">
      <div className="mb-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Data Order</h1>
            <p className="text-sm text-gray-500 mt-1">Kelola data order restoran Anda</p>
          </div>
        </div>
      </div>

      {/* Filter Section */}
      <div className="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
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

      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50">
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Order</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bayar</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kembali</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={8} className="px-6 py-4 text-center">
                    <div className="flex justify-center items-center space-x-2">
                      <div className="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                      <span>Loading...</span>
                    </div>
                  </td>
                </tr>
              ) : displayOrders && displayOrders.length > 0 ? (
                displayOrders.map((order, idx) => (
                  <tr key={order.idorder || idx} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{idx + 1}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{order.pelanggan}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{order.tglorder}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {Number(order.total).toLocaleString("id-ID")}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {Number(order.bayar).toLocaleString("id-ID")}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {Number(order.kembali).toLocaleString("id-ID")}</td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {order.status === 1 ? (
                        <span className="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Lunas</span>
                      ) : (
                        <span className="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Belum Bayar</span>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {order.status === 0 && (
                        <button
                          onClick={() => openModal(order)}
                          className="px-3 py-1 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                          Bayar
                        </button>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={8} className="px-6 py-4 text-center text-gray-500">
                    Tidak ada data order
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Payment Modal */}
      <Modal isOpen={modalIsOpen} onRequestClose={closeModal} style={customStyles}>
        <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
          <h3 className="text-lg font-semibold text-white">Pembayaran Order</h3>
        </div>
        <div className="p-6">
          {selectedOrder && (
            <div>
              <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700">Pelanggan</label>
                <p className="mt-1 text-sm text-gray-900">{selectedOrder.pelanggan}</p>
              </div>

              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">Total</label>
                  <input type="number" className="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50" value={selectedOrder.total} readOnly />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700">Bayar</label>
                  <input
                    type="number"
                    className="mt-1 block w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    value={paymentAmount}
                    onChange={handlePaymentChange}
                    placeholder="Masukkan jumlah pembayaran"
                    min={selectedOrder.total}
                    required
                  />
                  {paymentAmount < selectedOrder.total && <p className="mt-1 text-sm text-red-600">Pembayaran kurang dari total order</p>}
                </div>

                {paymentAmount > 0 && (
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Kembalian</label>
                    <input type="number" className="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50" value={kembalian} readOnly />
                  </div>
                )}

                <div className="flex justify-end space-x-3 mt-6">
                  <button type="button" onClick={closeModal} className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Tutup
                  </button>
                  <button
                    type="submit"
                    disabled={paymentAmount < selectedOrder?.total || isProcessing}
                    className="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {isProcessing ? (
                      <div className="flex items-center space-x-2">
                        <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span>Memproses...</span>
                      </div>
                    ) : (
                      "Proses Pembayaran"
                    )}
                  </button>
                </div>
              </form>
            </div>
          )}
        </div>
      </Modal>
    </div>
  );
};

export default Order;

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
      width: "40%",
      backgroundColor: "#fff",
      borderRadius: "8px",
      padding: "20px",
    },
    overlay: {
      backgroundColor: "rgba(0, 0, 0, 0.75) !important",
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
    <div className="container mt-4">
      <h2>Data Order</h2>

      {/* Filter Form */}
      <div className="row mb-3">
        <div className="col-md-3">
          <input type="date" className="form-control" value={startDate} onChange={(e) => setStartDate(e.target.value)} />
        </div>
        <div className="col-md-3">
          <input type="date" className="form-control" value={endDate} onChange={(e) => setEndDate(e.target.value)} />
        </div>
        <div className="col-md-2">
          <button className="btn btn-primary" onClick={handleFilter}>
            Filter
          </button>
        </div>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}
      <div className="table-responsive">
        <table className="table table-striped table-hover">
          <thead className="table-dark">
            <tr>
              <th>No</th>
              <th>Pelanggan</th>
              <th>Tanggal Order</th>
              <th>Total</th>
              <th>Bayar</th>
              <th>Kembali</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan={8} className="text-center">
                  Loading...
                </td>
              </tr>
            ) : displayOrders && displayOrders.length > 0 ? (
              displayOrders.map((order, idx) => (
                <tr key={order.idorder || idx}>
                  <td>{idx + 1}</td>
                  <td>{order.pelanggan}</td>
                  <td>{order.tglorder}</td>
                  <td>Rp. {Number(order.total).toLocaleString("id-ID")}</td>
                  <td>Rp. {Number(order.bayar).toLocaleString("id-ID")}</td>
                  <td>Rp. {Number(order.kembali).toLocaleString("id-ID")}</td>
                  <td>{order.status === 1 ? <span className="badge bg-success">Lunas</span> : <span className="badge bg-danger">Belum Bayar</span>}</td>
                  <td>
                    {order.status === 0 && ( // Only show button if status is not paid
                      <button className="btn btn-sm btn-primary" onClick={() => openModal(order)}>
                        Bayar
                      </button>
                    )}
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={8} className="text-center">
                  Tidak ada data order
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Modal */}
      <Modal isOpen={modalIsOpen} onRequestClose={closeModal} style={customStyles} contentLabel="Payment Modal">
        <div className="modal-header">
          <h2 className="modal-title">Pembayaran Order</h2>
          <button type="button" className="btn-close" onClick={closeModal}></button>
        </div>
        <div className="modal-body">
          {selectedOrder && (
            <div>
              <p>
                <strong>Pelanggan:</strong> {selectedOrder.pelanggan}
              </p>
              <form className="mt-4" onSubmit={handleSubmit}>
                <div className="row mb-3">
                  <div className="col">
                    <label htmlFor="total" className="form-label">
                      Total
                    </label>
                    <input type="number" className="form-control" id="total" value={selectedOrder.total} readOnly />
                  </div>
                </div>
                <div className="row mb-3">
                  <div className="col">
                    <label htmlFor="bayar" className="form-label">
                      Bayar
                    </label>
                    <input type="number" className="form-control" id="bayar" name="bayar" value={paymentAmount} onChange={handlePaymentChange} placeholder="Masukkan jumlah pembayaran" min={selectedOrder.total} required />
                    {paymentAmount < selectedOrder.total && <div className="text-danger mt-1">Pembayaran kurang dari total order</div>}
                  </div>
                </div>
                {paymentAmount > 0 && (
                  <div className="row mb-3">
                    <div className="col">
                      <label className="form-label">Kembalian</label>
                      <input type="number" className="form-control" value={kembalian} readOnly />
                    </div>
                  </div>
                )}
                <div className="modal-footer px-0 pb-0">
                  <button type="button" className="btn btn-danger me-2" onClick={closeModal}>
                    Tutup
                  </button>
                  <button type="submit" className="btn btn-primary" disabled={paymentAmount < selectedOrder?.total || isProcessing}>
                    {isProcessing ? "Memproses..." : "Proses Pembayaran"}
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

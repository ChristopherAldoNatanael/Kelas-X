import React, { useState, useEffect } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

export default function CheckoutComponent() {
  const navigate = useNavigate();
  const [cart, setCart] = useState([]);
  const [loading, setLoading] = useState(true);
  const [paymentMethod, setPaymentMethod] = useState("tunai");
  const [cash, setCash] = useState("");
  const [qrisImage] = useState("https://example.com/qris-code.png"); // Ganti dengan URL QRIS Anda
  const [bankAccounts] = useState([
    { bank: "BCA", number: "1234567890", name: "RASA NUSANTARA" },
    { bank: "BNI", number: "0987654321", name: "RASA NUSANTARA" },
    { bank: "Mandiri", number: "2468135790", name: "RASA NUSANTARA" },
  ]);
  const [processing, setProcessing] = useState(false);
  const [showSuccessModal, setShowSuccessModal] = useState(false);
  const [orderNumber, setOrderNumber] = useState(null);

  // Modify the useEffect hook at the top of your component
  useEffect(() => {
    // Check authentication
    if (!sessionStorage.getItem("customer_token")) {
      navigate("/login-customer");
      return;
    }

    // Fetch cart data
    const checkCartAndRedirect = async () => {
      try {
        const response = await axios.get(`${link}/cart/${sessionStorage.getItem("customer_idpelanggan")}`);
        if (response.data.status === "success") {
          // If cart is empty, redirect to home
          if (!response.data.data || response.data.data.length === 0) {
            navigate("/");
            return;
          }
          setCart(response.data.data);
        }
        setLoading(false);
      } catch (error) {
        console.error("Error fetching cart:", error);
        navigate("/");
        setLoading(false);
      }
    };

    checkCartAndRedirect();
  }, [navigate]);

  const fetchCart = async () => {
    try {
      const response = await axios.get(`${link}/cart/${sessionStorage.getItem("customer_idpelanggan")}`);
      if (response.data.status === "success") {
        setCart(response.data.data);
      }
      setLoading(false);
    } catch (error) {
      console.error("Error fetching cart:", error);
      setLoading(false);
    }
  };

  const calculateTotal = () => {
    return cart.reduce((total, item) => total + (item.menu?.harga || 0) * item.qty, 0);
  };

  const calculateChange = () => {
    const total = calculateTotal();
    const cashAmount = parseFloat(cash);
    return cashAmount >= total ? cashAmount - total : 0;
  };

  // Modify the handleSubmitOrder function
  const handleSubmitOrder = async () => {
    if (processing) return;

    try {
      setProcessing(true);
      const total = calculateTotal();

      // Recheck cart before proceeding
      const cartCheck = await axios.get(`${link}/cart/${sessionStorage.getItem("customer_idpelanggan")}`);
      if (!cartCheck.data.data || cartCheck.data.data.length === 0) {
        navigate("/");
        return;
      }

      if (paymentMethod === "tunai" && parseFloat(cash) < total) {
        alert("Jumlah pembayaran kurang dari total belanja");
        setProcessing(false);
        return;
      }

      const orderData = {
        idpelanggan: sessionStorage.getItem("customer_idpelanggan"),
        tglorder: new Date().toISOString().split("T")[0],
        total: total,
        bayar: parseFloat(cash) || total,
        kembali: calculateChange(),
        status: paymentMethod === "tunai" ? 1 : 0,
        payment_method: paymentMethod,
        items: cart.map((item) => ({
          idmenu: item.idmenu,
          jumlah: item.qty,
          hargajual: item.menu.harga,
        })),
      };

      console.log("Sending order data:", orderData); // Debug log

      const response = await axios.post(`${link}/order`, orderData);
      console.log("Response:", response.data); // Debug log

      if (response.data.status === "success") {
        setOrderNumber(response.data.data.idorder);
        setShowSuccessModal(true);

        // Clear local cart state
        setCart([]);
      }
    } catch (error) {
      console.error("Error creating order:", error);
      alert(error.response?.data?.message || "Gagal membuat pesanan");
    } finally {
      setProcessing(false);
    }
  };

  const handleCloseModal = () => {
    setShowSuccessModal(false);
    navigate("/order-history"); // Navigate to order history on modal close
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
      </div>
    );
  }

  // Add styles at the bottom of your file
  const styles = {
    modalOverlay: {
      position: "fixed",
      top: 0,
      left: 0,
      right: 0,
      bottom: 0,
      backgroundColor: "rgba(0, 0, 0, 0.5)",
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      zIndex: 50,
    },
    modalContent: {
      backgroundColor: "white",
      padding: "2rem",
      borderRadius: "1rem",
      maxWidth: "500px",
      width: "90%",
      position: "relative",
    },
  };

  return (
    <div className="min-h-screen bg-gray-50 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

          {/* Order Summary */}
          <div className="bg-white shadow rounded-lg p-6 mb-6">
            <h2 className="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
            <div className="space-y-4">
              {cart.map((item) => (
                <div key={item.idcart} className="flex justify-between">
                  <div>
                    <p className="font-medium">{item.menu?.menu}</p>
                    <p className="text-sm text-gray-500">Qty: {item.qty}</p>
                  </div>
                  <p className="font-medium">Rp {Number((item.menu?.harga || 0) * item.qty).toLocaleString("id-ID")}</p>
                </div>
              ))}
              <div className="border-t pt-4">
                <div className="flex justify-between font-bold">
                  <p>Total</p>
                  <p>Rp {calculateTotal().toLocaleString("id-ID")}</p>
                </div>
              </div>
            </div>
          </div>

          {/* Payment Method */}
          <div className="bg-white shadow rounded-lg p-6 mb-6">
            <h2 className="text-xl font-semibold mb-4">Metode Pembayaran</h2>
            <div className="space-y-4">
              {/* Tunai */}
              <div className="flex items-center space-x-4">
                <input type="radio" id="tunai" value="tunai" checked={paymentMethod === "tunai"} onChange={(e) => setPaymentMethod(e.target.value)} className="h-4 w-4 text-blue-600" />
                <label htmlFor="tunai" className="font-medium">
                  Tunai
                </label>
              </div>

              {/* QRIS */}
              <div className="flex items-center space-x-4">
                <input type="radio" id="qris" value="qris" checked={paymentMethod === "qris"} onChange={(e) => setPaymentMethod(e.target.value)} className="h-4 w-4 text-blue-600" />
                <label htmlFor="qris" className="font-medium">
                  QRIS
                </label>
              </div>

              {/* Transfer Bank */}
              <div className="flex items-center space-x-4">
                <input type="radio" id="transfer" value="transfer" checked={paymentMethod === "transfer"} onChange={(e) => setPaymentMethod(e.target.value)} className="h-4 w-4 text-blue-600" />
                <label htmlFor="transfer" className="font-medium">
                  Transfer Bank
                </label>
              </div>
            </div>

            {/* Payment Details */}
            {paymentMethod === "tunai" && (
              <div className="mt-4">
                <label className="block text-sm font-medium text-gray-700">Jumlah Tunai</label>
                <input type="number" value={cash} onChange={(e) => setCash(e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan jumlah uang" />
                {cash && <p className="mt-2 text-sm text-gray-600">Kembalian: Rp {calculateChange().toLocaleString("id-ID")}</p>}
              </div>
            )}

            {paymentMethod === "qris" && (
              <div className="mt-4">
                <img src={qrisImage} alt="QRIS Code" className="max-w-xs mx-auto" />
                <p className="text-center mt-2 text-sm text-gray-600">Scan QRIS code untuk melakukan pembayaran</p>
              </div>
            )}

            {paymentMethod === "transfer" && (
              <div className="mt-4 space-y-4">
                {bankAccounts.map((account, index) => (
                  <div key={index} className="p-4 border rounded-lg">
                    <p className="font-semibold">{account.bank}</p>
                    <p className="text-lg font-mono">{account.number}</p>
                    <p className="text-sm text-gray-600">a.n. {account.name}</p>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Submit Order Button */}
          <button
            onClick={handleSubmitOrder}
            disabled={processing}
            className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {processing ? (
              <div className="flex items-center justify-center space-x-2">
                <div className="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
                <span>Memproses...</span>
              </div>
            ) : (
              "Konfirmasi Pembayaran"
            )}
          </button>

          {/* Success Modal */}
          {showSuccessModal && (
            <div style={styles.modalOverlay}>
              <div style={styles.modalContent} className="transform transition-all animate-modal-scale">
                <div className="text-center">
                  {/* Success Icon */}
                  <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <svg className="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>

                  {/* Success Message */}
                  <h3 className="text-2xl font-bold text-gray-900 mb-2">Transaksi Berhasil!</h3>
                  <p className="text-gray-500 mb-6">
                    Terima kasih atas pesanan Anda. <br />
                    Nomor Order: #{orderNumber}
                  </p>

                  {/* Payment Info */}
                  <div className="bg-gray-50 rounded-lg p-4 mb-6">
                    <p className="text-sm text-gray-600 mb-2">
                      Metode Pembayaran: <span className="font-semibold capitalize">{paymentMethod}</span>
                    </p>
                    <p className="text-sm text-gray-600">
                      Total Pembayaran: <span className="font-semibold">Rp {calculateTotal().toLocaleString("id-ID")}</span>
                    </p>
                  </div>

                  {/* Action Buttons */}
                  <div className="flex flex-col space-y-3">
                    <button onClick={() => navigate("/order-history")} className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors duration-200">
                      Lihat Riwayat Pesanan
                    </button>
                    <button onClick={() => navigate("/")} className="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                      Kembali ke Beranda
                    </button>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

<style jsx>{`
  @keyframes modalScale {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .animate-modal-scale {
    animation: modalScale 0.3s ease-out forwards;
  }
`}</style>;

import React, { useState, useEffect } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { useNavigate } from "react-router-dom";

export default function HistoryComponent() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchOrders = async () => {
      const idpelanggan = sessionStorage.getItem("customer_idpelanggan");
      if (!idpelanggan) {
        navigate("/login-customer");
        return;
      }

      try {
        console.log("Fetching orders for customer:", idpelanggan);
        const response = await axios.get(`${link}/order/pelanggan/${idpelanggan}`);
        console.log("API Response:", response.data);

        const orderData = Array.isArray(response.data) ? response.data : response.data?.data || [];
        setOrders(orderData);
      } catch (err) {
        console.error("Error fetching orders:", err);
        setError(err.response?.data?.message || "Gagal memuat data pesanan");
      } finally {
        setLoading(false);
      }
    };

    fetchOrders();
  }, [navigate]);

  const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    }).format(value);
  };

  const getStatusBadge = (status) => {
    if (status === 1) {
      return (
        <div className="relative overflow-hidden px-4 py-2 rounded-full bg-gradient-to-r from-emerald-500 to-green-600 shadow-lg transform hover:scale-105 transition-all duration-300">
          <span className="text-white font-semibold text-sm flex items-center gap-2">
            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
            </svg>
            Lunas
          </span>
          <div className="absolute inset-0 bg-white opacity-20 transform skew-x-12 translate-x-full group-hover:translate-x-0 transition-transform duration-1000"></div>
        </div>
      );
    }
    return (
      <div className="relative overflow-hidden px-4 py-2 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 shadow-lg transform hover:scale-105 transition-all duration-300">
        <span className="text-white font-semibold text-sm flex items-center gap-2">
          <svg className="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" />
          </svg>
          Pending
        </span>
        <div className="absolute inset-0 bg-white opacity-20 transform skew-x-12 translate-x-full group-hover:translate-x-0 transition-transform duration-1000"></div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 flex items-center justify-center">
        <div className="text-center">
          <div className="relative">
            <div className="w-16 h-16 mx-auto mb-4">
              <div className="absolute inset-0 rounded-full border-4 border-red-200"></div>
              <div className="absolute inset-0 rounded-full border-4 border-red-600 border-t-transparent animate-spin"></div>
            </div>
            <p className="text-red-800 font-medium">Memuat riwayat pesanan...</p>
          </div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 flex items-center justify-center p-4">
        <div className="max-w-md w-full">
          <div className="bg-white rounded-2xl shadow-2xl border border-red-100 p-8 text-center">
            <div className="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
              <svg className="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">Terjadi Kesalahan</h3>
            <p className="text-red-600 mb-4">{error}</p>
            <button onClick={() => window.location.reload()} className="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
              Coba Lagi
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Add navigation function
  const goToHome = () => {
    navigate("/");
  };

  // Update the return JSX with blue color scheme
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-sky-50">
      {/* Header with Pattern - Updated colors */}
      <div className="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700">
        <div className="absolute inset-0 bg-black opacity-10"></div>
        <div className="absolute inset-0 bg-opacity-20">
          <svg className="absolute top-0 left-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <pattern id="batik" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)" />
              <circle cx="5" cy="5" r="0.5" fill="rgba(255,255,255,0.05)" />
              <circle cx="15" cy="15" r="0.5" fill="rgba(255,255,255,0.05)" />
            </pattern>
            <rect width="100%" height="100%" fill="url(#batik)" />
          </svg>
        </div>

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Add Home Button */}
          <div className="absolute top-4 left-4">
            <button onClick={goToHome} className="flex items-center space-x-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full transition-all duration-300 backdrop-blur-sm">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
              <span>Kembali ke Beranda</span>
            </button>
          </div>

          <div className="text-center">
            <div className="flex items-center justify-center mb-6">
              <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg mr-4">
                <span className="text-2xl">🍽️</span>
              </div>
              <h1 className="text-4xl md:text-5xl font-bold text-white">RasaNusantara</h1>
            </div>
            <h2 className="text-2xl md:text-3xl font-semibold text-blue-100 mb-4">Riwayat Pesanan Anda</h2>
            <p className="text-blue-200 text-lg max-w-2xl mx-auto">Nikmati perjalanan kuliner Nusantara melalui setiap pesanan yang telah Anda buat</p>
          </div>
        </div>

        {/* Wave separator - Updated color */}
        <div className="absolute bottom-0 w-full">
          <svg viewBox="0 0 1200 120" className="w-full h-12 fill-blue-50">
            <path d="M0,60 C300,100 900,20 1200,60 L1200,120 L0,120 Z"></path>
          </svg>
        </div>
      </div>

      {/* Update order cards with blue theme */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {orders.length === 0 ? (
          <div className="text-center py-20">
            <div className="max-w-md mx-auto">
              <div className="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-full flex items-center justify-center">{/* ...existing empty state icon... */}</div>
              <h3 className="text-2xl font-semibold text-gray-800 mb-4">Belum Ada Pesanan</h3>
              <p className="text-gray-600 mb-8">Mulai jelajahi cita rasa Nusantara dan buat pesanan pertama Anda!</p>
              <button
                onClick={goToHome}
                className="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-full hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg"
              >
                Mulai Pesan Sekarang
              </button>
            </div>
          </div>
        ) : (
          <div className="space-y-6">
            {orders.map((order, index) => (
              <div key={order.idorder} className="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-blue-100 transform hover:-translate-y-1">
                {/* Card Header - Updated colors */}
                <div className="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                      <div className="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span className="text-white font-bold text-lg">#{order.idorder}</span>
                      </div>
                      <div>
                        <h3 className="text-white font-semibold text-lg">Order #{order.idorder}</h3>
                        <p className="text-orange-100 text-sm">
                          {new Date(order.tglorder).toLocaleDateString("id-ID", {
                            weekday: "long",
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                          })}
                        </p>
                      </div>
                    </div>
                    <div className="text-right">{getStatusBadge(order.status)}</div>
                  </div>
                </div>

                {/* Card Body - Updated colors */}
                <div className="p-6">
                  <div className="grid md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                      <div className="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
                        <h4 className="font-semibold text-gray-800 mb-3 flex items-center">
                          <svg className="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth={2}
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                          </svg>
                          Detail Pembayaran
                        </h4>
                        <div className="space-y-2 text-sm">
                          <div className="flex justify-between items-center">
                            <span className="text-gray-600">Metode Pembayaran:</span>
                            <span className="font-medium text-gray-800 bg-white px-3 py-1 rounded-full">{order.payment_method || "Tunai"}</span>
                          </div>
                          {order.status === 1 && (
                            <>
                              <div className="flex justify-between items-center">
                                <span className="text-gray-600">Dibayar:</span>
                                <span className="font-medium text-green-700">{formatCurrency(order.bayar)}</span>
                              </div>
                              <div className="flex justify-between items-center">
                                <span className="text-gray-600">Kembalian:</span>
                                <span className="font-medium text-blue-700">{formatCurrency(order.kembali)}</span>
                              </div>
                            </>
                          )}
                        </div>
                      </div>
                    </div>

                    <div className="space-y-4">
                      <div className="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-6 text-white text-center">
                        <p className="text-blue-200 text-sm mb-2">Total Pesanan</p>
                        <p className="text-3xl font-bold">{formatCurrency(order.total)}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-blue-400"></div>
              </div>
            ))}
          </div>
        )}
      </div>

      <style jsx>{`
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      `}</style>
    </div>
  );
}

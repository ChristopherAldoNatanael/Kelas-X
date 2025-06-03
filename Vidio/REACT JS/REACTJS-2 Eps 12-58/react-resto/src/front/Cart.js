import React, { useState, useEffect } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { Link, useNavigate } from "react-router-dom";
import { ShoppingCart, User, Settings, Clock, LogOut, Trash2, Plus, Minus, Package, AlertCircle } from "lucide-react";

export default function Cart() {
  const [cartItems, setCartItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [checkoutLoading, setCheckoutLoading] = useState(false);
  const [error, setError] = useState(null);
  const [isScrolled, setIsScrolled] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);
  const [user, setUser] = useState(null);
  const [removingItems, setRemovingItems] = useState(new Set());
  const navigate = useNavigate();

  // Add scroll effect
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (showDropdown && !event.target.closest(".dropdown-container")) {
        setShowDropdown(false);
      }
    };
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [showDropdown]);

  // Check user login
  useEffect(() => {
    const checkUserLogin = () => {
      const customerEmail = sessionStorage.getItem("customer_email");
      const customerName = sessionStorage.getItem("customer_name");
      const customerId = sessionStorage.getItem("customer_idpelanggan");

      if (customerEmail && customerName && customerId) {
        setUser({
          email: customerEmail,
          pelanggan: customerName,
          id: customerId,
        });
      }
    };
    checkUserLogin();
  }, []);

  // Fetch cart data
  useEffect(() => {
    const fetchCart = async () => {
      try {
        setLoading(true);
        const idpelanggan = sessionStorage.getItem("customer_idpelanggan");

        if (!idpelanggan) {
          throw new Error("Silakan login terlebih dahulu");
        }

        const response = await axios.get(`${link}/cart/details/${idpelanggan}`);

        if (response.data.status === "success") {
          setCartItems(response.data.data || []);
        } else {
          throw new Error(response.data.message || "Gagal mengambil data keranjang");
        }
      } catch (err) {
        setError(err.message);
        console.error("Error fetching cart:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchCart();
  }, []);

  const calculateTotal = (items) => {
    return items.reduce((total, item) => total + item.harga * item.qty, 0);
  };

  const handleQuantityChange = async (idcart, newQty) => {
    if (newQty <= 0) return;

    try {
      const response = await axios.put(`${link}/cart/item/${idcart}`, {
        qty: newQty,
      });

      if (response.data.status === "success") {
        setCartItems(cartItems.map((item) => (item.idcart === idcart ? { ...item, qty: newQty } : item)));
      } else {
        throw new Error(response.data.message);
      }
    } catch (error) {
      console.error("Error updating quantity:", error);
      setError("Gagal mengubah jumlah item");
    }
  };

  const handleRemoveItem = async (idcart) => {
    try {
      setRemovingItems((prev) => new Set([...prev, idcart]));

      const response = await axios.delete(`${link}/cart/item/${idcart}`);

      if (response.data.status === "success") {
        setCartItems((prevItems) => prevItems.filter((item) => item.idcart !== idcart));
        setError(null);
      } else {
        throw new Error(response.data.message);
      }
    } catch (error) {
      console.error("Error removing item:", error);
      setError("Gagal menghapus item dari keranjang");
    } finally {
      setRemovingItems((prev) => {
        const newSet = new Set(prev);
        newSet.delete(idcart);
        return newSet;
      });
    }
  };

  const handleCheckout = async () => {
    try {
      setCheckoutLoading(true);
      const idpelanggan = sessionStorage.getItem("customer_idpelanggan");
      const total = calculateTotal(cartItems);

      const response = await axios.post(`${link}/order`, {
        idpelanggan,
        total,
        items: cartItems,
      });

      if (response.data.status === "success") {
        setCartItems([]);
        setError(null);
        alert("Pesanan berhasil dibuat!");
      } else {
        throw new Error(response.data.message || "Gagal memproses checkout");
      }
    } catch (err) {
      setError("Gagal memproses checkout");
      console.error("Error during checkout:", err);
    } finally {
      setCheckoutLoading(false);
    }
  };

  const handleDropdownAction = (action) => {
    setShowDropdown(false);

    if (action === "settings") {
      navigate("/profile/setting");
    } else if (action === "history") {
      navigate("/history");
    } else if (action === "logout") {
      sessionStorage.clear();
      navigate("/login-customer");
    }
  };

  if (loading)
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex items-center justify-center">
        <div className="relative">
          <div className="animate-spin rounded-full h-20 w-20 border-4 border-blue-200"></div>
          <div className="animate-spin rounded-full h-20 w-20 border-t-4 border-blue-600 absolute top-0"></div>
          <div className="absolute inset-0 flex items-center justify-center">
            <ShoppingCart className="h-8 w-8 text-blue-600 animate-pulse" />
          </div>
        </div>
      </div>
    );

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      {/* Navigation */}
      <nav className={`fixed w-full z-50 transition-all duration-500 ${isScrolled ? "bg-white/95 backdrop-blur-lg shadow-xl border-b border-blue-100" : "bg-white/80 backdrop-blur-sm"}`}>
        <div className="container mx-auto px-6">
          <div className="flex justify-between items-center h-20">
            <Link to="/" className="flex items-center space-x-3 group">
              <div className="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                <span className="text-white font-bold text-lg">R</span>
              </div>
              <span className="text-2xl font-bold bg-gradient-to-r from-blue-800 to-indigo-800 bg-clip-text text-transparent">RasaNusantara</span>
            </Link>

            {user ? (
              <div className="relative dropdown-container">
                <button
                  onClick={() => setShowDropdown(!showDropdown)}
                  className="flex items-center space-x-3 px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <div className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <User className="h-4 w-4" />
                  </div>
                  <span className="font-medium">{user.pelanggan}</span>
                  <div className={`transform transition-transform duration-200 ${showDropdown ? "rotate-180" : ""}`}>
                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                    </svg>
                  </div>
                </button>

                {showDropdown && (
                  <div className="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                    <div className="py-1">
                      <button onClick={() => handleDropdownAction("settings")} className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div className="flex items-center">
                          <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth={2}
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                            />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          Pengaturan
                        </div>
                      </button>
                      <button onClick={() => handleDropdownAction("history")} className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div className="flex items-center">
                          <Clock className="h-4 w-4 mr-2" />
                          Order History
                        </div>
                      </button>
                      <div className="border-t border-gray-100 mt-1 pt-1">
                        <button onClick={() => handleDropdownAction("logout")} className="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                          <LogOut className="h-4 w-4 mr-3" />
                          Logout
                        </button>
                      </div>
                    </div>
                  </div>
                )}
              </div>
            ) : (
              <Link
                to="/login-customer"
                className="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Login
              </Link>
            )}
          </div>
        </div>
      </nav>

      {/* Main Content */}
      <div className="container mx-auto px-6 pt-32 pb-20">
        <div className="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
          {/* Header */}
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8">
            <div className="flex items-center space-x-4">
              <div className="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                <ShoppingCart className="h-6 w-6 text-white" />
              </div>
              <div>
                <h1 className="text-3xl font-bold text-white">Keranjang Belanja</h1>
                <p className="text-blue-100 mt-1">{cartItems.length} item dalam keranjang</p>
              </div>
            </div>
          </div>

          {error && (
            <div className="mx-8 mt-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl animate-in slide-in-from-left duration-300">
              <div className="flex">
                <div className="flex-shrink-0">
                  <AlertCircle className="h-5 w-5 text-red-400" />
                </div>
                <div className="ml-3">
                  <p className="text-red-700">{error}</p>
                </div>
              </div>
            </div>
          )}

          {cartItems.length === 0 ? (
            <div className="px-8 py-16 text-center">
              <div className="w-24 h-24 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <Package className="h-12 w-12 text-blue-400" />
              </div>
              <h3 className="text-2xl font-bold text-gray-900 mb-2">Keranjang Belanja Kosong</h3>
              <p className="text-gray-500 mb-8">Mulai belanja untuk mengisi keranjang dengan makanan lezat.</p>
              <Link
                to="/"
                className="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Lihat Menu
              </Link>
            </div>
          ) : (
            <div className="p-8">
              <div className="space-y-4">
                {cartItems.map((item, index) => (
                  <div
                    key={item.idcart}
                    data-item-id={item.idcart}
                    className={`bg-gradient-to-r from-white to-blue-50/50 rounded-2xl p-6 border border-blue-100/50 transition-all duration-300 transform ${removingItems.has(item.idcart) ? "" : "hover:shadow-lg hover:scale-[1.02]"}`}
                    style={{
                      animationDelay: `${index * 100}ms`,
                      transition: "all 0.3s ease-out",
                    }}
                  >
                    <div className="flex items-center space-x-6">
                      {/* Product Image */}
                      <div className="flex-shrink-0">
                        <div className="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100">
                          {item.gambar ? (
                            <img
                              src={`${link}/upload/${item.gambar}`}
                              alt={item.menu}
                              className="w-full h-full object-cover"
                              onError={(e) => {
                                e.target.style.display = "none";
                                e.target.nextSibling.style.display = "flex";
                              }}
                            />
                          ) : null}
                          <div className="w-full h-full bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center text-white font-bold text-lg">{item.menu ? item.menu.charAt(0) : "M"}</div>
                        </div>
                      </div>

                      {/* Product Info */}
                      <div className="flex-1 min-w-0">
                        <h3 className="text-lg font-semibold text-gray-900 mb-1">{item.menu || "Menu Item"}</h3>
                        <p className="text-blue-600 font-medium">Rp {item.harga ? item.harga.toLocaleString() : "0"}</p>
                      </div>

                      {/* Quantity Controls */}
                      <div className="flex items-center space-x-3">
                        <button
                          onClick={() => handleQuantityChange(item.idcart, item.qty - 1)}
                          className="w-8 h-8 bg-blue-100 hover:bg-blue-200 rounded-full flex items-center justify-center transition-colors duration-200"
                          disabled={item.qty <= 1 || removingItems.has(item.idcart)}
                        >
                          <Minus className="h-4 w-4 text-blue-600" />
                        </button>

                        <span className="w-8 text-center font-semibold text-gray-900">{item.qty}</span>

                        <button
                          onClick={() => handleQuantityChange(item.idcart, item.qty + 1)}
                          className="w-8 h-8 bg-blue-100 hover:bg-blue-200 rounded-full flex items-center justify-center transition-colors duration-200"
                          disabled={removingItems.has(item.idcart)}
                        >
                          <Plus className="h-4 w-4 text-blue-600" />
                        </button>
                      </div>

                      {/* Total Price */}
                      <div className="text-right">
                        <p className="text-lg font-bold text-gray-900">Rp {((item.harga || 0) * (item.qty || 0)).toLocaleString()}</p>
                      </div>

                      {/* Remove Button */}
                      <button
                        onClick={() => handleRemoveItem(item.idcart)}
                        className="w-10 h-10 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center transition-colors duration-200"
                        disabled={removingItems.has(item.idcart)}
                      >
                        <Trash2 className="h-4 w-4 text-red-600" />
                      </button>
                    </div>
                  </div>
                ))}
              </div>

              {/* Order Summary */}
              <div className="mt-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white">
                <div className="flex justify-between items-center mb-4">
                  <span className="text-xl font-semibold">Total Pembayaran:</span>
                  <span className="text-2xl font-bold">Rp {calculateTotal(cartItems).toLocaleString()}</span>
                </div>
                <button
                  onClick={() => navigate("/checkout")}
                  disabled={checkoutLoading || cartItems.length === 0}
                  className="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center space-x-2"
                >
                  {checkoutLoading ? (
                    <div className="flex items-center space-x-2">
                      <div className="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
                      <span>Memproses...</span>
                    </div>
                  ) : (
                    <>
                      <span>Lanjutkan ke Pembayaran</span>
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                      </svg>
                    </>
                  )}
                </button>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Footer */}
      <footer className="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 text-white">
        <div className="container mx-auto px-6 py-16">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div className="space-y-4">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                  <span className="text-white font-bold text-lg">R</span>
                </div>
                <h3 className="text-2xl font-bold">RasaNusantara</h3>
              </div>
              <p className="text-blue-200">Nikmati kelezatan masakan nusantara dengan cita rasa autentik yang tak terlupakan.</p>
            </div>
            <div className="space-y-4">
              <h4 className="text-xl font-semibold">Kontak</h4>
              <div className="space-y-2 text-blue-200">
                <p>📧 info@rasanusantara.com</p>
                <p>📞 (021) 1234-5678</p>
                <p>📍 Jakarta, Indonesia</p>
              </div>
            </div>
            <div className="space-y-4">
              <h4 className="text-xl font-semibold">Jam Operasional</h4>
              <div className="space-y-2 text-blue-200">
                <p>Senin - Jumat: 10:00 - 22:00</p>
                <p>Sabtu - Minggu: 09:00 - 23:00</p>
                <p>Hari Libur: 11:00 - 21:00</p>
              </div>
            </div>
          </div>
          <div className="border-t border-blue-800 mt-12 pt-8 text-center">
            <p className="text-blue-200">&copy; 2024 RasaNusantara. All rights reserved. Made with ❤️ in Indonesia</p>
          </div>
        </div>
      </footer>

      <style jsx>{`
        @keyframes animate-in {
          from {
            opacity: 0;
            transform: translateY(10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .animate-in {
          animation: animate-in 0.3s ease-out forwards;
        }

        .slide-in-from-top-2 {
          animation: slideInFromTop 0.2s ease-out;
        }

        .slide-in-from-left {
          animation: slideInFromLeft 0.3s ease-out;
        }

        @keyframes slideInFromTop {
          from {
            opacity: 0;
            transform: translateY(-8px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes slideInFromLeft {
          from {
            opacity: 0;
            transform: translateX(-20px);
          }
          to {
            opacity: 1;
            transform: translateX(0);
          }
        }
      `}</style>
    </div>
  );
}

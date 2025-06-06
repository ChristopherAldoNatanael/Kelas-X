import React, { useEffect, useState } from "react";
import axios from "axios";
import { link } from "../axios/link";
import { Link, useNavigate } from "react-router-dom";

export default function MenuComponent() {
  const [menu, setMenu] = useState([]);
  const [kategori, setKategori] = useState([]);
  const [selectedKategori, setSelectedKategori] = useState("all");
  const [loading, setLoading] = useState(true);
  const [cart, setCart] = useState([]);
  const [cartCount, setCartCount] = useState(0);
  const [user, setUser] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [isScrolled, setIsScrolled] = useState(false);
  const [showCart, setShowCart] = useState(false);
  const [showCartPulse, setShowCartPulse] = useState(false);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const navigate = useNavigate();

  // Handle scroll effect for navbar
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Fetch cart data
  const fetchCartData = async () => {
    if (!user) return;

    try {
      const response = await axios.get(`${link}/cart/${sessionStorage.getItem("customer_idpelanggan")}`);
      if (response.data.status === "success" && Array.isArray(response.data.data)) {
        setCart(response.data.data);
        const totalCount = response.data.data.reduce((total, item) => total + parseInt(item.qty), 0);
        setCartCount(totalCount);
      }
    } catch (error) {
      console.error("Error fetching cart:", error);
    }
  };

  // Ambil data kategori dan menu dari backend
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      try {
        const [kategoriRes, menuRes] = await Promise.all([axios.get(`${link}/kategori`), axios.get(`${link}/menu`)]);

        // Kategori
        if (Array.isArray(kategoriRes.data)) {
          setKategori(kategoriRes.data);
        } else if (kategoriRes.data.data && Array.isArray(kategoriRes.data.data)) {
          setKategori(kategoriRes.data.data);
        }

        // Menu
        if (Array.isArray(menuRes.data)) {
          setMenu(menuRes.data);
        } else if (menuRes.data.data && Array.isArray(menuRes.data.data)) {
          setMenu(menuRes.data.data);
        }
      } catch (err) {
        setKategori([]);
        setMenu([]);
      } finally {
        setTimeout(() => setLoading(false), 800);
      }
    };
    fetchData();
  }, []);

  // Cek user login dari sessionStorage
  useEffect(() => {
    const email = sessionStorage.getItem("customer_email");
    const pelanggan = sessionStorage.getItem("customer_name");
    if (email) {
      setUser({ email, pelanggan });
    }
  }, []);

  // Fetch cart when user changes
  useEffect(() => {
    if (user) {
      fetchCartData();
    }
  }, [user]);

  // Filter menu berdasarkan kategori dan search
  const filteredMenu = menu.filter((item) => {
    const matchCategory = selectedKategori === "all" || String(item.idkategori) === String(selectedKategori);
    const matchSearch = item.menu.toLowerCase().includes(searchTerm.toLowerCase()) || item.deskripsi.toLowerCase().includes(searchTerm.toLowerCase());
    return matchCategory && matchSearch;
  });

  // Cek login pelanggan
  const isLoggedIn = !!sessionStorage.getItem("customer_token");

  // Tambah ke keranjang dengan animasi
  const handleAddToCart = async (item) => {
    if (!user) {
      navigate("/login-customer");
      return;
    }

    try {
      const cartData = {
        idpelanggan: sessionStorage.getItem("customer_idpelanggan"),
        idmenu: item.idmenu,
        qty: 1,
      };

      const response = await axios.post(`${link}/cart`, cartData);

      if (response.data.status === "success") {
        // Update cart count
        setCartCount((prev) => prev + 1);

        // Show success notification
        setShowCart(true);
        setTimeout(() => setShowCart(false), 3000);

        // Trigger cart button pulse animation
        setShowCartPulse(true);
        setTimeout(() => setShowCartPulse(false), 1000);

        // Refresh cart data
        fetchCartData();
      } else {
        throw new Error(response.data.message);
      }
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("Gagal menambahkan ke keranjang");
    }
  };

  const handleLogout = () => {
    sessionStorage.removeItem("customer_token");
    sessionStorage.removeItem("customer_email");
    sessionStorage.removeItem("customer_name");
    sessionStorage.removeItem("customer_idpelanggan");
    setUser(null);
    setCart([]);
    setCartCount(0);
    navigate("/");
  };

  // Close dropdown if clicked outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownOpen && !event.target.closest(".relative")) {
        setDropdownOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, [dropdownOpen]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      {/* Modern Navbar with Glass Effect */}
      <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${isScrolled ? "bg-white/90 backdrop-blur-lg shadow-xl border-b border-white/20" : "bg-transparent"}`}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            {/* Logo */}
            <Link to="/" className="flex items-center space-x-3 group">
              <div className="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                </svg>
              </div>
              <span className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">RasaNusantara</span>
            </Link>

            {/* Desktop Navigation */}
            <div className="hidden md:flex items-center space-x-8">
              <Link to="/" className="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                Home
              </Link>
              <Link to="/menu" className="text-blue-600 font-semibold relative">
                Menu
                <div className="absolute -bottom-1 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></div>
              </Link>
              <Link to="/cart" className="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                </svg>
                <span>Cart</span>
                {cartCount > 0 && <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">{cartCount}</span>}
              </Link>
            </div>

            {/* User Menu */}
            <div className="flex items-center space-x-4">
              {user ? (
                <div className="relative">
                  <button
                    className="flex items-center space-x-3 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl"
                    onClick={() => setDropdownOpen(!dropdownOpen)}
                  >
                    <div className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                      <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                      </svg>
                    </div>
                    <span className="font-medium hidden sm:block">{user.pelanggan || user.email}</span>
                    <svg className={`w-4 h-4 transition-transform duration-300 ${dropdownOpen ? "rotate-180" : ""}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  {/* Enhanced Dropdown Menu */}
                  {dropdownOpen && (
                    <div className="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                      {/* User Info Section */}
                      <div className="px-4 py-3 border-b border-gray-100">
                        <div className="flex items-center space-x-3">
                          <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                            <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                            </svg>
                          </div>
                          <div>
                            <p className="font-semibold text-gray-800">{user.pelanggan || "User"}</p>
                            <p className="text-sm text-gray-500">{user.email}</p>
                          </div>
                        </div>
                      </div>

                      {/* Menu Items */}
                      <div className="py-2">
                        <button
                          onClick={() => {
                            navigate("/history"); // Perbaikan: Gunakan "/history" bukan "/order-history"
                            setDropdownOpen(false);
                          }}
                          className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                          Riwayat Pesanan
                        </button>

                        <button
                          onClick={() => {
                            navigate("/profile/setting");
                            setDropdownOpen(false);
                          }}
                          className="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                          <div className="flex items-center">
                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                              />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                          </div>
                        </button>

                        <div className="border-t border-gray-100 my-2"></div>

                        <button onClick={handleLogout} className="flex items-center space-x-3 px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700 w-full text-left">
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                          </svg>
                          <span>Logout</span>
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              ) : (
                // Keep the existing login/register buttons unchanged
                <div className="flex items-center space-x-3">
                  <Link to="/login-customer" className="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                    Login
                  </Link>
                  <Link to="/register" className="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium">
                    Register
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <div className="pt-16 pb-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-16">
            <h1 className="text-5xl md:text-6xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-6 animate-fade-in">Our Menu</h1>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto mb-8 animate-fade-in-delay">Discover our carefully crafted dishes made with the finest ingredients</p>

            {/* Search Bar */}
            <div className="max-w-md mx-auto mb-8 animate-fade-in-delay-2">
              <div className="relative">
                <input
                  type="text"
                  placeholder="Search menu..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full px-5 py-3 pl-12 pr-4 rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-all duration-300 bg-white/80 backdrop-blur-sm shadow-lg"
                />
                <svg className="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Category Filter */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div className="flex flex-wrap justify-center gap-3">
          <button
            onClick={() => setSelectedKategori("all")}
            className={`px-6 py-3 rounded-2xl font-medium transition-all duration-300 transform hover:scale-105 ${
              selectedKategori === "all" ? "bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg" : "bg-white/80 backdrop-blur-sm text-gray-700 hover:bg-white border border-gray-200 hover:shadow-lg"
            }`}
          >
            All Categories
          </button>
          {kategori.map((kat) => (
            <button
              key={kat.idkategori}
              onClick={() => setSelectedKategori(String(kat.idkategori))}
              className={`px-6 py-3 rounded-2xl font-medium transition-all duration-300 transform hover:scale-105 ${
                selectedKategori === String(kat.idkategori) ? "bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg" : "bg-white/80 backdrop-blur-sm text-gray-700 hover:bg-white border border-gray-200 hover:shadow-lg"
              }`}
            >
              {kat.kategori}
            </button>
          ))}
        </div>
      </div>

      {/* Menu Grid */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        {loading ? (
          <div className="flex justify-center items-center h-64">
            <div className="relative">
              <div className="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
              <div className="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-indigo-600 rounded-full animate-ping"></div>
            </div>
          </div>
        ) : filteredMenu.length === 0 ? (
          <div className="text-center py-16">
            <div className="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
              <svg className="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 className="text-2xl font-semibold text-gray-600 mb-2">No menu found</h3>
            <p className="text-gray-500">Try adjusting your search or category filter</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {filteredMenu.map((item, index) => (
              <div
                key={item.idmenu}
                className="group bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-white/20 hover:border-blue-200 transform hover:-translate-y-2"
                style={{ animationDelay: `${index * 100}ms` }}
              >
                <div className="relative overflow-hidden">
                  <img
                    src={item.gambar ? `http://localhost:8000/upload/${item.gambar}` : "https://via.placeholder.com/400x250?text=No+Image"}
                    alt={item.menu}
                    className="w-full h-56 object-cover transition-transform duration-700 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  <div className="absolute top-4 left-4">
                    <span className="px-3 py-1 bg-white/90 backdrop-blur-sm text-blue-600 text-sm font-medium rounded-full">{kategori.find((kat) => String(kat.idkategori) === String(item.idkategori))?.kategori || "No Category"}</span>
                  </div>
                </div>

                <div className="p-6">
                  <h3 className="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors duration-300">{item.menu}</h3>
                  <p className="text-gray-600 mb-4 line-clamp-2">{item.deskripsi}</p>

                  <div className="flex items-center justify-between">
                    <div className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Rp {Number(item.harga).toLocaleString("id-ID")}</div>
                    <button
                      onClick={() => handleAddToCart(item)}
                      className="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2 group"
                    >
                      <svg className="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                      </svg>
                      <span>Add to Cart</span>
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Enhanced Floating Cart Button */}
      {isLoggedIn && cartCount > 0 && (
        <div className="fixed bottom-8 right-8 z-50">
          <button
            onClick={() => navigate("/cart")}
            className={`relative group bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white p-3 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:scale-110 ${
              showCartPulse ? "animate-pulse scale-125" : ""
            }`}
          >
            {/* Outer Ring Animation */}
            <div className="absolute inset-0 rounded-full bg-gradient-to-r from-blue-400 to-indigo-400 animate-ping opacity-20"></div>

            {/* Inner Ring */}
            <div className="absolute inset-1 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 animate-pulse opacity-30"></div>

            {/* Cart Icon */}
            <div className="relative z-10">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
              </svg>
            </div>

            {/* Cart Count Badge */}
            <div className="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center animate-bounce shadow-lg border-2 border-white">{cartCount > 99 ? "99+" : cartCount}</div>

            {/* Floating Tooltip */}
            <div className="absolute right-full mr-4 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap shadow-xl">
              <span>
                View Cart ({cartCount} {cartCount === 1 ? "item" : "items"})
              </span>
              <div className="absolute left-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-l-gray-900"></div>
            </div>
          </button>
        </div>
      )}

      {/* Success Notification */}
      {showCart && (
        <div className="fixed bottom-8 left-8 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 animate-bounce">
          <div className="flex items-center space-x-3">
            <div className="bg-white/20 rounded-full p-1">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div>
              <span className="font-semibold block">Success!</span>
              <span className="text-sm opacity-90">Item added to cart</span>
            </div>
          </div>
        </div>
      )}

      {/* Modern Footer */}
      <footer className="bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="text-center">
            <div className="flex justify-center items-center space-x-3 mb-6">
              <div className="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                </svg>
              </div>
              <span className="text-3xl font-bold">RasaNusantara</span>
            </div>
            <p className="text-blue-100 mb-8 max-w-2xl mx-auto">Experience the finest culinary journey with our carefully crafted dishes and exceptional service.</p>
            <div className="border-t border-white/20 pt-8">
              <p className="text-blue-200">© {new Date().getFullYear()} RasaNusantara. All rights reserved.</p>
              <p className="text-blue-300 mt-2 flex items-center justify-center space-x-1">
                <span>Made with</span>
                <svg className="w-4 h-4 text-red-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                </svg>
                <span>using React JS & Laravel Lumen</span>
              </p>
            </div>
          </div>
        </div>
      </footer>

      <style jsx>{`
        @keyframes fade-in {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .animate-fade-in {
          animation: fade-in 0.8s ease-out;
        }

        .animate-fade-in-delay {
          animation: fade-in 0.8s ease-out 0.2s both;
        }

        .animate-fade-in-delay-2 {
          animation: fade-in 0.8s ease-out 0.4s both;
        }

        .line-clamp-2 {
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        @keyframes float {
          0%,
          100% {
            transform: translateY(0px);
          }
          50% {
            transform: translateY(-5px);
          }
        }

        .animate-float {
          animation: float 2s ease-in-out infinite;
        }
      `}</style>
    </div>
  );
}

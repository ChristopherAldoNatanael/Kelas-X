import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import axios from "axios";
import { link } from "../axios/link";

export default function Home() {
  const [menu, setMenu] = useState([]);
  const [loading, setLoading] = useState(true);
  const [cart, setCart] = useState([]);
  const [user, setUser] = useState(null);
  const [isScrolled, setIsScrolled] = useState(false);
  const [showNotification, setShowNotification] = useState(false);
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const navigate = useNavigate();

  // Handle scroll effect for navbar
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Auto-slide hero images
  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % 3);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  // Ambil data menu dari backend
  useEffect(() => {
    const fetchMenu = async () => {
      try {
        const res = await axios.get(`${link}/menu`);
        if (Array.isArray(res.data)) {
          setMenu(res.data);
        } else if (res.data.data && Array.isArray(res.data.data)) {
          setMenu(res.data.data);
        }
      } catch (err) {
        setMenu([]);
      } finally {
        setTimeout(() => setLoading(false), 1000); // Smooth loading transition
      }
    };
    fetchMenu();
  }, []);

  // Update this useEffect to properly read session storage
  useEffect(() => {
    const checkUserLogin = () => {
      try {
        const customerEmail = sessionStorage.getItem("customer_email");
        const customerName = sessionStorage.getItem("customer_name");
        const customerId = sessionStorage.getItem("customer_idpelanggan");

        console.log("Checking login state:", {
          email: customerEmail,
          name: customerName,
          id: customerId,
        });

        if (customerEmail && customerName && customerId) {
          setUser({
            email: customerEmail,
            pelanggan: customerName,
            id: customerId,
          });
          console.log("User logged in:", {
            email: customerEmail,
            pelanggan: customerName,
            id: customerId,
          });
        } else {
          setUser(null);
          console.log("No user logged in");
        }
      } catch (error) {
        console.error("Error checking login state:", error);
        setUser(null);
      }
    };

    // Check login state when component mounts
    checkUserLogin();

    // Add event listener for storage changes
    window.addEventListener("storage", checkUserLogin);

    // Cleanup
    return () => {
      window.removeEventListener("storage", checkUserLogin);
    };
  }, []);

  // Fetch cart data when user logs in
  useEffect(() => {
    const fetchCartData = async () => {
      if (!user) return;

      try {
        const response = await axios.get(`${link}/cart/${sessionStorage.getItem("customer_idpelanggan")}`);

        if (response.data.status === "success" && Array.isArray(response.data.data)) {
          setCart(response.data.data);
        }
      } catch (error) {
        console.error("Error fetching cart:", error);
      }
    };

    if (user) {
      fetchCartData();
    }
  }, [user]);

  // Tambah ke cart dengan animasi
  const addToCart = (item) => {
    const isLoggedIn = !!sessionStorage.getItem("customer_token");

    if (!isLoggedIn) {
      navigate("/login-customer");
      return;
    }

    setCart((prev) => {
      const exist = prev.find((i) => i.idmenu === item.idmenu);
      if (exist) {
        return prev.map((i) => (i.idmenu === item.idmenu ? { ...i, qty: i.qty + 1 } : i));
      }
      return [...prev, { ...item, qty: 1 }];
    });

    // Show notification
    setShowNotification(true);
    setTimeout(() => setShowNotification(false), 3000);
  };

  // Logout
  const handleLogout = () => {
    sessionStorage.removeItem("customer_token");
    sessionStorage.removeItem("customer_email");
    sessionStorage.removeItem("customer_name");
    sessionStorage.removeItem("customer_pelanggan");
    setUser(null);
    navigate("/");
  };

  const toggleDropdown = () => {
    setIsDropdownOpen((prev) => !prev);
  };

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (!event.target.closest(".group")) {
        setIsDropdownOpen(false);
      }
    };

    document.addEventListener("click", handleClickOutside);
    return () => {
      document.removeEventListener("click", handleClickOutside);
    };
  }, []);

  const heroImages = [
    "https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
    "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
    "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80",
  ];

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
        setCart((prevCart) => {
          const existingItem = prevCart.find((i) => i.idmenu === item.idmenu);
          if (existingItem) {
            return prevCart.map((i) => (i.idmenu === item.idmenu ? { ...i, qty: i.qty + 1 } : i));
          }
          return [...prevCart, { ...item, qty: 1 }];
        });

        // Show success notification
        setShowNotification(true);
        setTimeout(() => setShowNotification(false), 3000);
      } else {
        throw new Error(response.data.message || "Failed to add item to cart");
      }
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("Gagal menambahkan ke keranjang");
    }
  };

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
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <span className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">RasaNusantara</span>
            </Link>

            {/* Desktop Navigation */}
            <div className="hidden md:flex items-center space-x-8">
              <Link to="/" className="text-blue-600 font-semibold relative">
                Home
                <div className="absolute -bottom-1 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full"></div>
              </Link>
              <Link to="/menu" className="text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium">
                Menu
              </Link>
              <Link to="/cart" className="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors duration-200 font-medium relative">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                </svg>
                <span>Cart</span>
                {cart.length > 0 && <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">{cart.reduce((a, b) => a + b.qty, 0)}</span>}
              </Link>
            </div>

            {/* User Menu */}
            <div className="flex items-center space-x-4">
              {user ? (
                <div className="relative">
                  <button
                    onClick={toggleDropdown}
                    className="flex items-center space-x-2 px-3 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl group"
                  >
                    <div className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                      <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                      </svg>
                    </div>
                    <div className="flex flex-col text-left">
                      <span className="font-semibold text-sm">{user.pelanggan}</span>
                    </div>
                    <svg className={`w-4 h-4 transition-transform duration-200 ${isDropdownOpen ? "rotate-180" : ""}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  {/* Enhanced Dropdown Menu - Reduced Size */}
                  {isDropdownOpen && (
                    <div className="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50 transform transition-all duration-200 ease-out">
                      {/* User Info Header - More Compact */}
                      <div className="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div className="flex items-center space-x-3">
                          <div className="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg className="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                            </svg>
                          </div>
                          <div className="flex-1">
                            <h3 className="font-semibold text-gray-900 text-base">{user.pelanggan}</h3>
                            <p className="text-xs text-gray-600 truncate">{user.email}</p>
                          </div>
                        </div>
                      </div>

                      {/* Menu Items - More Compact */}
                      <div className="py-1">
                       <button 
  onClick={() => {
    navigate("/profile/setting");
    setIsDropdownOpen(false);
  }} 
  className="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors duration-150"
>
  <div className="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
    <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path
        strokeLinecap="round"
        strokeLinejoin="round"
        strokeWidth={2}
        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
      />
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
  </div>
  <span className="text-sm">Settings</span>
</button>

                        <Link to="/history" className="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                          <div className="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                          </div>
                          <span className="text-sm">Order History</span>
                        </Link>

                        <div className="my-1 border-t border-gray-100"></div>

                        <button onClick={handleLogout} className="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 transition-colors duration-150">
                          <div className="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                            <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                          </div>
                          <span className="text-sm">Logout</span>
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              ) : (
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

      {/* Hero Section with Sliding Background */}
      <section className="relative pt-16 h-screen flex items-center justify-center overflow-hidden">
        {/* Background Images */}
        <div className="absolute inset-0">
          {heroImages.map((img, index) => (
            <div key={index} className={`absolute inset-0 transition-opacity duration-1000 ${index === currentSlide ? "opacity-100" : "opacity-0"}`}>
              <img src={img} alt={`Hero ${index + 1}`} className="w-full h-full object-cover" />
              <div className="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-transparent"></div>
            </div>
          ))}
        </div>

        {/* Hero Content */}
        <div className="relative z-10 text-center text-white max-w-4xl mx-auto px-4">
          <h1 className="text-5xl md:text-7xl font-bold mb-6 animate-fade-in-up">
            Welcome to <span className="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">RasaNusantara</span>
          </h1>
          <p className="text-xl md:text-2xl mb-8 text-gray-200 animate-fade-in-up-delay max-w-2xl mx-auto">Experience exceptional dining with our carefully crafted dishes, seamless ordering, and modern restaurant atmosphere</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up-delay-2">
            <Link
              to="/menu"
              className="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105 font-semibold text-lg flex items-center justify-center space-x-2"
            >
              <span>Explore Menu</span>
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </Link>
            <button className="px-8 py-4 bg-white/20 backdrop-blur-sm text-white rounded-2xl hover:bg-white/30 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-lg border border-white/30">
              Watch Video
            </button>
          </div>
        </div>

        {/* Slide Indicators */}
        <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3">
          {heroImages.map((_, index) => (
            <button key={index} onClick={() => setCurrentSlide(index)} className={`w-3 h-3 rounded-full transition-all duration-300 ${index === currentSlide ? "bg-white w-8" : "bg-white/50"}`} />
          ))}
        </div>

        {/* Scroll Indicator */}
        <div className="absolute bottom-8 right-8 animate-bounce">
          <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 14l-7 7m0 0l-7-7m7 7V3" />
          </svg>
        </div>
      </section>

      {/* Featured Menu Section */}
      <section className="py-20 relative">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Section Header */}
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-6">Featured Menu</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">Discover our most popular dishes crafted by expert chefs using the finest ingredients</p>
          </div>

          {/* Menu Grid */}
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="relative">
                <div className="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div className="absolute inset-0 w-20 h-20 border-4 border-transparent border-r-indigo-600 rounded-full animate-ping"></div>
              </div>
            </div>
          ) : menu.length === 0 ? (
            <div className="text-center py-16">
              <div className="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <svg className="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 className="text-2xl font-semibold text-gray-600 mb-2">No menu available yet</h3>
              <p className="text-gray-500">Check back later for delicious options</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {menu.slice(0, 3).map((item, index) => (
                <div
                  key={item.idmenu}
                  className="group bg-white/90 backdrop-blur-sm rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-white/20 hover:border-blue-200 transform hover:-translate-y-2 menu-card-animate"
                  style={{ animationDelay: `${index * 100}ms` }}
                >
                  <div className="relative overflow-hidden">
                    <img
                      src={item.gambar ? `http://localhost:8000/upload/${item.gambar}` : "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=250&q=80"}
                      alt={item.menu}
                      className="w-full h-56 object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div className="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                      <div className="bg-white/90 backdrop-blur-sm p-2 rounded-full">
                        <svg className="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </div>

                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition-colors duration-300">{item.menu}</h3>
                    <p className="text-gray-600 mb-4 line-clamp-2">{item.deskripsi}</p>

                    <div className="flex items-center justify-between">
                      <div className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        {Number(item.harga).toLocaleString("id-ID", {
                          style: "currency",
                          currency: "IDR",
                        })}
                      </div>
                      <button
                        onClick={() => handleAddToCart(item)}
                        className="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2 group"
                        disabled={!user}
                      >
                        <svg className="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
                        </svg>
                        <span>{user ? "Add to Cart" : "Login to Order"}</span>
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* View All Button */}
          {menu.length > 3 && (
            <div className="text-center mt-12">
              <Link
                to="/menu"
                className="inline-flex items-center space-x-2 px-8 py-4 bg-gradient-to-r from-gray-800 to-gray-700 text-white rounded-2xl hover:from-gray-900 hover:to-gray-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold text-lg"
              >
                <span>View All Menu</span>
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </Link>
            </div>
          )}
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-20 bg-gradient-to-r from-blue-600 to-indigo-600">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div className="transform hover:scale-105 transition-transform duration-300">
              <div className="text-4xl md:text-5xl font-bold mb-2">1000+</div>
              <div className="text-blue-100">Happy Customers</div>
            </div>
            <div className="transform hover:scale-105 transition-transform duration-300">
              <div className="text-4xl md:text-5xl font-bold mb-2">50+</div>
              <div className="text-blue-100">Menu Items</div>
            </div>
            <div className="transform hover:scale-105 transition-transform duration-300">
              <div className="text-4xl md:text-5xl font-bold mb-2">5★</div>
              <div className="text-blue-100">Rating</div>
            </div>
            <div className="transform hover:scale-105 transition-transform duration-300">
              <div className="text-4xl md:text-5xl font-bold mb-2">24/7</div>
              <div className="text-blue-100">Service</div>
            </div>
          </div>
        </div>
      </section>

      {/* Notification */}
      {showNotification && (
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

      {/* Floating Cart Button */}
      {cart.length > 0 && (
        <button
          onClick={() => navigate("/cart")}
          className="fixed bottom-8 left-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 z-50 group"
        >
          <div className="relative">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 0L5 3m2 10v6a1 1 0 001 1h8a1 1 0 001-1v-6m-10 0V9a1 1 0 011-1h8a1 1 0 011 1v4" />
            </svg>
            <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">{cart.reduce((a, b) => a + b.qty, 0)}</span>
          </div>
          <span className="absolute left-full ml-3 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            View Cart ({cart.reduce((a, b) => a + b.qty, 0)} items)
          </span>
        </button>
      )}

      {/* Modern Footer */}
      <footer className="bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            {/* Brand */}
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center space-x-3 mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-xl flex items-center justify-center shadow-lg">
                  <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <span className="text-3xl font-bold">RasaNusantara</span>
              </div>
              <p className="text-blue-100 mb-8">Experience the finest culinary journey with our carefully crafted dishes and exceptional service.</p>
              <p className="text-blue-200">© {new Date().getFullYear()} RasaNusantara. All rights reserved.</p>
            </div>

            {/* Quick Links */}
            <div>
              <h3 className="text-lg font-semibold mb-4">Quick Links</h3>
              <ul className="space-y-2">
                <li>
                  <Link to="/" className="text-blue-300 hover:text-blue-400 transition-colors duration-200">
                    Home
                  </Link>
                </li>
                <li>
                  <Link to="/menu" className="text-blue-300 hover:text-blue-400 transition-colors duration-200">
                    Menu
                  </Link>
                </li>
                <li>
                  <Link to="/cart" className="text-blue-300 hover:text-blue-400 transition-colors duration-200">
                    Cart
                  </Link>
                </li>
                <li>
                  <Link to="/contact" className="text-blue-300 hover:text-blue-400 transition-colors duration-200">
                    Contact Us
                  </Link>
                </li>
              </ul>
            </div>

            {/* Contact Info */}
            <div>
              <h3 className="text-lg font-semibold mb-4">Contact Us</h3>
              <ul className="space-y-2">
                <li className="text-blue-300">
                  <svg className="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                  </svg>
                  123 Culinary Street, Food City
                </li>
                <li className="text-blue-300">
                  <svg className="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 4v8m0-8L3 8m9 4l9-4" />
                  </svg>
                  info@RasaNusantara.com
                </li>
                <li className="text-blue-300">
                  <svg className="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                  </svg>
                  +62 812 3456 7890
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>

      <style jsx>{`
        @keyframes fade-in-up {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .animate-fade-in-up {
          animation: fade-in-up 0.8s ease-out;
        }

        .animate-fade-in-up-delay {
          animation: fade-in-up 0.8s ease-out 0.2s both;
        }

        .animate-fade-in-up-delay-2 {
          animation: fade-in-up 0.8s ease-out 0.4s both;
        }

        .menu-card-animate {
          animation: fade-in-up 0.8s ease-out;
        }
      `}</style>
    </div>
  );
}

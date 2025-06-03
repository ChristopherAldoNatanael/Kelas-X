import React, { useState } from "react";
import useGet from "../hooks/useGet";
import axios from "axios";
import { link } from "../axios/link";

const User = () => {
  const { isi: users, loading, error, fetchData } = useGet("user");
  const [message, setMessage] = useState("");
  const [modalOpen, setModalOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [filterLevel, setFilterLevel] = useState("");
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    level: "admin",
  });

  const openModal = () => setModalOpen(true);
  const closeModal = () => {
    setModalOpen(false);
    setFormData({
      name: "",
      email: "",
      password: "",
      level: "admin",
    });
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(`${link}/register`, formData);
      if (response?.data?.status === "success") {
        setMessage("User berhasil ditambahkan!");
        setFormData({
          name: "",
          email: "",
          password: "",
          level: "admin",
        });
        closeModal();
        fetchData();
        setTimeout(() => setMessage(""), 3000);
      } else {
        setMessage("Terjadi kesalahan: Format response tidak sesuai");
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || "Terjadi kesalahan saat menambahkan user";
      setMessage(`Error: ${errorMessage}`);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm("Yakin ingin menghapus data ini?")) {
      try {
        const response = await axios.delete(`${link}/user/${id}`);
        if (response.data.status === "success") {
          setMessage("Data berhasil dihapus!");
          fetchData();
          setTimeout(() => setMessage(""), 3000);
        }
      } catch (err) {
        setMessage("Gagal menghapus data: " + (err.response?.data?.message || err.message));
      }
    }
  };

  const handleToggleStatus = async (id) => {
    const user = users.find((u) => u.id === id);
    if (!user) return;

    const newStatus = user.status === 1 ? 0 : 1;

    try {
      const response = await axios.put(`${link}/user/${id}`, { status: newStatus });
      if (response.data.status === "success") {
        setMessage("Status user berhasil diubah!");
        fetchData();
        setTimeout(() => setMessage(""), 3000);
      } else {
        setMessage("Gagal mengubah status user");
      }
    } catch (err) {
      setMessage("Gagal mengubah status user: " + (err.response?.data?.message || err.message));
    }
  };

  // Filter users based on search and level
  const filteredUsers = users?.filter(user => {
    const matchesSearch = user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         user.name?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesLevel = filterLevel === "" || user.level === filterLevel;
    return matchesSearch && matchesLevel;
  }) || [];

  const getLevelColor = (level) => {
    switch(level) {
      case "admin": return "from-purple-500 to-purple-600";
      case "koki": return "from-green-500 to-green-600";
      case "kasir": return "from-blue-500 to-blue-600";
      default: return "from-gray-500 to-gray-600";
    }
  };

  const getLevelIcon = (level) => {
    switch(level) {
      case "admin": return "👑";
      case "koki": return "👨‍🍳";
      case "kasir": return "💰";
      default: return "👤";
    }
  };

  let no = 1;

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
      <div className="max-w-7xl mx-auto">
        {/* Header Section */}
        <div className="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-100">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h1 className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                👥 User Management
              </h1>
              <p className="text-gray-600 mt-1">Kelola pengguna sistem dengan mudah</p>
            </div>
            
            <button 
              onClick={openModal}
              className="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transform transition-all duration-300 group"
            >
              <span className="mr-2 text-lg group-hover:rotate-12 transition-transform duration-300">➕</span>
              Tambah User Baru
            </button>
          </div>
        </div>

        {/* Filter Section */}
        <div className="bg-white rounded-2xl shadow-xl p-6 mb-6 border border-gray-100">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span className="text-gray-400 text-lg">🔍</span>
              </div>
              <input
                type="text"
                placeholder="Cari berdasarkan email atau nama..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
              />
            </div>
            
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span className="text-gray-400 text-lg">🏷️</span>
              </div>
              <select
                value={filterLevel}
                onChange={(e) => setFilterLevel(e.target.value)}
                className="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none bg-white"
              >
                <option value="">Semua Level</option>
                <option value="admin">Admin</option>
                <option value="koki">Koki</option>
                <option value="kasir">Kasir</option>
              </select>
            </div>
          </div>
        </div>

        {/* Messages */}
        {message && (
          <div className={`mb-6 p-4 rounded-xl border-l-4 ${
            message.includes("berhasil") 
              ? "bg-green-50 border-green-400 text-green-700" 
              : "bg-red-50 border-red-400 text-red-700"
          } shadow-lg animate-fade-in`}>
            <div className="flex items-center">
              <span className="mr-2 text-lg">
                {message.includes("berhasil") ? "✅" : "❌"}
              </span>
              {message}
            </div>
          </div>
        )}

        {error && (
          <div className="mb-6 p-4 rounded-xl border-l-4 bg-red-50 border-red-400 text-red-700 shadow-lg">
            <div className="flex items-center">
              <span className="mr-2 text-lg">⚠️</span>
              {error}
            </div>
          </div>
        )}

        {/* Table Section */}
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
          <div className="overflow-x-auto">
            <table className="min-w-full">
              <thead className="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                  <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                  <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Level</th>
                  <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                  <th className="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {loading ? (
                  <tr>
                    <td colSpan={5} className="px-6 py-12 text-center">
                      <div className="flex flex-col items-center">
                        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                        <p className="text-gray-500">Memuat data...</p>
                      </div>
                    </td>
                  </tr>
                ) : filteredUsers && filteredUsers.length > 0 ? (
                  filteredUsers.map((user, index) => (
                    <tr key={user.id} className="hover:bg-gray-50 transition-colors duration-200">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {no++}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className="flex-shrink-0 h-10 w-10">
                            <div className={`h-10 w-10 rounded-full bg-gradient-to-r ${getLevelColor(user.level)} flex items-center justify-center text-white font-bold text-sm`}>
                              {user.email.charAt(0).toUpperCase()}
                            </div>
                          </div>
                          <div className="ml-4">
                            <div className="text-sm font-medium text-gray-900">{user.email}</div>
                            {user.name && <div className="text-sm text-gray-500">{user.name}</div>}
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r ${getLevelColor(user.level)} text-white shadow-sm`}>
                          <span className="mr-1">{getLevelIcon(user.level)}</span>
                          {user.level}
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <button 
                          onClick={() => handleToggleStatus(user.id)}
                          className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold transition-all duration-300 hover:scale-105 ${
                            user.status === 1 
                              ? "bg-gradient-to-r from-green-400 to-green-500 text-white shadow-green-200 shadow-md" 
                              : "bg-gradient-to-r from-red-400 to-red-500 text-white shadow-red-200 shadow-md"
                          }`}
                        >
                          <span className="mr-1">{user.status === 1 ? "✅" : "🚫"}</span>
                          {user.status === 1 ? "Aktif" : "Banned"}
                        </button>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <button 
                          onClick={() => handleDelete(user.id)}
                          className="inline-flex items-center px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg hover:scale-105 transform transition-all duration-300 group"
                        >
                          <span className="mr-1 group-hover:animate-bounce">🗑️</span>
                          Hapus
                        </button>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={5} className="px-6 py-12 text-center">
                      <div className="flex flex-col items-center">
                        <span className="text-6xl mb-4">📭</span>
                        <p className="text-gray-500 text-lg">Tidak ada data user</p>
                      </div>
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Statistics Card */}
        {users && users.length > 0 && (
          <div className="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-blue-100">Total Users</p>
                  <p className="text-2xl font-bold">{users.length}</p>
                </div>
                <span className="text-3xl">👥</span>
              </div>
            </div>
            <div className="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-purple-100">Admin</p>
                  <p className="text-2xl font-bold">{users.filter(u => u.level === 'admin').length}</p>
                </div>
                <span className="text-3xl">👑</span>
              </div>
            </div>
            <div className="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-green-100">Koki</p>
                  <p className="text-2xl font-bold">{users.filter(u => u.level === 'koki').length}</p>
                </div>
                <span className="text-3xl">👨‍🍳</span>
              </div>
            </div>
            <div className="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-indigo-100">Kasir</p>
                  <p className="text-2xl font-bold">{users.filter(u => u.level === 'kasir').length}</p>
                </div>
                <span className="text-3xl">💰</span>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Enhanced Modal */}
      {modalOpen && (
        <>
          <div className="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40 flex items-center justify-center p-4">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100">
              <form onSubmit={handleSubmit}>
                <div className="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-t-2xl">
                  <div className="flex items-center justify-between">
                    <div>
                      <h3 className="text-xl font-bold">➕ Tambah User Baru</h3>
                      <p className="text-blue-100 text-sm">Lengkapi informasi pengguna</p>
                    </div>
                    <button 
                      type="button" 
                      onClick={closeModal}
                      className="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors duration-200"
                    >
                      ❌
                    </button>
                  </div>
                </div>
                
                <div className="p-6 space-y-4">
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">👤 Nama</label>
                    <input 
                      type="text" 
                      name="name" 
                      value={formData.name} 
                      onChange={handleChange} 
                      required 
                      className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                      placeholder="Masukkan nama lengkap"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">📧 Email</label>
                    <input 
                      type="email" 
                      name="email" 
                      value={formData.email} 
                      onChange={handleChange} 
                      required 
                      className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                      placeholder="example@email.com"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">🔒 Password</label>
                    <input 
                      type="password" 
                      name="password" 
                      value={formData.password} 
                      onChange={handleChange} 
                      required 
                      className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                      placeholder="Masukkan password"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-2">🏷️ Level</label>
                    <select 
                      name="level" 
                      value={formData.level} 
                      onChange={handleChange} 
                      required 
                      className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none bg-white"
                    >
                      <option value="admin">👑 Admin</option>
                      <option value="koki">👨‍🍳 Koki</option>
                      <option value="kasir">💰 Kasir</option>
                    </select>
                  </div>
                </div>
                
                <div className="flex gap-3 p-6 pt-0">
                  <button 
                    type="button" 
                    onClick={closeModal}
                    className="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200 font-semibold"
                  >
                    Batal
                  </button>
                  <button 
                    type="submit"
                    className="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transform transition-all duration-300 font-semibold"
                  >
                    💾 Simpan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </>
      )}

      <style jsx>{`
        .animate-fade-in {
          animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-10px); }
          to { opacity: 1; transform: translateY(0); }
        }
      `}</style>
    </div>
  );
};

export default User;
import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import axios from "axios";
import { link } from "../axios/link";
import useGet from "../hooks/useGet";

const Menu = () => {
  const [showForm, setShowForm] = useState(false);
  const [isEditMode, setIsEditMode] = useState(false);
  const [currentId, setCurrentId] = useState(null);
  const [isSaving, setIsSaving] = useState(false);
  const [localIsi, setLocalIsi] = useState([]);
  const [localLoading, setLocalLoading] = useState(true);
  const [localError, setLocalError] = useState(null);

  const {
    register,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm();
  const { isi: menuData, loading: menuLoading, error: menuError, fetchData: refreshMenu } = useGet("menu");
  const { isi: kategoriData, loading: kategoriLoading } = useGet("kategori");

  // Load data on mount and handle refresh
  useEffect(() => {
    const loadData = async () => {
      try {
        const response = await axios.get(`${link}/menu`);
        if (Array.isArray(response.data)) {
          setLocalIsi(response.data);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          setLocalIsi(response.data.data);
        }
      } catch (error) {
        setLocalError(error.message);
      } finally {
        setLocalLoading(false);
      }
    };

    loadData();
  }, []); // Removed hapus dependency, not needed with the new deleteMenu function

  // Replace the old hapus function with this new deleteMenu function
  const deleteMenu = async (id) => {
    if (window.confirm("Apakah anda yakin ingin menghapus data ini?")) {
      try {
        await axios.delete(`${link}/menu/${id}`);
        refreshMenu(); // Refresh the data after deletion
      } catch (error) {
        alert("Gagal menghapus data");
      }
    }
  };

  const onSubmit = async (data) => {
    try {
      setIsSaving(true);
      const formData = new FormData();
      formData.append("idkategori", data.idkategori);
      formData.append("menu", data.menu);
      formData.append("deskripsi", data.deskripsi);
      formData.append("harga", data.harga);

      if (data.gambar?.[0]) {
        formData.append("gambar", data.gambar[0]);
      }

      if (isEditMode) {
        // Update existing menu menggunakan POST dengan _method=PUT untuk form-data
        await axios.post(`${link}/menu/${currentId}?_method=PUT`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
      } else {
        // Create new menu
        await axios.post(`${link}/menu`, formData, {
          headers: {
            "Content-Type": "multipart/form-data", // Perbaiki Content-Type
          },
        });
      }

      // Memuat ulang data setelah submit menggunakan setUrl dari useGet
      refreshMenu();

      // Reset form dan sembunyikan
      reset();
      setShowForm(false);
      setIsEditMode(false);
      setCurrentId(null);
      alert("Data berhasil disimpan");
    } catch (error) {
      alert(error.response?.data?.message || "Gagal menyimpan data");
    } finally {
      setIsSaving(false);
    }
  };

  // Ubah fungsi handleEdit untuk menandai bahwa ini adalah mode edit
  const handleEdit = (item) => {
    setIsEditMode(true);
    setCurrentId(item.idmenu);
    setShowForm(true);

    // Isi form dengan data menu yang akan diedit
    setValue("idkategori", item.idkategori);
    setValue("menu", item.menu);
    setValue("deskripsi", item.deskripsi || "");
    setValue("harga", item.harga);
    // Gambar tidak diisi karena biasanya tidak ingin mengganti gambar setiap kali edit
  };

  const renderImage = (gambar, menu) => {
    if (!gambar) {
      return <img src="https://via.placeholder.com/100x100?text=No+Image" alt="No Image" className="menu-image" width={100} height={100} />;
    }
    // Asumsikan gambar hanya nama file, buat URL lengkap
    const imageUrl = `http://127.0.0.1:8000/upload/${gambar}`;
    return (
      <img
        src={imageUrl}
        alt={menu}
        className="menu-image"
        width={100}
        height={100}
        onError={(e) => {
          e.target.onerror = null;
          e.target.src = "https://via.placeholder.com/100x100?text=No+Image";
        }}
        style={{ objectFit: "cover", borderRadius: 8, cursor: "pointer" }}
        onClick={() => window.open(imageUrl, "_blank")}
      />
    );
  };

  // Menampilkan loading atau error dari useGet
  if (menuLoading) return <div>Loading data...</div>;
  if (menuError) return <div>Error: {menuError}</div>;

  return (
    <div className="p-6 max-w-7xl mx-auto">
      {localError && <div className="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">{localError}</div>}

      <div className="mb-6 flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Data Menu</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola menu restoran Anda</p>
        </div>
        <button
          className={`px-4 py-2 rounded-lg flex items-center space-x-2 transition-all duration-200 ${showForm ? "bg-gray-200 hover:bg-gray-300 text-gray-700" : "bg-blue-600 hover:bg-blue-700 text-white"}`}
          onClick={() => {
            setShowForm(!showForm);
            if (!showForm) {
              reset();
              setIsEditMode(false);
              setCurrentId(null);
            }
          }}
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {showForm ? <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /> : <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />}
          </svg>
          <span>{showForm ? "Tutup Form" : "Tambah Menu"}</span>
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
          <div className="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h3 className="text-lg font-semibold text-white">{isEditMode ? "Edit Menu" : "Tambah Menu Baru"}</h3>
          </div>
          <div className="p-6">
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                  <select
                    className={`w-full rounded-lg border ${
                      errors.idkategori ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                    {...register("idkategori", {
                      required: "Kategori harus dipilih",
                    })}
                  >
                    <option value="">Pilih Kategori</option>
                    {!kategoriLoading &&
                      kategoriData &&
                      kategoriData.map((item) => (
                        <option key={item.idkategori} value={item.idkategori}>
                          {item.kategori}
                        </option>
                      ))}
                  </select>
                  {errors.idkategori && <p className="mt-1 text-sm text-red-600">{errors.idkategori.message}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Nama Menu</label>
                  <input
                    type="text"
                    className={`w-full rounded-lg border ${
                      errors.menu ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                    {...register("menu", { required: "Nama menu harus diisi" })}
                  />
                  {errors.menu && <p className="mt-1 text-sm text-red-600">{errors.menu.message}</p>}
                </div>

                <div className="md:col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                  <textarea
                    rows="3"
                    className={`w-full rounded-lg border ${
                      errors.deskripsi ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                    {...register("deskripsi", {
                      required: "Deskripsi harus diisi",
                    })}
                  ></textarea>
                  {errors.deskripsi && <p className="mt-1 text-sm text-red-600">{errors.deskripsi.message}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                  <div className="relative">
                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    <input
                      type="number"
                      className={`w-full pl-10 rounded-lg border ${
                        errors.harga ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                      } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                      {...register("harga", {
                        required: "Harga harus diisi",
                        min: {
                          value: 1000,
                          message: "Harga minimal Rp 1.000",
                        },
                      })}
                    />
                  </div>
                  {errors.harga && <p className="mt-1 text-sm text-red-600">{errors.harga.message}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
                  <input
                    type="file"
                    accept="image/*"
                    className={`w-full rounded-lg border ${
                      errors.gambar ? "border-red-300 focus:border-red-500 focus:ring-red-500" : "border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    } focus:ring-2 focus:ring-opacity-50 transition-colors duration-200`}
                    {...register("gambar", {
                      required: isEditMode ? false : "Gambar harus dipilih",
                    })}
                  />
                  {errors.gambar && <p className="mt-1 text-sm text-red-600">{errors.gambar.message}</p>}
                  {isEditMode && <p className="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengganti gambar</p>}
                </div>
              </div>

              <div className="flex space-x-3">
                <button
                  type="submit"
                  disabled={isSaving}
                  className={`px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 ${
                    isSaving && "opacity-50 cursor-not-allowed"
                  }`}
                >
                  {isSaving ? (
                    <div className="flex items-center space-x-2">
                      <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                      <span>Menyimpan...</span>
                    </div>
                  ) : isEditMode ? (
                    "Update Menu"
                  ) : (
                    "Simpan Menu"
                  )}
                </button>
                <button
                  type="button"
                  onClick={() => reset()}
                  className="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                >
                  Reset
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="bg-white rounded-xl shadow-lg overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50 text-left">
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {menuData && menuData.length > 0 ? (
                menuData.map((item, index) => (
                  <tr key={item.idmenu} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{index + 1}</td>
                    <td className="px-6 py-4 whitespace-nowrap">{renderImage(item.gambar, item.menu)}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{item.menu}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{item.nama_kategori}</td>
                    <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{item.deskripsi}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {item.harga.toLocaleString("id-ID")}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                      <div className="flex space-x-2">
                        <button
                          onClick={() => handleEdit(item)}
                          className="px-3 py-1 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                          Edit
                        </button>
                        <button
                          onClick={() => deleteMenu(item.idmenu)}
                          className="px-3 py-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="7" className="px-6 py-4 text-center text-gray-500">
                    {menuLoading ? (
                      <div className="flex justify-center items-center space-x-2">
                        <div className="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <span>Loading...</span>
                      </div>
                    ) : (
                      "Tidak ada data menu"
                    )}
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

export default Menu;

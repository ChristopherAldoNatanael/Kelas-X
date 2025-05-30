import React, { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import axios from "axios";
import { link } from "../axios/link";
import useGet from "../hooks/useGet";
import "../styles/Menu.css"; // Add this import

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
    <div className="container mt-4">
      {localError && <div className="alert alert-danger">{localError}</div>}

      {localLoading ? (
        <div>Loading...</div>
      ) : (
        <>
          <div className="d-flex justify-content-between align-items-center mb-3">
            <h2>Data Menu</h2>
            <button
              className="btn btn-primary"
              onClick={() => {
                setShowForm(!showForm);
                if (!showForm) {
                  // Reset form saat membuka form baru
                  reset();
                  setIsEditMode(false);
                  setCurrentId(null);
                }
              }}
            >
              {showForm ? "Tutup Form" : "Tambah Menu"}
            </button>
          </div>

          {showForm && (
            <div className="card mb-4">
              <div className="card-header bg-primary text-white">
                <h5 className="m-0">Form Menu</h5>
              </div>
              <div className="card-body">
                <form onSubmit={handleSubmit(onSubmit)}>
                  <div className="mb-3">
                    <label htmlFor="idkategori" className="form-label">
                      Kategori
                    </label>
                    <select
                      className={`form-select ${errors.idkategori ? "is-invalid" : ""}`}
                      id="idkategori"
                      {...register("idkategori", {
                        required: "Kategori harus dipilih",
                      })}
                    >
                      <option value="">Pilih Kategori</option>
                      {/* Menggunakan data kategori dari useGet dengan pengecekan */}
                      {!kategoriLoading &&
                        kategoriData &&
                        kategoriData.map((item, index) => (
                          <option key={index} value={item.idkategori}>
                            {item.kategori}
                          </option>
                        ))}
                    </select>
                    {errors.idkategori && <div className="invalid-feedback">{errors.idkategori.message}</div>}
                  </div>

                  <div className="mb-3">
                    <label htmlFor="menu" className="form-label">
                      Nama Menu
                    </label>
                    <input
                      type="text"
                      className={`form-control ${errors.menu ? "is-invalid" : ""}`}
                      id="menu"
                      {...register("menu", {
                        required: "Nama menu harus diisi",
                      })}
                    />
                    {errors.menu && <div className="invalid-feedback">{errors.menu.message}</div>}
                  </div>

                  <div className="mb-3">
                    <label htmlFor="deskripsi" className="form-label">
                      Deskripsi
                    </label>
                    <textarea
                      className={`form-control ${errors.deskripsi ? "is-invalid" : ""}`}
                      id="deskripsi"
                      rows="3"
                      {...register("deskripsi", {
                        required: "Deskripsi harus diisi",
                      })}
                    ></textarea>
                    {errors.deskripsi && <div className="invalid-feedback">{errors.deskripsi.message}</div>}
                  </div>

                  <div className="mb-3">
                    <label htmlFor="harga" className="form-label">
                      Harga
                    </label>
                    <input
                      type="number"
                      className={`form-control ${errors.harga ? "is-invalid" : ""}`}
                      id="harga"
                      {...register("harga", {
                        required: "Harga harus diisi",
                        min: {
                          value: 1000,
                          message: "Harga minimal Rp 1.000",
                        },
                      })}
                    />
                    {errors.harga && <div className="invalid-feedback">{errors.harga.message}</div>}
                  </div>

                  <div className="mb-3">
                    <label htmlFor="gambar" className="form-label">
                      Gambar
                    </label>
                    <input
                      type="file"
                      className={`form-control ${errors.gambar ? "is-invalid" : ""}`}
                      id="gambar"
                      accept="image/*"
                      {...register("gambar", {
                        required: isEditMode ? false : "Gambar harus dipilih",
                      })}
                    />
                    {errors.gambar && <div className="invalid-feedback">{errors.gambar.message}</div>}
                    {isEditMode && <small className="form-text text-muted">Biarkan kosong jika tidak ingin mengganti gambar</small>}
                  </div>

                  <div className="d-flex gap-2">
                    <button type="submit" className="btn btn-success" disabled={isSaving}>
                      {isSaving ? "Menyimpan..." : isEditMode ? "Update" : "Simpan"}
                    </button>
                    <button
                      type="button"
                      className="btn btn-secondary"
                      onClick={() => reset()} // Reset form
                    >
                      Reset
                    </button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* Replace the card layout with table */}
          <div className="table-responsive">
            <table className="table table-striped table-hover">
              <thead className="table-dark">
                <tr>
                  <th>No</th>
                  <th>Gambar</th>
                  <th>Menu</th>
                  <th>Kategori</th>
                  <th>Deskripsi</th>
                  <th>Harga</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {menuData && menuData.length > 0 ? (
                  menuData.map((item, index) => (
                    <tr key={item.idmenu}>
                      <td>{index + 1}</td>
                      <td>{renderImage(item.gambar, item.menu)}</td>
                      <td>{item.menu}</td>
                      <td>{item.nama_kategori}</td>
                      <td>{item.deskripsi}</td>
                      <td>Rp. {item.harga.toLocaleString("id-ID")}</td>
                      <td>
                        <div className="btn-group">
                          <button className="btn btn-warning btn-sm" onClick={() => handleEdit(item)}>
                            Edit
                          </button>
                          <button className="btn btn-danger btn-sm ms-1" onClick={() => deleteMenu(item.idmenu)}>
                            Hapus
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="7" className="text-center">
                      {menuLoading ? (
                        <div className="spinner-border text-primary" role="status">
                          <span className="visually-hidden">Loading...</span>
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
        </>
      )}

      {/* Your existing form modal code */}
    </div>
  );
};

export default Menu;

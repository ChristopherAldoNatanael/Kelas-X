import React, { useState } from 'react';
import './Kontak.css';

const Kontak = () => {
  const [formData, setFormData] = useState({
    nama: '',
    email: '',
    subjek: '',
    pesan: ''
  });
  
  const [formStatus, setFormStatus] = useState({
    submitted: false,
    error: false,
    message: ''
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: value
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Simulasi pengiriman form
    if (formData.nama && formData.email && formData.pesan) {
      setFormStatus({
        submitted: true,
        error: false,
        message: 'Terima kasih! Pesan Anda telah terkirim.'
      });
      // Reset form setelah berhasil
      setFormData({
        nama: '',
        email: '',
        subjek: '',
        pesan: ''
      });
    } else {
      setFormStatus({
        submitted: false,
        error: true,
        message: 'Mohon lengkapi semua field yang diperlukan.'
      });
    }
  };

  return (
    <div className="kontak-container">
      <div className="kontak-header">
        <h1>Hubungi Kami</h1>
        <p>Kami siap membantu Anda. Jangan ragu untuk menghubungi kami.</p>
      </div>

      <div className="kontak-content">
        <div className="kontak-info">
          <div className="kontak-card">
            <div className="kontak-icon">📍</div>
            <h3>Alamat</h3>
            <p>Jl. Pendidikan No. 123, Kota Teknologi, Indonesia 12345</p>
          </div>
          
          <div className="kontak-card">
            <div className="kontak-icon">📞</div>
            <h3>Telepon</h3>
            <p>+62 123 4567 890</p>
            <p>+62 098 7654 321</p>
          </div>
          
          <div className="kontak-card">
            <div className="kontak-icon">✉️</div>
            <h3>Email</h3>
            <p>info@sekolahkita.ac.id</p>
            <p>admin@sekolahkita.ac.id</p>
          </div>
          
          <div className="kontak-card">
            <div className="kontak-icon">🕒</div>
            <h3>Jam Operasional</h3>
            <p>Senin - Jumat: 08.00 - 16.00</p>
            <p>Sabtu: 08.00 - 12.00</p>
          </div>
        </div>
        
        <div className="kontak-form">
          <h2>Kirim Pesan</h2>
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label htmlFor="nama">Nama Lengkap</label>
              <input
                type="text"
                id="nama"
                name="nama"
                value={formData.nama}
                onChange={handleChange}
                placeholder="Masukkan nama lengkap Anda"
                required
              />
            </div>
            
            <div className="form-group">
              <label htmlFor="email">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="Masukkan alamat email Anda"
                required
              />
            </div>
            
            <div className="form-group">
              <label htmlFor="subjek">Subjek</label>
              <input
                type="text"
                id="subjek"
                name="subjek"
                value={formData.subjek}
                onChange={handleChange}
                placeholder="Masukkan subjek pesan"
              />
            </div>
            
            <div className="form-group">
              <label htmlFor="pesan">Pesan</label>
              <textarea
                id="pesan"
                name="pesan"
                value={formData.pesan}
                onChange={handleChange}
                placeholder="Tulis pesan Anda di sini"
                rows="5"
                required
              ></textarea>
            </div>
            
            <button type="submit" className="submit-btn">Kirim Pesan</button>
            
            {formStatus.message && (
              <div className={`form-message ${formStatus.error ? 'error' : 'success'}`}>
                {formStatus.message}
              </div>
            )}
          </form>
        </div>
      </div>
      
      <div className="kontak-map">
        <h2>Lokasi Kami</h2>
        <div className="map-container">
          {/* Di sini bisa ditambahkan peta menggunakan Google Maps atau peta statis */}
          <div className="map-placeholder">
            <p>Peta Lokasi Sekolah</p>
            <p>Jl. Pendidikan No. 123, Kota Teknologi, Indonesia 12345</p>
          </div>
        </div>
      </div>
      
      <div className="kontak-social">
        <h2>Ikuti Kami</h2>
        <div className="social-icons">
          <a href="#" className="social-icon">
            <span>Facebook</span>
          </a>
          <a href="#" className="social-icon">
            <span>Instagram</span>
          </a>
          <a href="#" className="social-icon">
            <span>Twitter</span>
          </a>
          <a href="#" className="social-icon">
            <span>YouTube</span>
          </a>
        </div>
      </div>
    </div>
  );
};

export default Kontak;
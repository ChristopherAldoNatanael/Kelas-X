import React from 'react';
import './Home.css';

const Home = () => {
  return (
    <div className="home-container">
      <div className="hero-section">
        <div className="hero-content">
          <h1>Selamat Datang di Website Kami</h1>
          <p>Tempat belajar dan berkembang bersama untuk masa depan yang lebih baik</p>
          <button className="cta-button">Mulai Sekarang</button>
        </div>
      </div>

      <div className="features-section">
        <h2 className="section-title">Layanan Kami</h2>
        <div className="features-grid">
          <div className="feature-card">
            <div className="feature-icon">📚</div>
            <h3>Pembelajaran Berkualitas</h3>
            <p>Kurikulum yang dirancang oleh para ahli pendidikan</p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">💻</div>
            <h3>Fasilitas Modern</h3>
            <p>Dilengkapi dengan teknologi terbaru untuk mendukung pembelajaran</p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">🏆</div>
            <h3>Prestasi Siswa</h3>
            <p>Berbagai penghargaan telah diraih oleh siswa-siswi kami</p>
          </div>
        </div>
      </div>

      <div className="testimonial-section">
        <h2 className="section-title">Testimoni</h2>
        <div className="testimonial-container">
          <div className="testimonial-card">
            <p>"Pengalaman belajar yang luar biasa dengan guru-guru yang kompeten."</p>
            <div className="testimonial-author">- Budi Santoso, Orang Tua Siswa</div>
          </div>
          <div className="testimonial-card">
            <p>"Fasilitas lengkap dan lingkungan yang mendukung untuk belajar."</p>
            <div className="testimonial-author">- Ani Wijaya, Alumni</div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Home;
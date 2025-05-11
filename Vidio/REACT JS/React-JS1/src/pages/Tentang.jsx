import React from 'react';
import './Tentang.css';

const Tentang = () => {
  return (
    <div className="tentang-container">
      <div className="tentang-header">
        <h1>Tentang Kami</h1>
        <p>Mengenal lebih dekat dengan institusi kami</p>
      </div>

      <div className="tentang-content">
        <div className="tentang-image">
          <img src="https://via.placeholder.com/600x400" alt="Gambar Sekolah" />
        </div>
        
        <div className="tentang-text">
          <h2>Visi Kami</h2>
          <p>
            Menjadi lembaga pendidikan terkemuka yang menghasilkan lulusan berkualitas, 
            berkarakter, dan siap menghadapi tantangan global dengan keunggulan di bidang 
            teknologi dan inovasi.
          </p>
          
          <h2>Misi Kami</h2>
          <ul>
            <li>Menyelenggarakan pendidikan berkualitas dengan kurikulum yang relevan dengan kebutuhan industri</li>
            <li>Mengembangkan karakter dan potensi siswa melalui berbagai kegiatan ekstrakurikuler</li>
            <li>Membangun kerjasama dengan berbagai pihak untuk meningkatkan kualitas pendidikan</li>
            <li>Menciptakan lingkungan belajar yang kondusif dan didukung teknologi modern</li>
          </ul>
          
          <h2>Nilai-Nilai Kami</h2>
          <div className="nilai-grid">
            <div className="nilai-item">
              <h3>Integritas</h3>
              <p>Menjunjung tinggi kejujuran dan etika dalam setiap aspek pendidikan</p>
            </div>
            <div className="nilai-item">
              <h3>Inovasi</h3>
              <p>Selalu berusaha mengembangkan metode pembelajaran yang lebih baik</p>
            </div>
            <div className="nilai-item">
              <h3>Kolaborasi</h3>
              <p>Bekerja sama untuk mencapai tujuan bersama</p>
            </div>
            <div className="nilai-item">
              <h3>Keunggulan</h3>
              <p>Berkomitmen untuk memberikan yang terbaik dalam segala hal</p>
            </div>
          </div>
        </div>
      </div>
      
      <div className="team-section">
        <h2 className="section-title">Tim Kami</h2>
        <div className="team-grid">
          <div className="team-card">
            <div className="team-image">
              <img src="https://via.placeholder.com/150" alt="Kepala Sekolah" />
            </div>
            <h3>Dr. Budi Santoso</h3>
            <p className="team-position">Kepala Sekolah</p>
            <p>Berpengalaman lebih dari 20 tahun di bidang pendidikan</p>
          </div>
          <div className="team-card">
            <div className="team-image">
              <img src="https://via.placeholder.com/150" alt="Wakil Kepala Sekolah" />
            </div>
            <h3>Dra. Siti Aminah</h3>
            <p className="team-position">Wakil Kepala Sekolah</p>
            <p>Ahli kurikulum dengan dedikasi tinggi</p>
          </div>
          <div className="team-card">
            <div className="team-image">
              <img src="https://via.placeholder.com/150" alt="Kepala Program RPL" />
            </div>
            <h3>Ir. Ahmad Wijaya</h3>
            <p className="team-position">Kepala Program RPL</p>
            <p>Praktisi IT dengan pengalaman industri 15 tahun</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Tentang;
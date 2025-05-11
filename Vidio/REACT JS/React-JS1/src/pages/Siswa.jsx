import React, { useState, useEffect } from 'react';
import './Siswa.css';

const Siswa = () => {
  const [siswaList, setSiswaList] = useState([]);
  const [filteredSiswa, setFilteredSiswa] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedKelas, setSelectedKelas] = useState('semua');
  const [isLoading, setIsLoading] = useState(true);

  // Data siswa (simulasi data dari API)
  useEffect(() => {
    // Simulasi fetch data dari server
    setTimeout(() => {
      const dummyData = [
        { id: 1, nama: 'Ahmad Rizky', kelas: 'X RPL 1', nilai: 90, foto: 'https://randomuser.me/api/portraits/men/1.jpg' },
        { id: 2, nama: 'Budi Santoso', kelas: 'X RPL 1', nilai: 85, foto: 'https://randomuser.me/api/portraits/men/2.jpg' },
        { id: 3, nama: 'Citra Dewi', kelas: 'X RPL 2', nilai: 95, foto: 'https://randomuser.me/api/portraits/women/3.jpg' },
        { id: 4, nama: 'Dian Purnama', kelas: 'X RPL 2', nilai: 88, foto: 'https://randomuser.me/api/portraits/women/4.jpg' },
        { id: 5, nama: 'Eko Prasetyo', kelas: 'X RPL 3', nilai: 92, foto: 'https://randomuser.me/api/portraits/men/5.jpg' },
        { id: 6, nama: 'Fitri Handayani', kelas: 'X RPL 3', nilai: 87, foto: 'https://randomuser.me/api/portraits/women/6.jpg' },
        { id: 7, nama: 'Galih Pratama', kelas: 'X RPL 1', nilai: 89, foto: 'https://randomuser.me/api/portraits/men/7.jpg' },
        { id: 8, nama: 'Hana Safira', kelas: 'X RPL 2', nilai: 94, foto: 'https://randomuser.me/api/portraits/women/8.jpg' },
        { id: 9, nama: 'Irfan Maulana', kelas: 'X RPL 3', nilai: 91, foto: 'https://randomuser.me/api/portraits/men/9.jpg' },
        { id: 10, nama: 'Jasmine Putri', kelas: 'X RPL 1', nilai: 86, foto: 'https://randomuser.me/api/portraits/women/10.jpg' },
        { id: 11, nama: 'Kevin Wijaya', kelas: 'X RPL 2', nilai: 93, foto: 'https://randomuser.me/api/portraits/men/11.jpg' },
        { id: 12, nama: 'Lina Anggraini', kelas: 'X RPL 3', nilai: 84, foto: 'https://randomuser.me/api/portraits/women/12.jpg' },
      ];
      
      setSiswaList(dummyData);
      setFilteredSiswa(dummyData);
      setIsLoading(false);
    }, 1000);
  }, []);

  // Filter siswa berdasarkan pencarian dan kelas
  useEffect(() => {
    let result = siswaList;
    
    // Filter berdasarkan pencarian
    if (searchTerm) {
      result = result.filter(siswa => 
        siswa.nama.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }
    
    // Filter berdasarkan kelas
    if (selectedKelas !== 'semua') {
      result = result.filter(siswa => siswa.kelas === selectedKelas);
    }
    
    setFilteredSiswa(result);
  }, [searchTerm, selectedKelas, siswaList]);

  // Handler untuk pencarian
  const handleSearch = (e) => {
    setSearchTerm(e.target.value);
  };

  // Handler untuk filter kelas
  const handleKelasFilter = (e) => {
    setSelectedKelas(e.target.value);
  };

  // Mendapatkan daftar kelas unik
  const kelasList = ['semua', ...new Set(siswaList.map(siswa => siswa.kelas))];

  return (
    <div className="siswa-container">
      <div className="siswa-header">
        <h1>Daftar Siswa</h1>
        <p>Informasi lengkap tentang siswa-siswi SMK Teknologi</p>
      </div>

      <div className="siswa-filter">
        <div className="search-box">
          <input
            type="text"
            placeholder="Cari siswa..."
            value={searchTerm}
            onChange={handleSearch}
          />
          <span className="search-icon">🔍</span>
        </div>
        
        <div className="filter-box">
          <label htmlFor="kelas-filter">Filter Kelas:</label>
          <select
            id="kelas-filter"
            value={selectedKelas}
            onChange={handleKelasFilter}
          >
            {kelasList.map((kelas, index) => (
              <option key={index} value={kelas}>
                {kelas === 'semua' ? 'Semua Kelas' : kelas}
              </option>
            ))}
          </select>
        </div>
      </div>

      {isLoading ? (
        <div className="loading-container">
          <div className="loading-spinner"></div>
          <p>Memuat data siswa...</p>
        </div>
      ) : (
        <>
          <div className="siswa-stats">
            <div className="stat-card">
              <h3>Total Siswa</h3>
              <p>{siswaList.length}</p>
            </div>
            <div className="stat-card">
              <h3>Rata-rata Nilai</h3>
              <p>{(siswaList.reduce((acc, curr) => acc + curr.nilai, 0) / siswaList.length).toFixed(1)}</p>
            </div>
            <div className="stat-card">
              <h3>Nilai Tertinggi</h3>
              <p>{Math.max(...siswaList.map(s => s.nilai))}</p>
            </div>
          </div>

          {filteredSiswa.length === 0 ? (
            <div className="no-data">
              <p>Tidak ada siswa yang ditemukan</p>
            </div>
          ) : (
            <div className="siswa-grid">
              {filteredSiswa.map(siswa => (
                <div key={siswa.id} className="siswa-card">
                  <div className="siswa-foto">
                    <img src={siswa.foto} alt={siswa.nama} />
                  </div>
                  <div className="siswa-info">
                    <h3>{siswa.nama}</h3>
                    <p className="siswa-kelas">{siswa.kelas}</p>
                    <div className="siswa-nilai">
                      <span>Nilai:</span>
                      <span className={`nilai ${siswa.nilai >= 90 ? 'excellent' : siswa.nilai >= 80 ? 'good' : 'average'}`}>
                        {siswa.nilai}
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default Siswa;
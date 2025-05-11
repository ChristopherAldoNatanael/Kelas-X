import { useState } from 'react'
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom'
import './App.css'

// Import halaman-halaman
import Home from './pages/Home'
import Tentang from './pages/Tentang'
import Sejarah from './pages/Sejarah'
import Kontak from './pages/Kontak'
import Siswa from './pages/Siswa'

function App() {
  const [isNavOpen, setIsNavOpen] = useState(false);

  const toggleNav = () => {
    setIsNavOpen(!isNavOpen);
  };

  return (
    <Router>
      <div className="app-container">
        <header className="header">
          <div className="logo">
            <h1>SMK Teknologi</h1>
          </div>
          <div className="mobile-toggle" onClick={toggleNav}>
            <span></span>
            <span></span>
            <span></span>
          </div>
          <nav className={`navigation ${isNavOpen ? 'active' : ''}`}>
            <ul>
              <li><Link to="/" onClick={() => setIsNavOpen(false)}>Home</Link></li>
              <li><Link to="/tentang" onClick={() => setIsNavOpen(false)}>Tentang</Link></li>
              <li><Link to="/sejarah" onClick={() => setIsNavOpen(false)}>Sejarah</Link></li>
              <li><Link to="/kontak" onClick={() => setIsNavOpen(false)}>Kontak</Link></li>
              <li><Link to="/siswa" onClick={() => setIsNavOpen(false)}>Siswa</Link></li>
            </ul>
          </nav>
        </header>

        <main className="main-content">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/tentang" element={<Tentang />} />
            <Route path="/sejarah" element={<Sejarah />} />
            <Route path="/kontak" element={<Kontak />} />
            <Route path="/siswa" element={<Siswa />} />
          </Routes>
        </main>

        <footer className="footer">
          <div className="footer-content">
            <div className="footer-section">
              <h3>SMK Teknologi</h3>
              <p>Mendidik generasi masa depan dengan teknologi terkini</p>
            </div>
            <div className="footer-section">
              <h3>Kontak</h3>
              <p>Email: info@smkteknologi.ac.id</p>
              <p>Telepon: +62 123 4567 890</p>
            </div>
            <div className="footer-section">
              <h3>Ikuti Kami</h3>
              <div className="social-links">
                <a href="#" className="social-link">Facebook</a>
                <a href="#" className="social-link">Instagram</a>
                <a href="#" className="social-link">Twitter</a>
              </div>
            </div>
          </div>
          <div className="footer-bottom">
            <p>&copy; {new Date().getFullYear()} SMK Teknologi. Hak Cipta Dilindungi.</p>
          </div>
        </footer>
      </div>
    </Router>
  )
}

export default App

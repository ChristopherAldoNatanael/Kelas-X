import React from 'react';
import './Sejarah.css';

const Sejarah = () => {
  const timelineEvents = [
    {
      year: '1985',
      title: 'Pendirian Sekolah',
      description: 'Sekolah kami didirikan oleh sekelompok pendidik visioner yang ingin menciptakan pendidikan berkualitas.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '1995',
      title: 'Pengembangan Kurikulum',
      description: 'Melakukan pembaruan kurikulum untuk mengikuti perkembangan teknologi dan kebutuhan industri.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '2005',
      title: 'Pembangunan Gedung Baru',
      description: 'Membangun fasilitas modern untuk mendukung proses pembelajaran yang lebih baik.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '2010',
      title: 'Akreditasi A',
      description: 'Mendapatkan akreditasi A dari Badan Akreditasi Nasional sebagai bukti kualitas pendidikan.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '2015',
      title: 'Jurusan RPL Dibuka',
      description: 'Membuka jurusan Rekayasa Perangkat Lunak untuk menjawab kebutuhan industri teknologi.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '2020',
      title: 'Kerjasama Industri',
      description: 'Menjalin kerjasama dengan berbagai perusahaan teknologi untuk program magang siswa.',
      image: 'https://via.placeholder.com/300x200'
    },
    {
      year: '2023',
      title: 'Inovasi Pembelajaran Digital',
      description: 'Mengembangkan platform pembelajaran digital untuk mendukung pendidikan jarak jauh.',
      image: 'https://via.placeholder.com/300x200'
    }
  ];

  return (
    <div className="sejarah-container">
      <div className="sejarah-header">
        <h1>Sejarah Kami</h1>
        <p>Perjalanan kami dalam membangun pendidikan berkualitas</p>
      </div>

      <div className="sejarah-intro">
        <div className="sejarah-intro-text">
          <h2>Awal Mula</h2>
          <p>
            Perjalanan kami dimulai dari sebuah visi sederhana untuk menciptakan 
            lembaga pendidikan yang tidak hanya fokus pada akademik, tetapi juga 
            pengembangan karakter dan keterampilan praktis siswa. Didirikan pada 
            tahun 1985, sekolah kami telah melalui berbagai fase perkembangan dan 
            terus beradaptasi dengan perubahan zaman.
          </p>
          <p>
            Komitmen kami terhadap kualitas pendidikan telah membawa sekolah ini 
            menjadi salah satu lembaga pendidikan terkemuka di daerah ini. Kami 
            percaya bahwa pendidikan adalah investasi untuk masa depan, dan kami 
            terus berusaha memberikan yang terbaik bagi para siswa.
          </p>
        </div>
        <div className="sejarah-intro-image">
          <img src="https://via.placeholder.com/500x300" alt="Foto Sekolah Lama" />
        </div>
      </div>

      <h2 className="timeline-title">Lini Masa Perjalanan Kami</h2>
      <div className="timeline">
        {timelineEvents.map((event, index) => (
          <div key={index} className={`timeline-item ${index % 2 === 0 ? 'left' : 'right'}`}>
            <div className="timeline-content">
              <div className="timeline-year">{event.year}</div>
              <h3>{event.title}</h3>
              <p>{event.description}</p>
              <img src={event.image} alt={event.title} />
            </div>
          </div>
        ))}
      </div>

      <div className="sejarah-quote">
        <blockquote>
          "Pendidikan adalah senjata paling ampuh yang dapat Anda gunakan untuk mengubah dunia."
          <cite>— Nelson Mandela</cite>
        </blockquote>
      </div>

      <div className="sejarah-future">
        <h2>Visi Ke Depan</h2>
        <p>
          Kami terus berkomitmen untuk mengembangkan pendidikan berkualitas dan 
          relevan dengan kebutuhan masa depan. Dengan fondasi kuat yang telah 
          dibangun selama bertahun-tahun, kami optimis dapat terus berkontribusi 
          dalam menciptakan generasi penerus yang unggul dan siap menghadapi 
          tantangan global.
        </p>
      </div>
    </div>
  );
};

export default Sejarah;
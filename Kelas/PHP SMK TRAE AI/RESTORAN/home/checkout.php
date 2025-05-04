<?php 

    if (isset($_GET['total'])) {
        $total = $_GET['total'];
        $idorder = idorder();
        $idpelanggan = $_SESSION['idpelanggan'];
        $tgl = date('Y-m-d');

        $sql = "SELECT * FROM tblorder WHERE idorder = $idorder";
        $count = $db->rowCOUNT($sql);

        if ($count == 0) {
            insertorder($idorder, $idpelanggan, $tgl, $total);
            insertOrderDetail($idorder);
        } else {
            insertOrderDetail($idorder);
        }

        kosongkanSession();
        echo '<script>window.location.href="?f=home&m=checkout";</script>';
    } else {
        info();
    }

    function idorder()
    {
        global $db;
        $sql = "SELECT idorder FROM tblorder ORDER BY idorder DESC";
        $jumlah = $db->rowCOUNT($sql);
        if ($jumlah == 0) {
            $id = 1;
        }else {
            $item = $db->getITEM($sql);
            $id = $item['idorder']+1;
        }
        return $id;
    }

    function insertorder($idorder, $idpelanggan, $tgl, $total)
    {
        global $db;    
        $sql = "INSERT INTO tblorder VALUES ($idorder, $idpelanggan, '$tgl',$total,0,0,0)";

        $db->runSQL($sql);
    }

    function insertOrderDetail($idorder=1)
    {
        global $db;

        foreach ($_SESSION as $key => $value) {
            if ($key<>'pelanggan' && $key<> 'idpelanggan' && $key<> 'user' && $key<> 'level' && $key<> 'iduser') {
                $id = substr($key,1);

                $sql = "SELECT * FROM tblmenu WHERE idmenu=$id";

                $row = $db->getAll($sql);

                foreach ($row as $r) {
                    $idmenu = $r['idmenu'];
                    $harga = $r['harga'];
                    $sql = "INSERT INTO  tblorderdetail VALUES ('',$idorder,$idmenu,$value,$harga)";
                    $db->runSQL($sql);
                }
            }
        }
    }

    function kosongkanSession()
    {
        foreach ($_SESSION as $key => $value) {
            if ($key<>'pelanggan' && $key<> 'idpelanggan' && $key<> 'user' && $key<> 'level' && $key<> 'iduser') {
                $id = substr($key,1);

                unset($_SESSION['_'.$id]);

            }
        }
    }

    function info()
    {
        ?>
        <div class="checkout-success">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="success-content">
                    <h2 class="success-title">Pesanan Berhasil!</h2>
                    <p class="success-message">Terima Kasih Sudah Berbelanja Di Warung Aldo</p>
                    <div class="order-details">
                        <div class="detail-item">
                            <span class="detail-label">Status Pesanan:</span>
                            <span class="detail-value">Sedang Diproses</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Waktu Pemesanan:</span>
                            <span class="detail-value"><?php echo date('d F Y, H:i'); ?></span>
                        </div>
                    </div>
                    <div class="success-actions">
                        <a href="?f=home&m=produk" class="btn btn-primary action-btn">
                            <i class="fas fa-utensils mr-2"></i>Pesan Lagi
                        </a>
                        <a href="?f=home&m=histori" class="btn btn-outline-primary action-btn">
                            <i class="fas fa-history mr-2"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .checkout-success {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 80vh;
                padding: 20px;
                background-color: #f8f9fa;
            }
            
            .success-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                width: 100%;
                max-width: 800px;
                display: flex;
                flex-direction: column;
                animation: fadeIn 0.8s ease-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .success-icon {
                background: linear-gradient(135deg, #4CAF50, #2E7D32);
                color: white;
                padding: 30px 0;
                text-align: center;
            }
            
            .success-icon i {
                font-size: 80px;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            
            .success-content {
                padding: 40px;
                text-align: center;
            }
            
            .success-title {
                color: #2E7D32;
                font-size: 32px;
                font-weight: 700;
                margin-bottom: 15px;
            }
            
            .success-message {
                color: #555;
                font-size: 18px;
                margin-bottom: 30px;
                font-weight: 500;
            }
            
            .order-details {
                background-color: #f8f9fa;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 30px;
                text-align: left;
            }
            
            .detail-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            
            .detail-item:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            
            .detail-label {
                color: #777;
                font-weight: 600;
            }
            
            .detail-value {
                color: #333;
                font-weight: 600;
            }
            
            .success-actions {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 20px;
            }
            
            .action-btn {
                padding: 12px 25px;
                font-weight: 600;
                border-radius: 50px;
                transition: all 0.3s ease;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #4CAF50, #2E7D32);
                border: none;
                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            }
            
            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
                background: linear-gradient(135deg, #5CBF60, #3E8D42);
            }
            
            .btn-outline-primary {
                color: #2E7D32;
                border: 2px solid #2E7D32;
                background: transparent;
            }
            
            .btn-outline-primary:hover {
                background-color: rgba(46, 125, 50, 0.1);
                color: #2E7D32;
                transform: translateY(-3px);
            }
            
            @media (max-width: 768px) {
                .success-actions {
                    flex-direction: column;
                }
                
                .action-btn {
                    width: 100%;
                    margin-bottom: 10px;
                }
            }
        </style>
        <?php
    }
?>
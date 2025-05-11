<?php 
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        
        $sql = "SELECT * FROM tblorder WHERE idorder=$id";
        
        $row = $db->getITEM($sql);

        // Get order details for the receipt
        $sqlDetail = "SELECT * FROM vorderdetail WHERE idorder=$id";
        $orderDetails = $db->getALL($sqlDetail);
    }
?>

<div class="payment-container">
    <div class="header-section">
        <div class="d-flex align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-cash-register mr-2"></i> Pembayaran Order
                </h3>
                <p class="section-subtitle">Proses pembayaran untuk order #<?php echo $id; ?></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card payment-card">
                <div class="card-header payment-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt mr-2"></i> Detail Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <form action="" method="post" id="paymentForm">
                        <div class="payment-summary">
                            <div class="summary-item">
                                <span class="summary-label">Order ID</span>
                                <span class="summary-value">#<?php echo $id; ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Tanggal</span>
                                <span class="summary-value"><?php echo date('d M Y'); ?></span>
                            </div>
                            <div class="summary-item total-amount">
                                <span class="summary-label">Total Pembayaran</span>
                                <span class="summary-value">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></span>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="total" class="form-label">
                                <i class="fas fa-shopping-cart mr-1"></i> Total Belanja
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="total" id="total" required value="<?php echo $row['total'] ?>" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="bayar" class="form-label">
                                <i class="fas fa-money-bill-wave mr-1"></i> Jumlah Bayar
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="bayar" id="bayar" required class="form-control" placeholder="Masukkan jumlah pembayaran" autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4" id="changeContainer" style="display: none;">
                            <label for="kembali" class="form-label">
                                <i class="fas fa-hand-holding-usd mr-1"></i> Kembalian
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" id="kembali" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <div class="payment-methods">
                            <h6 class="payment-methods-title">Metode Pembayaran</h6>
                            <div class="payment-method-options">
                                <div class="payment-method-option active">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Cash</span>
                                </div>
                                <div class="payment-method-option disabled">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Debit</span>
                                </div>
                                <div class="payment-method-option disabled">
                                    <i class="fas fa-qrcode"></i>
                                    <span>QRIS</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="simpan" class="btn btn-primary submit-btn">
                                <i class="fas fa-check-circle mr-2"></i> Proses Pembayaran
                            </button>
                            <a href="?f=order&m=select" class="btn btn-outline-secondary cancel-btn">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card receipt-card">
                <div class="card-header receipt-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice mr-2"></i> Struk Pembayaran
                    </h5>
                </div>
                <div class="card-body receipt-body">
                    <div class="receipt-header">
                        <div class="receipt-logo">
                            <i class="fas fa-utensils"></i>
                            <h4>WARUNG ALDO</h4>
                        </div>
                        <div class="receipt-info">
                            <p>Jl. Contoh No. 123</p>
                            <p>Telp: 021-1234567</p>
                        </div>
                    </div>
                    
                    <div class="receipt-divider"></div>
                    
                    <div class="receipt-order-info">
                        <div class="receipt-order-item">
                            <span>Order ID:</span>
                            <span>#<?php echo $id; ?></span>
                        </div>
                        <div class="receipt-order-item">
                            <span>Tanggal:</span>
                            <span><?php echo date('d/m/Y'); ?></span>
                        </div>
                        <div class="receipt-order-item">
                            <span>Waktu:</span>
                            <span><?php echo date('H:i'); ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-divider"></div>
                    
                    <div class="receipt-items">
                        <?php if(!empty($orderDetails)): ?>
                            <?php foreach($orderDetails as $detail): ?>
                                <div class="receipt-item">
                                    <div class="receipt-item-name">
                                        <span><?php echo $detail['menu']; ?></span>
                                        <span>x<?php echo $detail['jumlah']; ?></span>
                                    </div>
                                    <div class="receipt-item-price">
                                        Rp <?php echo number_format($detail['harga'] * $detail['jumlah'], 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="receipt-item">
                                <div class="receipt-item-name">
                                    <span>Item</span>
                                    <span>x1</span>
                                </div>
                                <div class="receipt-item-price">
                                    Rp <?php echo number_format($row['total'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="receipt-divider"></div>
                    
                    <div class="receipt-total">
                        <div class="receipt-total-item">
                            <span>Subtotal:</span>
                            <span>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="receipt-total-item">
                            <span>Pajak:</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="receipt-total-item grand-total">
                            <span>Total:</span>
                            <span>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-divider"></div>
                    
                    <div class="receipt-payment" id="receiptPayment" style="display: none;">
                        <div class="receipt-payment-item">
                            <span>Bayar:</span>
                            <span id="receiptBayar">Rp 0</span>
                        </div>
                        <div class="receipt-payment-item">
                            <span>Kembali:</span>
                            <span id="receiptKembali">Rp 0</span>
                        </div>
                    </div>
                    
                    <div class="receipt-footer">
                        <p>Terima kasih atas kunjungan Anda!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
        if (isset($_POST['simpan'])) {
            $bayar = $_POST['bayar'];
            $kembali = $bayar - $row['total'];

            if ($kembali < 0) {
                echo '<div class="alert alert-danger mt-4" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i> Pembayaran Kurang! Jumlah bayar harus lebih besar atau sama dengan total belanja.
                      </div>';
            } else {
                $sql = "UPDATE tblorder SET bayar='$bayar', kembali = $kembali, status=1 WHERE idorder=$id";
                $db->runSQL($sql);
                
                echo '<div class="alert alert-success mt-4" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> Pembayaran berhasil! Kembalian: Rp '.number_format($kembali, 0, ',', '.').'
                      </div>';
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "?f=order&m=select";
                        }, 2000);
                      </script>';
            }
        }
    ?>
</div>

<style>
    .payment-container {
        padding: 15px;
    }
    
    .header-section {
        margin-bottom: 25px;
    }
    
    .section-title {
        color: var(--secondary);
        font-weight: 600;
        border-left: 4px solid var(--primary);
        padding-left: 12px;
        margin-bottom: 5px;
    }
    
    .section-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .payment-card, .receipt-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: white;
        margin-bottom: 20px;
        height: 100%;
    }
    
    .payment-card-header, .receipt-card-header {
        background-color: var(--secondary);
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 15px 20px;
    }
    
    .payment-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .summary-item:last-child {
        margin-bottom: 0;
    }
    
    .summary-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .summary-value {
        font-weight: 500;
    }
    
    .total-amount {
        border-top: 1px dashed #dee2e6;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    .total-amount .summary-label {
        font-weight: 500;
        color: var(--secondary);
    }
    
    .total-amount .summary-value {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--secondary);
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .input-group-text {
        background-color: var(--secondary);
        color: white;
        border: none;
        border-radius: 6px 0 0 6px;
    }
    
    .payment-methods {
        margin-top: 25px;
        margin-bottom: 25px;
    }
    
    .payment-methods-title {
        font-weight: 500;
        color: var(--secondary);
        margin-bottom: 15px;
    }
    
    .payment-method-options {
        display: flex;
        gap: 15px;
    }
    
    .payment-method-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .payment-method-option i {
        font-size: 1.5rem;
        margin-bottom: 5px;
        color: #6c757d;
    }
    
    .payment-method-option span {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .payment-method-option.active {
        border-color: var(--primary);
        background-color: rgba(255, 107, 53, 0.1);
    }
    
    .payment-method-option.active i,
    .payment-method-option.active span {
        color: var(--primary);
    }
    
    .payment-method-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    
    .submit-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 10px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
        flex: 1;
    }
    
    .submit-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .cancel-btn {
        padding: 10px 25px;
        font-weight: 500;
    }
    
    /* Receipt Styling */
    .receipt-body {
        padding: 20px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }
    
    .receipt-header {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .receipt-logo {
        margin-bottom: 10px;
    }
    
    .receipt-logo i {
        font-size: 2rem;
        color: var(--primary);
    }
    
    .receipt-logo h4 {
        margin: 5px 0;
        font-weight: 700;
    }
    
    .receipt-info p {
        margin: 0;
        font-size: 0.8rem;
    }
    
    .receipt-divider {
        border-top: 1px dashed #dee2e6;
        margin: 15px 0;
    }
    
    .receipt-order-info {
        margin-bottom: 15px;
    }
    
    .receipt-order-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.8rem;
    }
    
    .receipt-items {
        margin-bottom: 15px;
    }
    
    .receipt-item {
        margin-bottom: 10px;
    }
    
    .receipt-item-name {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
    }
    
    .receipt-item-price {
        text-align: right;
        font-weight: 500;
    }
    
    .receipt-total {
        margin-bottom: 15px;
    }
    
    .receipt-total-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .grand-total {
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .receipt-payment-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .receipt-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 0.8rem;
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        
        .submit-btn, .cancel-btn {
            width: 100%;
        }
        
        .payment-method-options {
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bayarInput = document.getElementById('bayar');
        const kembaliInput = document.getElementById('kembali');
        const changeContainer = document.getElementById('changeContainer');
        const receiptPayment = document.getElementById('receiptPayment');
        const receiptBayar = document.getElementById('receiptBayar');
        const receiptKembali = document.getElementById('receiptKembali');
        const totalAmount = <?php echo $row['total']; ?>;
        
        bayarInput.addEventListener('input', function() {
            const bayarValue = parseFloat(this.value) || 0;
            const kembaliValue = bayarValue - totalAmount;
            
            if (bayarValue > 0) {
                changeContainer.style.display = 'block';
                receiptPayment.style.display = 'block';
                
                if (kembaliValue >= 0) {
                    kembaliInput.value = kembaliValue;
                    kembaliInput.classList.remove('is-invalid');
                    kembaliInput.classList.add('is-valid');
                } else {
                    kembaliInput.value = 'Pembayaran Kurang';
                    kembaliInput.classList.remove('is-valid');
                    kembaliInput.classList.add('is-invalid');
                }
                
                // Update receipt
                receiptBayar.textContent = 'Rp ' + bayarValue.toLocaleString('id-ID');
                receiptKembali.textContent = kembaliValue >= 0 ? 
                    'Rp ' + kembaliValue.toLocaleString('id-ID') : 
                    'Pembayaran Kurang';
            } else {
                changeContainer.style.display = 'none';
                receiptPayment.style.display = 'none';
            }
        });
        
        // Payment method selection
        const paymentOptions = document.querySelectorAll('.payment-method-option:not(.disabled)');
        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                paymentOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>

<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || !in_array($_SESSION['level'], ['admin', 'kasir'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Upload Transaksi - PD. EKOLAN</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<style>
body { background-color:#f1f6f9; font-family:'Segoe UI',sans-serif; }
.content{ margin-left:260px; padding:30px; }
.btn-toggle { flex:1; padding:12px; font-weight:600; border-radius:10px; transition:all 0.3s ease; }
.btn-toggle.active { transform:scale(1.05); }
</style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card p-4 mb-4 shadow-sm border-0">
                <h4 class="mb-4 text-primary"><i class="bi bi-upload"></i> Upload Transaksi Baru</h4>

                <div class="d-flex mb-4">
                    <button type="button" id="btnPemasukan" class="btn btn-success btn-toggle active" onclick="showForm('pemasukan')">
                        <i class="bi bi-plus-circle me-1"></i> Pemasukan
                    </button>
                    <button type="button" id="btnPengeluaran" class="btn btn-danger btn-toggle ms-2" onclick="showForm('pengeluaran')">
                        <i class="bi bi-dash-circle me-1"></i> Pengeluaran
                    </button>
                </div>

                <!-- 🔹 FORM PEMASUKAN -->
                <form id="formPemasukan" action="proses_upload.php" method="POST">
                    <input type="hidden" name="jenis_transaksi" value="debet">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barang / Item</label>
                        <input type="text" class="form-control" id="inputProduk" name="keterangan" placeholder="Cari barang..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (pcs/unit/set)</label>
                        <input type="number" class="form-control" id="pcsPemasukan" name="pcs" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Harga (Rp)</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlahPemasukan" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle me-1"></i> Simpan Transaksi</button>
                </form>

                <!-- 🔹 FORM PENGELUARAN -->
                <form id="formPengeluaran" action="proses_upload.php" method="POST" style="display:none;">
                    <input type="hidden" name="jenis_transaksi" value="kredit">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="tanggal" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Akun</label>
                        <select class="form-select" name="id_akun" id="selectAkunPengeluaran" required>
                            <option value="">-- Pilih Akun --</option>
                            <?php
                            $akun_q = mysqli_query($koneksi, "SELECT * FROM akun ORDER BY nama_akun ASC");
                            while ($ak = mysqli_fetch_assoc($akun_q)) {
                                echo "<option value='{$ak['id_akun']}'>{$ak['nama_akun']} ({$ak['jenis_akun']})</option>";
                            }
                            ?>
                            <option value="new">+ Tambah Akun Baru</option>
                        </select>
                    </div>

                    <!-- Tambah akun baru -->
                    <div id="akunBaruPengeluaran" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Nama Akun Baru</label>
                            <input type="text" class="form-control" name="nama_akun_baru" placeholder="Contoh: Beban Transportasi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Akun</label>
                            <select class="form-select" name="jenis_akun_baru">
                                <option value="">-- Pilih Jenis Akun --</option>
                                <option value="Aktiva">Aktiva</option>
                                <option value="Pasiva">Pasiva</option>
                                <option value="Modal">Modal</option>
                                <option value="Pendapatan">Pendapatan</option>
                                <option value="Beban">Beban</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Pengeluaran</label>
                        <select class="form-select" name="jenis_pengeluaran" id="jenisPengeluaran" onchange="togglePengeluaranForm()">
                            <option value="lainnya">Lainnya</option>
                            <option value="pembelian">Pembelian Barang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" required>
                    </div>
                    <div id="formPembelian" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Jumlah Barang (pcs/unit/set)</label>
                            <input type="number" class="form-control" name="pcs" min="1" value="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Harga (Rp)</label>
                        <input type="number" class="form-control" name="jumlah" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle me-1"></i> Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showForm(jenis) {
    document.getElementById("btnPemasukan").classList.remove("active");
    document.getElementById("btnPengeluaran").classList.remove("active");
    document.getElementById("formPemasukan").style.display = "none";
    document.getElementById("formPengeluaran").style.display = "none";
    if (jenis === "pemasukan") {
        document.getElementById("btnPemasukan").classList.add("active");
        document.getElementById("formPemasukan").style.display = "block";
    } else {
        document.getElementById("btnPengeluaran").classList.add("active");
        document.getElementById("formPengeluaran").style.display = "block";
    }
}
function togglePengeluaranForm() {
    const jenis = document.getElementById("jenisPengeluaran").value;
    document.getElementById("formPembelian").style.display = (jenis === "pembelian") ? "block" : "none";
}
document.getElementById("selectAkunPengeluaran").addEventListener("change", function() {
    document.getElementById("akunBaruPengeluaran").style.display = (this.value === "new") ? "block" : "none";
});

// Autocomplete Barang
let hargaSatuan = 0;
$("#inputProduk").autocomplete({
    source: "search_barang.php",
    minLength: 1,
    select: function(event, ui) {
        $("#inputProduk").val(ui.item.value);
        hargaSatuan = ui.item.harga;
        hitungTotal();
        return false;
    }
});
$("#pcsPemasukan").on("input", function() { hitungTotal(); });
function hitungTotal() {
    let pcs = parseInt($("#pcsPemasukan").val()) || 0;
    $("#jumlahPemasukan").val(hargaSatuan * pcs);
}
</script>
</body>
</html>

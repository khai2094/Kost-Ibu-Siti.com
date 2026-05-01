# 🏠 Sistem Manajemen Kos Bu Siti

## 📌 Deskripsi Proyek

Sistem Manajemen Kos Bu Siti merupakan aplikasi berbasis web yang dikembangkan untuk mendukung proses pengelolaan operasional kos secara terintegrasi dan efisien.  

Aplikasi ini dirancang untuk membantu pemilik kos dalam mengelola berbagai data penting, seperti data kamar, penghuni, kontrak sewa, hingga pembayaran, sehingga seluruh informasi dapat tersimpan dengan rapi dalam satu sistem terpusat.

Dengan adanya sistem ini, proses administrasi yang sebelumnya dilakukan secara manual dapat diminimalkan, sehingga meningkatkan akurasi, efisiensi, dan kemudahan dalam monitoring kondisi kos secara keseluruhan.

---

## 🎯 Tujuan Pengembangan

- Meningkatkan efisiensi dalam pengelolaan kos  
- Mengurangi kesalahan akibat pencatatan manual  
- Mempermudah proses monitoring kamar dan penghuni  
- Menyediakan sistem penyimpanan data yang terstruktur dan terintegrasi  

---

## ⚙️ Fitur Utama

### 🛏️ Manajemen Kamar
- Menyediakan fungsi Create, Read, Update, Delete (CRUD) untuk data kamar  
- Validasi nomor kamar agar bersifat unik  
- Pilihan harga kamar:
  - Rp1.000.000  
  - Rp1.800.000  
  - Harga kustom  
- Status kamar:
  - Tersedia  
  - Terisi  
- Fitur pencarian dan filter data kamar  
- Tampilan tabel dengan pagination  

---

### 👤 Manajemen Penghuni
- Penyimpanan data penghuni secara terstruktur  
- Terintegrasi dengan data kamar dan kontrak  

---

### 📄 Manajemen Kontrak
- Pengelolaan masa sewa penghuni  
- Relasi antara penghuni dan kamar  

---

### 💰 Manajemen Pembayaran
- Pencatatan transaksi pembayaran  
- Monitoring status pembayaran  

---

### 🛠️ Fasilitas dan Keluhan
- Pengelolaan data fasilitas kos  
- Fitur pengajuan dan pencatatan keluhan dari penghuni  

---

### 🔐 Sistem Pengguna
- Autentikasi login pengguna  
- Tampilan navbar dengan sapaan pengguna  
- Menu navigasi (hamburger menu) yang mencakup:
  - Ganti password  
  - Logout  

---

### 🎨 Antarmuka Pengguna
- Desain responsif (responsive design)  
- Konfirmasi sebelum penghapusan data  
- Visualisasi status dengan indikator warna  

---

## 🗂️ Struktur Basis Data

Sistem ini menggunakan beberapa tabel utama yang saling terintegrasi, antara lain:

- penghuni  
- kamar  
- kontrak  
- pembayaran  
- fasilitas  
- keluhan  
- pengunjung  

Relasi antar tabel dirancang untuk menjaga konsistensi dan integritas data.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP  
- **Frontend**: HTML, CSS, JavaScript  
- **Database**: MySQL  
- **Tools Pengembangan**:
  - XAMPP  
  - Visual Studio Code  

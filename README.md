# Kost-Ibu-Siti.com
# 🏠 Sistem Manajemen Kos Bu Siti

## 📌 Tentang Proyek

Project ini adalah aplikasi web yang aku buat untuk membantu pengelolaan kos “Bu Siti”.

Tujuannya biar semua data—mulai dari kamar, penghuni, sampai pembayaran—bisa dikelola dengan lebih rapi, cepat, dan nggak ribet.

Dengan sistem ini, pemilik kos bisa lebih mudah ngecek kondisi kos secara keseluruhan tanpa harus catat manual lagi.

---

## 🎯 Tujuan

- Biar pengelolaan kos jadi lebih praktis  
- Mengurangi kesalahan pencatatan manual  
- Mempermudah monitoring kamar & penghuni  
- Semua data tersimpan dalam satu sistem  

---

## ⚙️ Fitur Utama

### 🛏️ Manajemen Kamar

- Tambah, edit, hapus kamar (CRUD)
- Nomor kamar harus unik
- Pilihan harga:
  - Rp1.000.000  
  - Rp1.800.000  
  - Custom  
- Status kamar:
  - Tersedia  
  - Terisi  
- Ada fitur **search & filter**
- Tabel sudah pakai **pagination**

---

### 👤 Manajemen Penghuni

- Menyimpan data penghuni kos  
- Terhubung langsung dengan kamar & kontrak  

---

### 📄 Manajemen Kontrak

- Mengatur masa sewa penghuni  
- Relasi antara penghuni dan kamar  

---

### 💰 Pembayaran

- Mencatat pembayaran  
- Status pembayaran bisa dipantau  

---

### 🛠️ Fasilitas & Keluhan

- Data fasilitas kos  
- Penghuni bisa mengajukan keluhan  

---

### 🔐 Sistem User

- Login user  
- Navbar ada sapaan: **“Halo, username”**  
- Menu hamburger:
  - Ganti Password  
  - Logout  

---

### 🎨 Tampilan

- Sudah responsif  
- Ada konfirmasi saat hapus data  
- Status ditampilkan dengan warna biar lebih jelas  

---

## 🗂️ Struktur Database

Project ini pakai beberapa tabel utama:

- penghuni  
- kamar  
- kontrak  
- pembayaran  
- fasilitas  
- keluhan  
- pengunjung  

Semua tabel saling terhubung supaya data tetap konsisten.

---

## 🛠️ Teknologi yang Dipakai

- **Backend**: PHP  
- **Frontend**: HTML, CSS, JavaScript  
- **Database**: MySQL  
- **Tools**:
  - XAMPP  
  - Visual Studio Code  

---

## 🚀 Cara Menjalankan

1. Clone repo:
   ```bash
   git clone https://github.com/username/nama-repo.git

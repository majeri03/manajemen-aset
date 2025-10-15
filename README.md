# Alur Kerja Git (Git Workflow)

Panduan ini bertujuan untuk menstandarkan cara kita menggunakan Git dalam proyek **Manajemen Aset**. Mengikuti alur ini akan membantu menjaga histori repository tetap bersih, meminimalkan konflik, dan memudahkan pelacakan perubahan.

## Prinsip Utama

1.  **`main` adalah Sumber Kebenaran**: Branch `main` harus selalu dalam keadaan stabil dan siap untuk di-deploy. Tidak ada yang boleh melakukan `push` langsung ke `main`.
2.  **Bekerja di Feature Branch**: Setiap tugas, baik itu fitur baru, perbaikan bug, atau penyesuaian kecil, harus dikerjakan di dalam *branch* terpisah.
3.  **Selalu Update Sebelum Mulai**: Sebelum membuat branch baru atau melanjutkan pekerjaan, pastikan branch `main` lokal Anda sudah sinkron dengan server (`origin`).
4.  **Commit Kecil dan Sering**: Simpan pekerjaan Anda secara berkala dengan `commit`. Gunakan pesan yang jelas agar mudah dipahami.
5.  **Jaga Kebersihan Histori**: Gunakan `pull --rebase` untuk menjaga histori commit tetap lurus dan mudah dibaca.

---

##  Workflow Lengkap

Ada dua skenario utama saat memulai pekerjaan: membuat fitur baru atau melanjutkan pekerjaan yang sudah ada.

### ✅ Skenario 1: Memulai Tugas/Fitur Baru

Lakukan ini setiap kali Anda akan mengerjakan tugas baru yang belum pernah dimulai.

**Tujuan**: Membuat branch baru yang dasarnya adalah versi terbaru dari `main`.

**Langkah-langkah:**

1.  **Pindah ke Branch `main`**
    Pastikan Anda berada di branch `main` sebelum mengambil data terbaru.
    ```bash
    git checkout main
    ```

2.  **Update Branch `main` Lokal**
    Tarik semua perubahan terbaru dari repository pusat (origin) ke `main` lokal Anda.
    ```bash
    git pull origin main
    ```

3.  **Buat Branch Baru**
    Buat branch baru untuk tugas Anda dan langsung pindah ke branch tersebut. Gunakan format penamaan yang deskriptif.

    **Format Penamaan Branch:**
    * **Fitur Baru**: `fitur/<deskripsi-singkat>` (contoh: `fitur/form-perbaikan-aset`)
    * **Perbaikan Bug**: `fix/<deskripsi-singkat>` (contoh: `fix/validasi-tanggal-mundur`)
    * **Tugas Lain**: `chore/<deskripsi-singkat>` (contoh: `chore/update-readme-workflow`)

    ```bash
    # Ganti <nama-branch-baru> dengan nama branch Anda
    git checkout -b <nama-branch-baru>

    # Contoh:
    git checkout -b fitur/tampilan-detail-stock-opname
    ```
    Sekarang Anda siap untuk mulai *coding* di branch baru ini.

### 🔁 Skenario 2: Melanjutkan Pekerjaan di Branch Lama

Lakukan ini jika Anda ingin melanjutkan pekerjaan di branch yang sudah pernah Anda buat sebelumnya.

**Tujuan**: Mengupdate branch kerja Anda dengan kode terbaru dari `main` sebelum melanjutkan.

**Langkah-langkah:**

1.  **Update Branch `main` Lokal**
    Sama seperti skenario 1, pastikan `main` Anda adalah yang terbaru.
    ```bash
    git checkout main
    git pull origin main
    ```

2.  **Pindah ke Branch Kerja Anda**
    Pindah kembali ke branch tempat Anda mengerjakan fitur sebelumnya.
    ```bash
    # Ganti <nama-branch-lama> dengan branch Anda
    git checkout <nama-branch-lama>

    # Contoh:
    git checkout dashboard/tampilan
    ```

3.  **Gabungkan Perubahan dari `main`**
    Terapkan semua update dari `main` ke branch Anda saat ini. Ini penting untuk memastikan kode Anda kompatibel dengan pekerjaan orang lain.
    ```bash
    git merge main
    ```
    > **Catatan**: Jika terjadi *merge conflict*, Git akan memberitahu Anda. Buka file yang konflik, selesaikan perbedaannya, lalu `add` dan `commit` untuk menyelesaikan proses merge.

### 📤 Menyimpan dan Mengunggah Progress

Lakukan ini secara berkala setelah Anda menyelesaikan satu unit pekerjaan.

**Langkah-langkah:**

1.  **Tambahkan File ke Staging Area**
    Pilih file mana yang ingin Anda simpan. Untuk menambahkan semua perubahan, gunakan `.`.
    ```bash
    git add .
    ```
    *Tips: Untuk menambahkan file hanya dari satu direktori, gunakan `git add app/Controllers/`.*

2.  **Simpan Perubahan (Commit)**
    Beri pesan yang jelas tentang apa yang Anda kerjakan.

    **Format Pesan Commit:**
    Gunakan prefix untuk menjelaskan jenis perubahan:
    * `feat:` (fitur baru)
    * `fix:` (perbaikan bug)
    * `docs:` (perubahan dokumentasi)
    * `style:` (perubahan format, spasi, dll.)
    * `refactor:` (perubahan kode yang tidak mengubah fungsionalitas)
    * `chore:` (tugas lain seperti update library)

    ```bash
    # Ganti <Pesan commit> dengan deskripsi Anda
    git commit -m "<Tipe>: <Pesan commit yang jelas>"

    # Contoh:
    git commit -m "feat: add PDF generation for asset repair requests"
    ```

3.  **Unggah ke Server (Push)**
    Kirim semua commit Anda ke repository pusat.
    ```bash
    # Ganti <nama-branch-aktif> dengan nama branch Anda saat ini
    git push origin <nama-branch-aktif>

    # Contoh:
    git push origin fitur/form-perbaikan-aset
    ```

---

## Contoh Alur Kerja Lengkap (Studi Kasus)

**Tugas**: Membuat fitur filter laporan berdasarkan rentang tanggal.

1.  **Memulai Tugas (Skenario 1)**
    ```bash
    git checkout main
    git pull origin main
    git checkout -b fitur/filter-laporan-tanggal
    ```

2.  **Coding...**
    * Mengubah file `app/Controllers/LaporanController.php`.
    * Mengubah file `app/Views/laporan/index.php`.

3.  **Menyimpan Progress Pertama**
    ```bash
    git add app/Controllers/LaporanController.php app/Views/laporan/index.php
    git commit -m "feat: add date filter inputs on report view"
    ```

4.  **Coding Lagi...**
    * Menyelesaikan logika filter di controller.

5.  **Menyimpan Progress Kedua dan Mengunggah**
    ```bash
    git add app/Controllers/LaporanController.php
    git commit -m "feat: implement date range logic in LaporanController"
    git push origin fitur/filter-laporan-tanggal
    ```
    Pekerjaan Anda sekarang sudah aman di server.

---

## Konfigurasi Opsional (Sangat Direkomendasikan)

Jalankan perintah ini sekali untuk membuat alur kerja lebih mulus dan menghindari histori yang berantakan.

```bash
# Otomatis menggunakan 'rebase' saat 'pull', membuat histori lurus
git config --global pull.rebase true

# Otomatis menyimpan perubahan lokal sementara saat 'rebase'
git config --global rebase.autoStash true

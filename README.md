# 🧭 Panduan Alur Kerja Git (Git Workflow)

## 🎯 Prinsip Utama

| No | Prinsip | Penjelasan |
|----|----------|------------|
| 1 | **`main` adalah sumber kebenaran** | Branch `main` selalu stabil dan siap deploy. Jangan `push` langsung ke `main`. |
| 2 | **Gunakan Feature Branch** | Semua pekerjaan (fitur, bug, dokumentasi) dikerjakan di branch terpisah. |
| 3 | **Selalu update sebelum mulai** | Pastikan `main` lokal sudah sinkron dengan `origin/main`. |
| 4 | **Commit kecil & sering** | Simpan pekerjaan per bagian dengan pesan jelas. |
| 5 | **Histori bersih** | Gunakan `git pull --rebase` agar riwayat commit tetap lurus. |

---

## 🚀 Skenario 1 – Membuat Fitur/Tugas Baru

| Langkah | Perintah Git | Keterangan |
|----------|---------------|------------|
| 1 | `git checkout main` | Pindah ke branch utama |
| 2 | `git pull origin main` | Update versi terbaru dari server |
| 3 | `git checkout -b fitur/<nama-fitur>` | Buat branch baru untuk fitur |

📌 **Format Nama Branch:**
| Jenis | Format | Contoh |
|-------|--------|--------|
| Fitur Baru | `fitur/<deskripsi>` | `fitur/form-perbaikan-aset` |
| Perbaikan Bug | `fix/<deskripsi>` | `fix/validasi-tanggal` |
| Tugas Lain | `chore/<deskripsi>` | `chore/update-readme` |

---

## 🔁 Skenario 2 – Melanjutkan Branch Lama

| Langkah | Perintah Git | Keterangan |
|----------|---------------|------------|
| 1 | `git checkout main` | Pindah ke branch utama |
| 2 | `git pull origin main` | Update perubahan terbaru |
| 3 | `git checkout <nama-branch-lama>` | Pindah ke branch lama Anda |
| 4 | `git merge main` | Gabungkan update dari `main` |

> ⚠️ Jika ada konflik, selesaikan manual lalu `git add` dan `git commit` ulang.

---

## 💾 Menyimpan dan Mengunggah Perubahan

| Langkah | Perintah Git | Keterangan |
|----------|---------------|------------|
| 1 | `git add .` | Tambahkan semua perubahan ke staging |
| 2 | `git commit -m "<tipe>: <pesan>"` | Simpan perubahan dengan pesan jelas |
| 3 | `git push origin <nama-branch>` | Kirim branch ke server GitHub |

📌 **Format Pesan Commit:**
| Tipe | Keterangan | Contoh |
|------|-------------|--------|
| `feat:` | Fitur baru | `feat: add PDF generation for asset report` |
| `fix:` | Perbaikan bug | `fix: date validation logic` |
| `docs:` | Dokumentasi | `docs: update README for git workflow` |
| `style:` | Perubahan tampilan/kode minor | `style: format controller code` |
| `refactor:` | Ubah struktur tanpa ubah fungsi | `refactor: simplify asset fetch logic` |
| `chore:` | Tugas umum/non-code | `chore: update dependencies` |

---

## 🧩 Contoh Kasus Lengkap

**Tugas:** Membuat fitur filter laporan berdasarkan rentang tanggal.

| Tahap | Perintah | Penjelasan |
|--------|-----------|------------|
| 1 | `git checkout main`<br>`git pull origin main`<br>`git checkout -b fitur/filter-laporan-tanggal` | Buat branch baru |
| 2 | _(Coding…)_ | Ubah `LaporanController.php` & `laporan/index.php` |
| 3 | `git add .`<br>`git commit -m "feat: add date filter inputs"` | Simpan perubahan pertama |
| 4 | _(Coding lagi…)_ | Selesaikan logika filter |
| 5 | `git add .`<br>`git commit -m "feat: implement date range filter"`<br>`git push origin fitur/filter-laporan-tanggal` | Upload ke server |

---

## ⚙️ Konfigurasi Tambahan (Opsional tapi Disarankan)

```bash
git config --global pull.rebase true
git config --global rebase.autoStash true
```
💡 Ini akan menjaga histori commit tetap **rata (tanpa merge bubble)** dan otomatis menyimpan perubahan lokal saat melakukan `pull`.

---

## 🧱 Diagram Ringkas Alur Kerja

```
       ┌────────────────┐
       │   MAIN (Stabil)│
       └──────┬─────────┘
              │
       git pull origin main
              │
       git checkout -b fitur/nama-fitur
              │
        🧑‍💻 Coding & Commit
              │
       git push origin fitur/nama-fitur
              │
       🔁 Merge ke MAIN via Pull Request
```

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\LokasiModel;

class HistoryController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $asetModel = new AsetModel();

        $history = [];

        // 1. HISTORY INPUT
        $inputs = $asetModel->select('kode, created_at, user_pengguna')->findAll();
        foreach ($inputs as $item) {
            $history[] = [
                'date'  => $item['created_at'],
                'type'  => 'input', // Satu tipe saja
                'title' => 'Input Aset Baru',
                'desc'  => "Aset <strong>{$item['kode']}</strong> telah ditambahkan ke sistem.",
                'user'  => 'Admin/Staff', 
                'icon'  => 'bi-plus-circle-fill', // Satu ikon seragam
                'color' => 'text-primary'
            ];
        }

        // 2. HISTORY PEMUSNAHAN
        $deleted = $asetModel->onlyDeleted()->select('kode, deleted_at')->findAll();
        foreach ($deleted as $item) {
            $history[] = [
                'date' => $item['deleted_at'],
                'type' => 'pemusnahan',
                'title' => 'Aset Dimusnahkan',
                'desc' => "Aset <strong>{$item['kode']}</strong> telah dimusnahkan (Write-off).",
                'user' => 'Admin',
                'icon' => 'bi-trash-fill',
                'color' => 'text-danger'
            ];
        }

        // 3. HISTORY PENJUALAN
        $sales = $asetModel->where('status', 'Penjualan')
                           ->select('kode, updated_at, harga_penjualan')
                           ->findAll();
        foreach ($sales as $item) {
            $harga = number_format($item['harga_penjualan'] ?? 0, 0, ',', '.');
            $history[] = [
                'date' => $item['updated_at'],
                'type' => 'penjualan',
                'title' => 'Aset Dijual',
                'desc' => "Aset <strong>{$item['kode']}</strong> terjual seharga Rp {$harga}.",
                'user' => 'Admin',
                'icon' => 'bi-currency-dollar',
                'color' => 'text-success'
            ];
        }

        // 4. HISTORY MUTASI
        $mutations = $db->table('aset_update_requests as aur')
            ->select('a.kode, aur.created_at, u.full_name, aur.status')
            ->join('aset as a', 'a.id = aur.aset_id')
            ->join('users as u', 'u.id = aur.user_id')
            ->where('aur.status', 'approved')
            ->get()->getResultArray();

        foreach ($mutations as $item) {
            $history[] = [
                'date' => $item['created_at'],
                'type' => 'mutasi',
                'title' => 'Mutasi Aset',
                'desc' => "Perubahan data aset <strong>{$item['kode']}</strong> telah disetujui.",
                'user' => $item['full_name'],
                'icon' => 'bi-pencil-square',
                'color' => 'text-warning'
            ];
        }

        // 5. HISTORY STOCK OPNAME
        $lastSO = $db->query("
            SELECT l.nama_lokasi, MAX(soh.opname_at) as last_so, u.full_name
            FROM stock_opname_history soh
            JOIN aset a ON a.id = soh.aset_id
            JOIN lokasi l ON l.id = a.lokasi_id
            JOIN users u ON u.id = soh.user_id
            GROUP BY l.id, l.nama_lokasi, u.full_name
            ORDER BY last_so DESC
        ")->getResultArray();

        foreach ($lastSO as $item) {
            $history[] = [
                'date' => $item['last_so'],
                'type' => 'stock_opname',
                'title' => 'Stock Opname Lokasi',
                'desc' => "Pengecekan aset di lokasi <strong>{$item['nama_lokasi']}</strong>.",
                'user' => $item['full_name'],
                'icon' => 'bi-qr-code-scan',
                'color' => 'text-info'
            ];
        }

        // SORTING TERBARU
        usort($history, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $data = [
            'title' => 'Riwayat Aktivitas Aset',
            'history' => $history
        ];

        return view('history/index', $data);
    }
}
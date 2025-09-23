<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;

class TrackingController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $asetModel = new AsetModel();

        // 1. Ambil riwayat UPDATE
        $updatesQuery = $db->table('aset_update_requests as aur')
            ->select("a.kode as kode_aset, aur.created_at as tanggal, u.full_name as nama_user, aur.proposed_data, 'update' as tipe_aktivitas")
            ->join('aset as a', 'a.id = aur.aset_id')
            ->join('users as u', 'u.id = aur.user_id')
            ->where('aur.status', 'approved');

        // 2. Ambil riwayat CREATE
        $createsQuery = $db->table('aset as a')
            ->select("a.kode as kode_aset, a.created_at as tanggal, 'Sistem' as nama_user, CONCAT('Aset baru dengan kategori ', a.kategori) as proposed_data, 'create' as tipe_aktivitas")
            ->where('a.deleted_at IS NULL'); // Hanya aset yang belum dihapus

        // 3. Ambil riwayat DELETE (BARU)
        $deletesQuery = $db->table('aset as a')
            ->select("a.kode as kode_aset, a.deleted_at as tanggal, 'Sistem' as nama_user, 'Aset telah dihapus dari sistem' as proposed_data, 'delete' as tipe_aktivitas")
            ->where('a.deleted_at IS NOT NULL'); // Hanya aset yang sudah ditandai terhapus

        // Gabungkan semua query
        $activities = $updatesQuery->union($createsQuery)->union($deletesQuery)->orderBy('tanggal', 'DESC')->get()->getResultArray();
        
        // Statistik Cepat
        $aktivitasHariIni = array_filter($activities, function($item) {
            return date('Y-m-d', strtotime($item['tanggal'])) === date('Y-m-d');
        });
        $asetBaruBulanIni = $db->table('aset')->where('MONTH(created_at)', date('m'))->where('YEAR(created_at)', date('Y'))->countAllResults();

        $data = [
            'title'             => 'Asset Command Center',
            'activities'        => $activities,
            'aktivitasHariIni'  => count($aktivitasHariIni),
            'asetBaruBulanIni'  => $asetBaruBulanIni
        ];

        return view('tracking/index', $data);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'user'  => session()->get('full_name') ?: 'Guest',

            // Opsional: tinggal aktifkan saat datanya sudah siap
            // 'stats' => [
            //     'total_value'  => 1250000000,
            //     'broken_count' => 15,
            //     'user_count'   => 42,
            // ],
            // 'filters' => [
            //     'categories' => ['Elektronik','Mebel','Kendaraan','Proyek'],
            // ],
            // 'latestAssets' => [
            //     ['kode'=>'LP-001','nama'=>'Macbook Pro M3','kategori'=>'Elektronik','lokasi'=>'Lantai 12 - Tim Dev','status'=>'Aktif'],
            // ],
            // 'charts' => [
            //     'kategori' => ['labels'=>['Elektronik','Mebel','Kendaraan'],'data'=>[45,18,9]],
            //     'lokasi'   => ['labels'=>['Dev','Meeting A','Gudang','HR','Workshop'],'data'=>[20,14,12,9,7]],
            // ],
        ];

        return view('dashboard/index', $data);
    }
}

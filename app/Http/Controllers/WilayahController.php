<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class WilayahController extends Controller
{
    public function semuaWilayah()
    {
        $token = Session::get('api_token');

        $response = Http::withToken($token)
            ->get('https://gisapis.manpits.xyz/api/mregion');

        $data = $response->json();

        $provinsi = $data['provinsi'] ?? [];
        $kabupaten = $data['kabupaten'] ?? [];
        $kecamatan = $data['kecamatan'] ?? [];
        $desa = $data['desa'] ?? [];

        // dd([
        //     'provinsi_1' => $provinsi[0] ?? 'Tidak ada',
        //     'kabupaten_1' => $kabupaten[0] ?? 'Tidak ada',
        //     'kecamatan_1' => $kecamatan[0] ?? 'Tidak ada',
        //     'desa_1' => $desa[0] ?? 'Tidak ada',
        // ]);

        return view('wilayah.semua', compact('provinsi', 'kabupaten', 'kecamatan', 'desa'));
    }
}


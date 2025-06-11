<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RuasJalanController extends Controller
{
    public function index()
    {
        $token = Session::get('api_token');

        // Cek apakah token valid
        if (!$token) {
            return redirect()->route('login')->withErrors(['Token API tidak ditemukan, silakan login ulang.']);
        }

        // Ambil data ruas jalan
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withToken($token)->get('https://gisapis.manpits.xyz/api/ruasjalan');

        $responseData = $response->json();
        $ruasjalan = $responseData['ruasjalan'] ?? [];

        // Ambil data referensi untuk eksisting, jenis jalan, dan kondisi jalan
        $eksistingResponse = Http::withHeaders(['Accept' => 'application/json'])
            ->withToken($token)
            ->get('https://gisapis.manpits.xyz/api/meksisting');
        $eksisting = $eksistingResponse->json()['eksisting'] ?? [];

        $jenisjalanResponse = Http::withHeaders(['Accept' => 'application/json'])
            ->withToken($token)
            ->get('https://gisapis.manpits.xyz/api/mjenisjalan');
        $jenisjalan = $jenisjalanResponse->json()['eksisting'] ?? [];

        $kondisiResponse = Http::withHeaders(['Accept' => 'application/json'])
            ->withToken($token)
            ->get('https://gisapis.manpits.xyz/api/mkondisi');
        $kondisi = $kondisiResponse->json()['eksisting'] ?? [];

        // Ubah menjadi associative array untuk lookup cepat berdasarkan ID
        $eksistingLookup = collect($eksisting)->pluck('eksisting', 'id')->toArray();
        $jenisjalanLookup = collect($jenisjalan)->pluck('jenisjalan', 'id')->toArray();
        $kondisiLookup = collect($kondisi)->pluck('kondisi', 'id')->toArray();

        // Gabungkan nama ke data ruas jalan
        foreach ($ruasjalan as &$jalan) {
            $jalan['eksisting_nama'] = $eksistingLookup[$jalan['eksisting_id']] ?? '-';
            $jalan['jenisjalan_nama'] = $jenisjalanLookup[$jalan['jenisjalan_id']] ?? '-';
            $jalan['kondisi_nama'] = $kondisiLookup[$jalan['kondisi_id']] ?? '-';
        }

        // Kirim data ke view
        return view('ruasjalan.index', compact('ruasjalan', 'eksisting','jenisjalan','kondisi'));
    }


    public function create()
    {
        $token = Session::get('api_token');

        // Cek dulu token valid atau tidak
        if (!$token) {
            return redirect()->route('login')->withErrors(['Token API tidak ditemukan.']);
        }

        // Ambil data wilayah
        $regionResponse = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mregion');
        $regionData = $regionResponse->json();

        $provinsi = $regionData['provinsi'] ?? [];
        $kabupaten = $regionData['kabupaten'] ?? [];
        $kecamatan = $regionData['kecamatan'] ?? [];
        $desa = $regionData['desa'] ?? [];

        // Ambil data eksisting, jenis jalan, kondisi
        $eksistingRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/meksisting');
        $jenisjalanRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mjenisjalan');
        $kondisiRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mkondisi');

        $eksistingData = $eksistingRes->json();
        $eksisting = $eksistingData['eksisting'] ?? [];

        $jenisjalanData = $jenisjalanRes->json();
        $jenisjalan = $jenisjalanData['eksisting'] ?? [];

        $kondisiData = $kondisiRes->json();
        $kondisi = $kondisiData['eksisting'] ?? [];

        // // Cek semua data sekaligus biar kamu bisa lihat
        // dd([
        //     'eksisting' => $eksisting,
        //     'jenisjalan' => $jenisjalan,
        //     'kondisi' => $kondisi,
        // ]);

        // Ruas jalan untuk ditampilkan di peta
        $ruasjalanRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/ruasjalan');
        $ruasjalan = $ruasjalanRes->json()['data'] ?? [];

        return view('ruasjalan.create', compact(
            'provinsi', 'kabupaten', 'kecamatan', 'desa',
            'eksisting', 'jenisjalan', 'kondisi', 'ruasjalan'
        ));
    }

    public function store(Request $request)
    {
        $token = Session::get('api_token');

        $data = [
            'desa_id' => $request->desa_id,
            'kode_ruas' => $request->kode_ruas,
            'nama_ruas' => $request->nama_ruas,
            'panjang' => $request->panjang,
            'lebar' => $request->lebar,
            'eksisting_id' => $request->eksisting_id,
            'kondisi_id' => $request->kondisi_id,
            'jenisjalan_id' => $request->jenisjalan_id,
            'keterangan' => $request->keterangan,
            'paths' => $request->paths, // path encoded dari leaflet draw
        ];

        $response = Http::withToken($token)->post('https://gisapis.manpits.xyz/api/ruasjalan', $data);

        if ($response->successful()) {
            return redirect()->route('ruasjalan.index')->with('success', 'Ruas jalan berhasil ditambahkan.');
        } else {
            return back()->withErrors(['msg' => 'Gagal menambahkan ruas jalan: ' . $response->body()]);
        }
    }

    public function destroy($id)
    {
        $token = Session::get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['Token API tidak ditemukan, silakan login ulang.']);
        }

        // Mengirim permintaan untuk menghapus ruas jalan berdasarkan ID
        $response = Http::withHeaders(['Accept' => 'application/json'])
                        ->withToken($token)
                        ->delete('https://gisapis.manpits.xyz/api/ruasjalan/' . $id);

        if ($response->successful()) {
            return redirect()->route('ruasjalan.index')->with('success', 'Ruas jalan berhasil dihapus.');
        } else {
            return back()->withErrors(['msg' => 'Gagal menghapus ruas jalan: ' . $response->body()]);
        }
    }

   public function edit($id)
    {
        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['Token API tidak ditemukan.']);
        }

        // Ambil data ruas jalan berdasarkan ID
        $response = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/ruasjalan/'.$id);
        $ruasjalan = $response->json()['ruasjalan'] ?? [];

        if (!$ruasjalan) {
            return redirect()->route('ruasjalan.index')->withErrors(['msg' => 'Data ruas jalan tidak ditemukan.']);
        }

        // Ambil data wilayah untuk dropdown
        $regionResponse = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mregion');
        $regionData = $regionResponse->json();
        $provinsi = $regionData['provinsi'] ?? [];
        $kabupaten = $regionData['kabupaten'] ?? [];
        $kecamatan = $regionData['kecamatan'] ?? [];
        $desa = $regionData['desa'] ?? [];

        // Ambil data eksisting, jenis jalan, kondisi
        $eksistingRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/meksisting');
        $eksisting = $eksistingRes->json()['eksisting'] ?? [];

        $jenisjalanRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mjenisjalan');
        $jenisjalan = $jenisjalanRes->json()['eksisting'] ?? [];

        $kondisiRes = Http::withToken($token)->get('https://gisapis.manpits.xyz/api/mkondisi');
        $kondisi = $kondisiRes->json()['eksisting'] ?? [];

        // dd($ruasjalan);

        // Ambil ID provinsi, kabupaten, dan kecamatan berdasarkan desa_id
        $desaId = $ruasjalan['desa_id'];
        $desaData = collect($desa)->firstWhere('id', $desaId);
        $kecId = $desaData['kec_id'] ?? null;
        $kabId = collect($kecamatan)->firstWhere('id', $kecId)['kab_id'] ?? null;
        $provId = collect($kabupaten)->firstWhere('id', $kabId)['prov_id'] ?? null;

        return view('ruasjalan.edit', compact(
            'ruasjalan', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'eksisting', 'jenisjalan', 'kondisi', 'provId', 'kabId', 'kecId'
        ));
    }

    public function update(Request $request, $id)
    {
        $token = Session::get('api_token');

        $data = [
            'desa_id' => $request->desa_id,
            'kode_ruas' => $request->kode_ruas,
            'nama_ruas' => $request->nama_ruas,
            'panjang' => $request->panjang,
            'lebar' => $request->lebar,
            'eksisting_id' => $request->eksisting_id,
            'kondisi_id' => $request->kondisi_id,
            'jenisjalan_id' => $request->jenisjalan_id,
            'keterangan' => $request->keterangan,
            'paths' => $request->paths, // path encoded dari leaflet draw
        ];

        $response = Http::withToken($token)->put('https://gisapis.manpits.xyz/api/ruasjalan/' . $id, $data);

        if ($response->successful()) {
            return redirect()->route('ruasjalan.index')->with('success', 'Ruas jalan berhasil diperbarui.');
        } else {
            return back()->withErrors(['msg' => 'Gagal memperbarui ruas jalan: ' . $response->body()]);
        }
    }

}


<x-layouts.app>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-4">Tambah Ruas Jalan</h1>

        <div class="flex flex-wrap -mx-2">
            <!-- FORM -->
            <div class="w-full md:w-1/2 px-2 mb-4">
                <form action="{{ route('ruasjalan.store') }}" method="POST">
                    @csrf

                    <!-- Provinsi -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Provinsi</label>
                        <select id="provinsi" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsi as $p)
                                <option value="{{ $p['id'] }}">{{ $p['provinsi'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kabupaten -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Kabupaten</label>
                        <select id="kabupaten" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Kabupaten</option>
                            @foreach($kabupaten as $k)
                                <option value="{{ $k['id'] }}" data-prov-id="{{ $k['prov_id'] }}">{{ $k['kabupaten'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kecamatan -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Kecamatan</label>
                        <select id="kecamatan" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Kecamatan</option>
                            @foreach($kecamatan as $kc)
                                <option value="{{ $kc['id'] }}" data-kab-id="{{ $kc['kab_id'] }}">{{ $kc['kecamatan'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Desa -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Desa</label>
                        <select name="desa_id" id="desa" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih Desa</option>
                            @foreach($desa as $d)
                                <option value="{{ $d['id'] }}" data-kec-id="{{ $d['kec_id'] }}">{{ $d['desa'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kode Ruas -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Kode Ruas</label>
                        <input type="text" name="kode_ruas" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Nama Ruas -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Nama Ruas</label>
                        <input type="text" name="nama_ruas" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Panjang -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Panjang (m)</label>
                        <input type="number" name="panjang" class="w-full border rounded px-3 py-2" required readonly>
                    </div>

                    <!-- Lebar -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Lebar (m)</label>
                        <input type="number" name="lebar" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <!-- Eksisting Jalan -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Eksisting Jalan</label>
                        <select name="eksisting_id" class="w-full border rounded px-3 py-2">
                            @foreach($eksisting as $e)
                                <option value="{{ $e['id'] }}">{{ $e['eksisting'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Jalan -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Jenis Jalan</label>
                        <select name="jenisjalan_id" class="w-full border rounded px-3 py-2">
                            @foreach($jenisjalan as $j)
                                <option value="{{ $j['id'] }}">{{ $j['jenisjalan'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Jalan -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Kondisi Jalan</label>
                        <select name="kondisi_id" class="w-full border rounded px-3 py-2">
                            @foreach($kondisi as $k)
                                <option value="{{ $k['id'] }}">{{ $k['kondisi'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-2">
                        <label class="block font-semibold text-white">Keterangan</label>
                        <textarea name="keterangan" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <!-- Paths -->
                    <input type="hidden" name="paths" id="paths" />

                    <!-- Submit -->
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                    </div>
                </form>
            </div>

            <!-- PETA -->
            <div class="w-full md:w-1/2 px-2">
                <div id="map" class="w-full h-[600px] rounded shadow"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS + CSS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <!-- Leaflet Draw -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

    <!-- Polyline Encode -->
    <script src="https://unpkg.com/@mapbox/polyline@1.1.1/src/polyline.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const map = L.map('map').setView([-8.409518, 115.188919], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            const drawControl = new L.Control.Draw({
                draw: {
                    polygon: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                    polyline: {
                        shapeOptions: {
                            color: 'blue',
                            weight: 5
                        }
                    }
                },
                edit: {
                    featureGroup: drawnItems
                }
            });
            map.addControl(drawControl);

            map.on(L.Draw.Event.CREATED, function (e) {
                drawnItems.clearLayers(); // Hanya 1 polyline saja
                const layer = e.layer;
                drawnItems.addLayer(layer);

                const latlngs = layer.getLatLngs().map(ll => [ll.lat, ll.lng]);
                const encodedPath = polyline.encode(latlngs);

                document.getElementById('paths').value = encodedPath;

                // Hitung panjang
                let panjang = 0;
                for (let i = 1; i < latlngs.length; i++) {
                    panjang += map.distance(latlngs[i - 1], latlngs[i]);
                }
                document.querySelector('input[name="panjang"]').value = panjang;
            });

            // Dropdown Chaining
            const kabupatenEl = document.getElementById('kabupaten');
            const kecamatanEl = document.getElementById('kecamatan');
            const desaEl = document.getElementById('desa');
            const allKabupaten = @json($kabupaten);
            const allKecamatan = @json($kecamatan);
            const allDesa = @json($desa);

            document.getElementById('provinsi').addEventListener('change', function () {
                const provId = this.value;

                kabupatenEl.innerHTML = '<option value="">Pilih Kabupaten</option>';
                kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredKab = allKabupaten.filter(k => k.prov_id == provId);
                filteredKab.forEach(k => {
                    kabupatenEl.innerHTML += `<option value="${k.id}">${k.kabupaten}</option>`;
                });
            });

            kabupatenEl.addEventListener('change', function () {
                const kabId = this.value;

                kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredKec = allKecamatan.filter(k => k.kab_id == kabId);
                filteredKec.forEach(k => {
                    kecamatanEl.innerHTML += `<option value="${k.id}">${k.kecamatan}</option>`;
                });
            });

            kecamatanEl.addEventListener('change', function () {
                const kecId = this.value;

                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredDesa = allDesa.filter(d => d.kec_id == kecId);
                filteredDesa.forEach(d => {
                    desaEl.innerHTML += `<option value="${d.id}">${d.desa}</option>`;
                });
            });
        });
    </script>
</x-layouts.app>

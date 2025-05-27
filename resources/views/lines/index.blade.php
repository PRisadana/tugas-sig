<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Garis') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <!-- Daftar Garis -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Daftar Garis</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b">
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                        <tr class="border-b">
                            <td>
                                <button 
                                    class="text-blue-600 underline hover:text-blue-800 line-link"
                                    data-geo='{{ $line->geometry }}'>
                                    {{ $line->name }}
                                </button>
                            </td>
                            <td class="space-x-2">
                                <form action="{{ route('lines.destroy', $line->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus garis ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if(count($lines) === 0)
                        <tr><td colspan="2" class="text-center py-4">Belum ada garis.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Form Tambah Garis -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <form action="{{ route('lines.store') }}" method="POST" id="line-form">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">Nama Garis</label>
                    <input type="text" name="name" required class="w-full border rounded p-2" />
                </div>

                <input type="hidden" name="geometry" id="geometry" />

                <div class="mt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Garis</button>
                </div>
            </form>
        </div>

        <!-- Peta -->
        <h3 class="text-lg font-semibold mb-2">Peta Garis</h3>
        <div id="map" class="w-full h-[500px] rounded shadow"></div>
    </div>

    <!-- Leaflet & Leaflet.draw -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        const map = L.map('map').setView([-8.409518, 115.188919], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const drawnItems = new L.FeatureGroup().addTo(map);

        // Inisialisasi draw control hanya untuk polyline
        const drawControl = new L.Control.Draw({
            draw: {
                polygon: false,
                rectangle: false,
                circle: false,
                marker: false,
                polyline: true
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);

        // Saat gambar selesai
        map.on('draw:created', function (e) {
            const layer = e.layer;
            drawnItems.clearLayers(); // hanya satu garis yang disimpan
            drawnItems.addLayer(layer);

            const geojson = layer.toGeoJSON();
            document.getElementById('geometry').value = JSON.stringify(geojson);
        });

        // Tampilkan garis dari database
        const lines = @json($lines);
        lines.forEach(l => {
            const geo = JSON.parse(l.geometry);
            L.geoJSON(geo).addTo(map);
        });

        // Zoom to garis saat klik nama
        document.querySelectorAll('.line-link').forEach(btn => {
            btn.addEventListener('click', function () {
                const geo = JSON.parse(this.dataset.geo);
                const layer = L.geoJSON(geo);
                map.fitBounds(layer.getBounds());
            });
        });
    </script>
</x-app-layout>

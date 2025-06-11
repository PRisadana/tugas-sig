<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Polygon') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <!-- Tabel Polygon -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Daftar Polygon</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b">
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($polygons as $polygon)
                        <tr class="border-b">
                            <td>
                                <button 
                                    class="text-blue-600 underline hover:text-blue-800 polygon-link"
                                    data-geo='{{ $polygon->geometry }}'>
                                    {{ $polygon->name }}
                                </button>
                            </td>
                            <td>
                                <form action="{{ route('polygons.destroy', $polygon->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus polygon ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                            <td>
                                <button 
                                    type="button"
                                    class="text-blue-500 hover:underline edit-polygon-btn"
                                    data-id="{{ $polygon->id }}"
                                    data-name="{{ $polygon->name }}"
                                    data-geo='{{ $polygon->geometry }}'>
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @if(count($polygons) === 0)
                        <tr><td colspan="2" class="text-center py-4">Belum ada polygon.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Form Tambah Polygon -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <form action="{{ route('polygons.store') }}" method="POST" id="polygon-form">
                @csrf
                <input type="hidden" name="id" id="polygon-id">

                <div class="mb-4">
                    <label class="block mb-1">Nama Polygon</label>
                    <input type="text" name="name" id="polygon-name" required class="w-full border rounded p-2" />
                </div>

                <input type="hidden" name="geometry" id="geometry" />

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="polygon-submit">
                    Simpan Polygon
                </button>
                <button type="button" id="cancel-edit" class="ml-2 text-gray-600 hover:underline hidden">Batal</button>
            </form>
        </div>


        <!-- Peta -->
        <h3 class="text-lg font-semibold mb-2 text-white">Peta Polygon</h3>
        <div id="map" class="w-full h-[500px] rounded shadow"></div>
    </div>

    <!-- Leaflet & Draw -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        const map = L.map('map').setView([-8.409518, 115.188919], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const drawnItems = new L.FeatureGroup().addTo(map);
        const drawControl = new L.Control.Draw({
            draw: {
                polygon: true,
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);

        map.on('draw:created', function (e) {
            drawnItems.clearLayers();
            drawnItems.addLayer(e.layer);
            const geojson = e.layer.toGeoJSON();
            document.getElementById('geometry').value = JSON.stringify(geojson);
        });

        const polygons = @json($polygons);
        polygons.forEach(p => {
            const geo = JSON.parse(p.geometry);
            L.geoJSON(geo).addTo(map);
        });

        document.querySelectorAll('.polygon-link').forEach(btn => {
            btn.addEventListener('click', function () {
                const geo = JSON.parse(this.dataset.geo);
                const layer = L.geoJSON(geo);
                map.fitBounds(layer.getBounds());
            });
        });

        document.querySelectorAll('.edit-polygon-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const geo = JSON.parse(this.dataset.geo);

                document.getElementById('polygon-id').value = id;
                document.getElementById('polygon-name').value = name;
                document.getElementById('geometry').value = JSON.stringify(geo);
                document.getElementById('polygon-submit').textContent = "Update Polygon";
                document.getElementById('cancel-edit').classList.remove('hidden');

                drawnItems.clearLayers();
                const layer = L.geoJSON(geo);
                layer.eachLayer(l => drawnItems.addLayer(l));
            });
        });

        document.getElementById('cancel-edit').addEventListener('click', function () {
            document.getElementById('polygon-id').value = "";
            document.getElementById('polygon-name').value = "";
            document.getElementById('geometry').value = "";
            document.getElementById('polygon-submit').textContent = "Simpan Polygon";
            this.classList.add('hidden');
            drawnItems.clearLayers();
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Lokasi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah/Edit Lokasi -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
                <form action="{{ isset($location) ? route('locations.update', $location->id) : route('locations.store') }}" method="POST">
                    @csrf
                    @if(isset($location))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <input type="text" name="name" placeholder="Nama Lokasi" value="{{ old('name', $location->name ?? '') }}" required class="border rounded p-2">
                        <input type="text" name="latitude" placeholder="Latitude" value="{{ old('latitude', $location->latitude ?? '') }}" required class="border rounded p-2">
                        <input type="text" name="longitude" placeholder="Longitude" value="{{ old('longitude', $location->longitude ?? '') }}" required class="border rounded p-2">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ isset($location) ? 'Update' : 'Tambah' }} Lokasi
                        </button>

                        @if(isset($location))
                            <a href="{{ route('locations.index') }}" class="ml-2 text-gray-500">Batal</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Daftar Lokasi -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Daftar Lokasi</h3>
                <table class="w-full text-left text-white">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                            <tr class="border-t">
                                <td class="text-white">
                                    <div class="flex flex-col gap-1">
                                        <button 
                                            class="underline hover:text-blue-300 location-link text-left"
                                            data-lat="{{ $loc->latitude }}" 
                                            data-lng="{{ $loc->longitude }}" 
                                            data-name="{{ $loc->name }}"
                                            data-id="{{ $loc->id }}">
                                            {{ $loc->name }}
                                        </button>
                                    </div>
                                </td>
                                <td>{{ $loc->latitude }}</td>
                                <td>{{ $loc->longitude }}</td>
                                <td class="space-x-2">
                                    <a href="{{ route('locations.edit', $loc->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('locations.destroy', $loc->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus lokasi ini?')" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Peta -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Peta Lokasi</h3>
                <!-- Kontrol Geometri dan Reset Marker -->
                <div class="flex gap-3 mb-4">
                    <button id="btn-point" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Point</button>
                    <button id="btn-line" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Line</button>
                    <button id="btn-polygon" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Polygon</button>
                </div>
                <div id="map" class="w-full h-[500px] rounded"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([-8.56132963, 115.33465187], 11);
    
        // Menggunakan OpenStreetMap sebagai background map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    
        let activeLayer = null; // Layer aktif yang sedang ditampilkan
        let selectedGeom = 'point'; // Default geometri adalah Point
        let selectedLocation = null; // Lokasi yang dipilih (lat, lng, name)
        let allMarkers = []; // Array untuk menyimpan semua marker
        let allLineMarkers = []; // Array untuk menyimpan garis yang digambar
        let allPolygonMarkers = []; // Array untuk menyimpan polygon yang digambar
    
        // Data lokasi dari Laravel
        const locations = @json($locations);
    
        // Menampilkan semua marker saat awal
        locations.forEach(loc => {
            const marker = L.marker([loc.latitude, loc.longitude])
                .addTo(map)
                .bindPopup(`<strong>${loc.name}</strong>`);
            allMarkers.push(marker);
        });
    
        // Event listener untuk memilih geometri
        document.getElementById('btn-point').addEventListener('click', () => {
            selectedGeom = 'point';
            updateButtonStyles(); // Update warna tombol
            if (selectedLocation) renderGeometry(); // Jika lokasi sudah dipilih, tampilkan geometri
        });
        document.getElementById('btn-line').addEventListener('click', () => {
            selectedGeom = 'line';
            updateButtonStyles(); // Update warna tombol
            if (selectedLocation) renderGeometry(); // Jika lokasi sudah dipilih, tampilkan geometri
        });
        document.getElementById('btn-polygon').addEventListener('click', () => {
            selectedGeom = 'polygon';
            updateButtonStyles(); // Update warna tombol
            if (selectedLocation) renderGeometry(); // Jika lokasi sudah dipilih, tampilkan geometri
        });
    
    
        // Handler klik nama lokasi
        document.querySelectorAll('.location-link').forEach(button => {
            button.addEventListener('click', function () {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
    
                // Menyimpan lokasi yang dipilih
                selectedLocation = { lat, lng, name };
    
                // Bersihkan layer sebelumnya
                if (activeLayer) {
                    map.removeLayer(activeLayer);
                    activeLayer = null;
                }
    
                // Zoom ke lokasi yang dipilih
                map.setView([lat, lng], 15);
    
                // Tampilkan geometri sesuai tipe yang dipilih
                renderGeometry();
            });
        });
    
        // Fungsi untuk menggambar geometri sesuai dengan tipe yang dipilih
        function renderGeometry() {
            if (!selectedLocation) return;
    
            const { lat, lng, name } = selectedLocation;
    
            // Bersihkan geometri sebelumnya
            if (selectedGeom === 'point' && activeLayer) {
                map.removeLayer(activeLayer);
            }
    
            // Tampilkan sesuai tipe geometri yang dipilih
            if (selectedGeom === 'point') {
                activeLayer = L.marker([lat, lng]).addTo(map)
                    .bindPopup("<b>" + name + "</b>").openPopup();
            }
    
            if (selectedGeom === 'line') {
                // Menghapus line jika ada
                if (allLineMarkers.length > 0) {
                    allLineMarkers.forEach(line => map.removeLayer(line));
                }
    
                const target = [-6.25, 106.85]; // contoh garis menuju titik tetap
                activeLayer = L.polyline([[lat, lng], target], {
                    color: 'orange',
                    weight: 4
                }).addTo(map).bindPopup("Line dari " + name).openPopup();
                allLineMarkers.push(activeLayer);
            }
    
            if (selectedGeom === 'polygon') {
                // Menghapus polygon jika ada
                if (allPolygonMarkers.length > 0) {
                    allPolygonMarkers.forEach(polygon => map.removeLayer(polygon));
                }
    
                const offset = 0.01;
                const coords = [
                    [lat, lng],
                    [lat + offset, lng + offset],
                    [lat - offset, lng + offset]
                ];
                activeLayer = L.polygon(coords, {
                    color: 'blue',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.4
                }).addTo(map).bindPopup("Polygon dari " + name).openPopup();
                allPolygonMarkers.push(activeLayer);
            }
        }
    
        // Fungsi untuk mengubah warna tombol geometri yang dipilih
        function updateButtonStyles() {
            // Reset semua tombol ke warna biru
            document.getElementById('btn-point').classList.remove('bg-blue-700');
            document.getElementById('btn-line').classList.remove('bg-blue-700');
            document.getElementById('btn-polygon').classList.remove('bg-blue-700');
    
            // Set tombol yang dipilih ke biru gelap
            if (selectedGeom === 'point') {
                document.getElementById('btn-point').classList.add('bg-blue-700');
            } else if (selectedGeom === 'line') {
                document.getElementById('btn-line').classList.add('bg-blue-700');
            } else if (selectedGeom === 'polygon') {
                document.getElementById('btn-polygon').classList.add('bg-blue-700');
            }
        }
    </script>        
</x-app-layout>

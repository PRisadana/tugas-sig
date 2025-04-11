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
                                
                                        <select class="geom-type bg-gray-700 text-white text-sm rounded px-2 py-1 mt-1" data-id="{{ $loc->id }}">
                                            <option value="point">Point</option>
                                            <option value="line">Line</option>
                                            <option value="polygon">Polygon</option>
                                        </select>
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
                <div id="map" class="w-full h-[500px] rounded"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([-8.56132963, 115.33465187], 11);
        
        // Menggunakan OpenStreetMap sebagai background map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    
        let activeLayer = null;
    
        document.querySelectorAll('.location-link').forEach(function(button) {
            button.addEventListener('click', function () {
                // Ambil data lokasi dan tipe
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
                const id = this.getAttribute('data-id');
    
                // Ambil tipe geometri dari dropdown
                const typeSelector = document.querySelector(`.geom-type[data-id="${id}"]`);
                const type = typeSelector.value;
    
                // Bersihkan layer sebelumnya agar tidak menumpuk
                if (activeLayer) {
                    map.removeLayer(activeLayer);
                }
    
                // Zoom ke lokasi
                map.setView([lat, lng], 15);
    
                // Tampilkan sesuai jenis geometri
                if (type === 'point') {
                    activeLayer = L.marker([lat, lng]).addTo(map).bindPopup("<b>" + name + "</b>").openPopup();
                }
    
                if (type === 'line') {
                    // Contoh: garis dari lokasi ke titik acak tetap
                    const target = [-6.25, 106.85]; // titik tujuan statis
                    activeLayer = L.polyline([[lat, lng], target], {
                        color: 'red',
                        weight: 4
                    }).addTo(map).bindPopup("Line dari " + name).openPopup();
                }
    
                if (type === 'polygon') {
                    // Contoh: buat polygon segitiga sederhana dari lokasi
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
                }
            });
        });
    </script>    
</x-app-layout>

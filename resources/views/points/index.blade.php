<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Titik') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif

        <!-- Tabel Titik -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2">Daftar Titik</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b">
                        <th>Nama</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($points as $point)
                        <tr class="border-b">
                            <td>
                                <button 
                                    class="text-blue-600 underline hover:text-blue-800 point-link"
                                    data-id="{{ $point->id }}"
                                    data-lat="{{ $point->latitude }}"
                                    data-lng="{{ $point->longitude }}">
                                    {{ $point->name }}
                                </button>
                            </td>
                            <td>{{ $point->latitude }}</td>
                            <td>{{ $point->longitude }}</td>
                            <td class="space-x-2">
                                <form action="{{ route('points.destroy', $point->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus titik ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                            <td>
                                <button 
                                    type="button"
                                    class="text-blue-500 hover:underline edit-btn"
                                    data-id="{{ $point->id }}"
                                    data-name="{{ $point->name }}"
                                    data-lat="{{ $point->latitude }}"
                                    data-lng="{{ $point->longitude }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @if(count($points) === 0)
                        <tr><td colspan="4" class="text-center py-4">Belum ada titik.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Form Tambah Titik -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <form action="{{ route('points.store') }}" method="POST" id="point-form">
                @csrf
                <input type="hidden" name="id" id="point-id">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block mb-1">Nama Titik</label>
                        <input type="text" name="name" id="point-name" required class="w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block mb-1">Latitude</label>
                        <input type="text" name="latitude" id="point-latitude" required class="w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block mb-1">Longitude</label>
                        <input type="text" name="longitude" id="point-longitude" required class="w-full border rounded p-2" />
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="form-button">Simpan Titik</button>
                <button type="button" id="cancel-edit" class="ml-2 text-gray-600 hover:underline hidden">Batal</button>
            </form>
        </div>

        <!-- Peta -->
        <h3 class="text-lg font-semibold mb-2 text-white">Peta Titik Lokasi</h3>
        <div id="map" class="w-full h-[500px] rounded shadow"></div>
    </div>

    <!-- Leaflet JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([-8.409518, 115.188919], 11); // Pulau Bali

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const points = @json($points);
        let allMarkers = {};

        points.forEach(p => {
            const marker = L.marker([p.latitude, p.longitude])
                .addTo(map)
                .bindPopup(`<strong>${p.name}</strong>`);
            allMarkers[p.id] = marker;
        });

        document.querySelectorAll('.point-link').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);

                map.setView([lat, lng], 15);
                if (allMarkers[id]) {
                    allMarkers[id].openPopup();
                }
            });
        });

        // Tombol Edit di tabel
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('point-id').value = this.dataset.id;
                document.getElementById('point-name').value = this.dataset.name;
                document.getElementById('point-latitude').value = this.dataset.lat;
                document.getElementById('point-longitude').value = this.dataset.lng;
                document.getElementById('form-button').textContent = "Update Titik";
                document.getElementById('cancel-edit').classList.remove('hidden');
            });
        });

        // Tombol batal
        document.getElementById('cancel-edit').addEventListener('click', function () {
            document.getElementById('point-id').value = "";
            document.getElementById('point-name').value = "";
            document.getElementById('point-latitude').value = "";
            document.getElementById('point-longitude').value = "";
            document.getElementById('form-button').textContent = "Simpan Titik";
            this.classList.add('hidden');
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Peta Lokasi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Tampilkan Berdasarkan Jenis</h3>
                <div class="flex gap-3 mb-4">
                    <button id="show-point" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Tampilkan Titik</button>
                    <button id="show-line" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Tampilkan Garis</button>
                    <button id="show-polygon" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Tampilkan Polygon</button>
                </div>
                <div id="map" class="w-full h-[500px] rounded"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([-8.409518, 115.188919], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const allData = @json($locations);
        let currentLayers = [];

        function clearLayers() {
            currentLayers.forEach(layer => map.removeLayer(layer));
            currentLayers = [];
        }

        function showPoints() {
            clearLayers();
            allData.filter(l => l.type === 'point').forEach(loc => {
                const marker = L.marker([loc.latitude, loc.longitude])
                    .addTo(map)
                    .bindPopup(`<strong>${loc.name}</strong>`);
                currentLayers.push(marker);
            });
        }

        function showLines() {
            clearLayers();
            allData.filter(l => l.type === 'line').forEach(loc => {
                if (loc.geometry) {
                    const geo = JSON.parse(loc.geometry);
                    const layer = L.geoJSON(geo).addTo(map);
                    currentLayers.push(layer);
                }
            });
        }

        function showPolygons() {
            clearLayers();
            allData.filter(l => l.type === 'polygon').forEach(loc => {
                if (loc.geometry) {
                    const geo = JSON.parse(loc.geometry);
                    const layer = L.geoJSON(geo).addTo(map);
                    currentLayers.push(layer);
                }
            });
        }

        document.getElementById('show-point').addEventListener('click', showPoints);
        document.getElementById('show-line').addEventListener('click', showLines);
        document.getElementById('show-polygon').addEventListener('click', showPolygons);
    </script>
</x-app-layout>

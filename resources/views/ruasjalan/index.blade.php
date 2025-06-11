<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-white">Peta & Data Ruas Jalan</h1>
            <div class="flex space-x-2">
                <button id="filterButton" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Filter Data
                </button>
                <a href="{{ route('ruasjalan.create') }}"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Tambah Ruas Jalan
                </a>
            </div>
        </div>

      

        <!-- Pencarian -->
        <div class="mb-6 flex items-center space-x-2">
            <input type="text" id="search" placeholder="Cari Nama Ruas Jalan"
                class="w-full md:w-1/3 border rounded px-3 py-2">
            <button id="searchButton" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
        </div>

        <!-- TABEL DATA -->
        <div class="overflow-x-auto mb-6 bg-white rounded shadow p-4">
            <table class="w-full text-left border-collapse" id="ruasJalanTable">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="p-2 border">Nama Ruas</th>
                        <th class="p-2 border">Kode Ruas</th>
                        <th class="p-2 border">Panjang (m)</th>
                        <th class="p-2 border">Lebar (m)</th>
                        <th class="p-2 border">Eksisting Jalan</th>
                        <th class="p-2 border">Jenis Jalan</th>
                        <th class="p-2 border">Kondisi Jalan</th>
                        <th class="p-2 border">Keterangan</th>
                        <th class="p-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ruasjalan as $jalan)
                        <tr class="border-t text-gray-800" data-paths="{{ $jalan['paths'] }}"
                            data-name="{{ $jalan['nama_ruas'] }}" data-eksisting="{{ $jalan['eksisting_id'] }}"
                            data-jenisjalan="{{ $jalan['jenisjalan_id'] }}"
                            data-kondisi="{{ $jalan['kondisi_id'] }}">
                            <td class="p-2 border">
                                <button class="text-blue-600 hover:underline jalan-zoom"
                                    data-paths="{{ $jalan['paths'] }}">
                                    {{ $jalan['nama_ruas'] }}
                                </button>
                            </td>
                            <td class="p-2 border">{{ $jalan['kode_ruas'] }}</td>
                            <td class="p-2 border">{{ $jalan['panjang'] }}</td>
                            <td class="p-2 border">{{ $jalan['lebar'] }}</td>
                            <td class="p-2 border">{{ $jalan['eksisting_nama'] ?? '-' }}</td>
                            <td class="p-2 border">{{ $jalan['jenisjalan_nama'] ?? '-' }}</td>
                            <td class="p-2 border">{{ $jalan['kondisi_nama'] ?? '-' }}</td>
                            <td class="p-2 border">{{ $jalan['keterangan'] }}</td>
                            <td class="p-2 border space-x-2">
                                <a href="{{ route('ruasjalan.edit', $jalan['id']) }}"
                                    class="text-yellow-600 hover:underline">Edit</a>
                                   <form action="{{ route('ruasjalan.destroy', $jalan['id']) }}" method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:underline delete-button">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if (count($ruasjalan) === 0)
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">Belum ada ruas jalan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2">


          <!-- Legend and Tile Layer Selectors -->
        <div class="mb-4 ">
            <div>
                <div class="w-64">
                    <label for="legendSelect" class="text-white font-semibold mr-2 w-96">Pilih Jenis Legenda:</label>
                </div>
                <select id="legendSelect" class="border rounded px-3 py-2 w-96">
                    <option value="eksisting">Eksisting Jalan</option>
                    <option value="jenisjalan">Jenis Jalan</option>
                    <option value="kondisi">Kondisi Jalan</option>
                </select>
            </div>
            <div>
                <div class="w-64">
                    <label for="tileLayerSelect" class="text-white font-semibold mr-2 w-96">Pilih Tile Layer:</label>
                </div>
                <select id="tileLayerSelect" class="border rounded px-3 py-2 w-96">
                    <option value="OpenStreetMap">OpenStreetMap</option>
                    <option value="Google Satellite">Google Satellite</option>
                    <option value="CartoDB Dark">CartoDB Dark</option>
                    <option value="Esri World Imagery">Esri World Imagery</option>
                    <option value="OpenStreetMap Humanitarian">OpenStreetMap Humanitarian</option>
                    <option value="OpenStreetMap German">OpenStreetMap German</option>
                    <option value="OpenTopoMap">OpenTopoMap</option>
                    <option value="Google Streets">Google Streets</option>
                    <option value="Google Hybrid">Google Hybrid</option>
                    <option value="Google Terrain">Google Terrain</option>
                    <option value="CartoDB Light">CartoDB Light</option>
                    <option value="CartoDB Voyager">CartoDB Voyager</option>
                </select>
            </div>
        </div>

        <div id="legend" class="bg-white p-4 rounded shadow mb-4"></div>
        </div>


        <!-- Filter Popup -->
        <div id="filterPopup"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-[999]">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Filter Ruas Jalan</h2>
                <form id="filterForm">
                    <!-- Eksisting Filter -->
                    <div class="mb-4">
                        <h3 class="font-semibold">Eksisting Jalan</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="eksisting" value="all" checked class="mr-2">
                                Semua
                            </label>
                            @foreach ($eksisting as $item)
                                <label class="flex items-center">
                                    <input type="radio" name="eksisting" value="{{ $item['id'] }}" class="mr-2">
                                    {{ $item['eksisting'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Jenis Jalan Filter -->
                    <div class="mb-4">
                        <h3 class="font-semibold">Jenis Jalan</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="jenisjalan" value="all" checked class="mr-2">
                                Semua
                            </label>
                            @foreach ($jenisjalan as $item)
                                <label class="flex items-center">
                                    <input type="radio" name="jenisjalan" value="{{ $item['id'] }}" class="mr-2">
                                    {{ $item['jenisjalan'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kondisi Jalan Filter -->
                    <div class="mb-4">
                        <h3 class="font-semibold">Kondisi Jalan

                        </h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="kondisi" value="all" checked class="mr-2">
                                Semua
                            </label>
                            @foreach ($kondisi as $item)
                                <label class="flex items-center">
                                    <input type="radio" name="kondisi" value="{{ $item['id'] }}" class="mr-2">
                                    {{ $item['kondisi'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="closeFilter" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit" class="px-4 pyទ0Apy-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PETA -->
        <div id="map" class="w-full h-[600px] rounded shadow"></div>
    </div>

    <!-- Load Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Load Polyline decoder from Mapbox -->
    <script src="https://unpkg.com/@mapbox/polyline@1.1.1/src/polyline.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const map = L.map('map').setView([-8.409518, 115.188919], 9); // Fokus ke Bali

            // Tile layers configuration
            const tileLayers = {
                OpenStreetMap: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '© OpenStreetMap'
                }),
                'Google Satellite': L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google'
                }),
                'CartoDB Dark': L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 18,
                    attribution: '© CartoDB'
                }),
                'Esri World Imagery': L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 18,
                        attribution: '© Esri'
                    }),
                'OpenStreetMap Humanitarian': L.tileLayer(
                    'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '© OpenStreetMap'
                    }),
                'OpenStreetMap German': L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '© OpenStreetMap'
                }),
                OpenTopoMap: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                    maxZoom: 17,
                    attribution: '© OpenTopoMap'
                }),
                'Google Streets': L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google'
                }),
                'Google Hybrid': L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google'
                }),
                'Google Terrain': L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '© Google'
                }),
                'CartoDB Light': L.tileLayer(
                    'https://{s}.basemaps狙0Aps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 18,
                        attribution: '© CartoDB'
                    }),
                'CartoDB Voyager': L.tileLayer(
                    'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        maxZoom: 18,
                        attribution: '© CartoDB'
                    })
            };

            let currentTileLayer = tileLayers.OpenStreetMap;
            currentTileLayer.addTo(map);

            const ruasJalan = @json($ruasjalan);
            const eksistingData = @json($eksisting);
            const jenisjalanData = @json($jenisjalan);
            const kondisiData = @json($kondisi);
            let polylineLayers = [];

            // Define color mappings for each legend type
            const colorMaps = {
                eksisting: {
                    @foreach ($eksisting as $item)
                        "{{ $item['id'] }}": "{{ ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF'][array_search($item, $eksisting) % 6] }}",
                    @endforeach
                    "default": "#808080"
                },
                jenisjalan: {
                    @foreach ($jenisjalan as $item)
                        "{{ $item['id'] }}": "{{ ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#33FFF3', '#FFC107'][array_search($item, $jenisjalan) % 6] }}",
                    @endforeach
                    "default": "#808080"
                },
                kondisi: {
                    @foreach ($kondisi as $item)
                        "{{ $item['id'] }}": "{{ ['#FF6347', '#4682B4', '#FFD700', '#6A5ACD', '#20B2AA', '#FF4500'][array_search($item, $kondisi) % 6] }}",
                    @endforeach
                    "default": "#808080"
                }
            };

            // Function to update legend
            function updateLegend(legendType) {
                const legendContainer = document.getElementById('legend');
                legendContainer.innerHTML = '<h3 class="font-semibold mb-2">' +
                    (legendType === 'eksisting' ? 'Eksisting Jalan' :
                        legendType === 'jenisjalan' ? 'Jenis Jalan' : 'Kondisi Jalan') +
                    '</h3>';

                let items = [];
                if (legendType === 'eksisting') {
                    items = eksistingData.map(item => ({
                        id: item.id,
                        name: item.eksisting,
                        color: colorMaps.eksisting[item.id] || colorMaps.eksisting.default
                    }));
                } else if (legendType === 'jenisjalan') {
                    items = jenisjalanData.map(item => ({
                        id: item.id,
                        name: item.jenisjalan,
                        color: colorMaps.jenisjalan[item.id] || colorMaps.jenisjalan.default
                    }));
                } else if (legendType === 'kondisi') {
                    items = kondisiData.map(item => ({
                        id: item.id,
                        name: item.kondisi,
                        color: colorMaps.kondisi[item.id] || colorMaps.kondisi.default
                    }));
                }

                items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center mb-1';
                    div.innerHTML = `
                        <div class="w-4 h-4 mr-2" style="background-color: ${item.color};"></div>
                        <span>${item.name}</span>
                    `;
                    legendContainer.appendChild(div);
                });

                // Update map colors based on legend type
                polylineLayers.forEach(p => {
                    const id = p[legendType];
                    const color = id ? colorMaps[legendType][id] || colorMaps[legendType].default :
                        colorMaps[legendType].default;
                    p.layer.setStyle({
                        color
                    });
                });
            }

            // Initialize polyline layers
            ruasJalan.forEach(jalan => {
                if (jalan.paths) {
                    try {
                        const latlngs = polyline.decode(jalan.paths).map(coord => [coord[0], coord[1]]);
                        const polylineLayer = L.polyline(latlngs, {
                            color: 'blue'
                        }).addTo(map);
                        polylineLayer.bindPopup(`
                            <strong>${jalan.nama_ruas}</strong><br>
                            Panjang: ${jalan.panjang} m<br>
                            Lebar: ${jalan.lebar} m
                        `);
                        polylineLayers.push({
                            paths: jalan.paths,
                            layer: polylineLayer,
                            name: jalan.nama_ruas,
                            eksisting: jalan.eksisting_id,
                            jenisjalan: jalan.jenisjalan_id,
                            kondisi: jalan.kondisi_id
                        });
                    } catch (e) {
                        console.warn('Gagal decode paths:', jalan.paths, e);
                    }
                }
            });

            // Initialize legend with default type
            updateLegend('eksisting');

            // Legend selector event listener
            document.getElementById('legendSelect').addEventListener('change', function() {
                updateLegend(this.value);
            });

            // Tile layer selector event listener
            document.getElementById('tileLayerSelect').addEventListener('change', function() {
                if (currentTileLayer) {
                    map.removeLayer(currentTileLayer);
                }
                currentTileLayer = tileLayers[this.value];
                currentTileLayer.addTo(map);
                map.invalidateSize();
            });

            // Zoom to specific ruas jalan when clicking on the name
            document.querySelectorAll('.jalan-zoom').forEach(button => {
                button.addEventListener('click', function() {
                    const targetPaths = this.getAttribute('data-paths');
                    const found = polylineLayers.find(p => p.paths === targetPaths);
                    if (found) {
                        map.fitBounds(found.layer.getBounds());
                        found.layer.openPopup();
                    }
                });
            });

            // Search functionality with button click
            const searchButton = document.getElementById('searchButton');
            const searchInput = document.getElementById('search');
            const tableRows = document.querySelectorAll('#ruasJalanTable tbody tr');

            searchButton.addEventListener('click', function() {
                const searchTerm = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const nameCell = row.querySelector('td:first-child');
                    const name = nameCell ? nameCell.textContent.toLowerCase() : '';

                    if (name.includes(searchTerm)) {
                        row.style.display = '';
                        const targetPaths = row.getAttribute('data-paths');
                        const found = polylineLayers.find(p => p.paths === targetPaths);
                        if (found) {
                            const legendType = document.getElementById('legendSelect').value;
                            const id = found[legendType];
                            const color = id ? colorMaps[legendType][id] || colorMaps[legendType]
                                .default : colorMaps[legendType].default;
                            found.layer.setStyle({
                                color
                            });
                            found.layer.addTo(map);
                        }
                    } else {
                        row.style.display = 'none';
                        const targetPaths = row.getAttribute('data-paths');
                        const found = polylineLayers.find(p => p.paths === targetPaths);
                        if (found) {
                            map.removeLayer(found.layer);
                        }
                    }
                });
            });

            // Filter Popup functionality
            const filterButton = document.getElementById('filterButton');
            const filterPopup = document.getElementById('filterPopup');
            const closeFilter = document.getElementById('closeFilter');
            const filterForm = document.getElementById('filterForm');

            filterButton.addEventListener('click', () => {
                filterPopup.classList.remove('hidden');
            });

            closeFilter.addEventListener('click', () => {
                filterPopup.classList.add('hidden');
            });

            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const eksisting = filterForm.querySelector('input[name="eksisting"]:checked').value;
                const jenisjalan = filterForm.querySelector('input[name="jenisjalan"]:checked').value;
                const kondisi = filterForm.querySelector('input[name="kondisi"]:checked').value;

                tableRows.forEach(row => {
                    const rowEksisting = row.getAttribute('data-eksisting');
                    const rowJenisjalan = row.getAttribute('data-jenisjalan');
                    const rowKondisi = row.getAttribute('data-kondisi');

                    const eksistingMatch = eksisting === 'all' || rowEksisting === eksisting;
                    const jenisjalanMatch = jenisjalan === 'all' || rowJenisjalan === jenisjalan;
                    const kondisiMatch = kondisi === 'all' || rowKondisi === kondisi;

                    if (eksistingMatch && jenisjalanMatch && kondisiMatch) {
                        row.style.display = '';
                        const targetPaths = row.getAttribute('data-paths');
                        const found = polylineLayers.find(p => p.paths === targetPaths);
                        if (found) {
                            const legendType = document.getElementById('legendSelect').value;
                            const id = found[legendType];
                            const color = id ? colorMaps[legendType][id] || colorMaps[legendType]
                                .default : colorMaps[legendType].default;
                            found.layer.setStyle({
                                color
                            });
                            found.layer.addTo(map);
                        }
                    } else {
                        row.style.display = 'none';
                        const targetPaths = row.getAttribute('data-paths');
                        const found = polylineLayers.find(p => p.paths === targetPaths);
                        if (found) {
                            map.removeLayer(found.layer);
                        }
                    }
                });

                filterPopup.classList.add('hidden');
            });

             // Delete functionality with SweetAlert confirmation
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    const namaRuas = form.closest('tr').querySelector('td:first-child').textContent.trim();

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus ruas jalan "${namaRuas}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Refresh map size
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    </script>
</x-layouts.app>

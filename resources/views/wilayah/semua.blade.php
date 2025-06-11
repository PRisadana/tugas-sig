<x-layouts.app>
    <div class="p-6 space-y-4">
        <h1 class="text-xl font-bold text-white">Pilih Wilayah</h1>

        <div class="flex flex-wrap gap-4">
            <div>
                <label for="provinsi" class="block font-medium text-white">Provinsi</label>
                <select id="provinsi" class="border rounded px-2 py-1"></select>
            </div>
            <div>
                <label for="kabupaten" class="block font-medium text-white">Kabupaten</label>
                <select id="kabupaten" class="border rounded px-2 py-1"></select>
            </div>
            <div>
                <label for="kecamatan" class="block font-medium text-white">Kecamatan</label>
                <select id="kecamatan" class="border rounded px-2 py-1"></select>
            </div>
            <div>
                <label for="desa" class="block font-medium text-white">Desa</label>
                <select id="desa" class="border rounded px-2 py-1"></select>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const provinsiEl = document.getElementById('provinsi');
            const kabupatenEl = document.getElementById('kabupaten');
            const kecamatanEl = document.getElementById('kecamatan');
            const desaEl = document.getElementById('desa');

            const allProvinsi = @json($provinsi);
            const allKabupaten = @json($kabupaten);
            const allKecamatan = @json($kecamatan);
            const allDesa = @json($desa);

            // Populate Provinsi
            provinsiEl.innerHTML = '<option value="">Pilih Provinsi</option>';
            allProvinsi.forEach(p => {
                provinsiEl.innerHTML += `<option value="${p.id}">${p.provinsi}</option>`;
            });

            // Provinsi -> Kabupaten
            provinsiEl.addEventListener('change', () => {
                const provId = provinsiEl.value;
                kabupatenEl.innerHTML = '<option value="">Pilih Kabupaten</option>';
                kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredKab = allKabupaten.filter(k => k.prov_id == provId);
                filteredKab.forEach(k => {
                    kabupatenEl.innerHTML += `<option value="${k.id}">${k.kabupaten}</option>`;
                });
            });

            // Kabupaten -> Kecamatan
            kabupatenEl.addEventListener('change', () => {
                const kabId = kabupatenEl.value;
                kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredKec = allKecamatan.filter(k => k.kab_id == kabId);
                filteredKec.forEach(k => {
                    kecamatanEl.innerHTML += `<option value="${k.id}">${k.kecamatan}</option>`;
                });
            });

            // Kecamatan -> Desa
            kecamatanEl.addEventListener('change', () => {
                const kecId = kecamatanEl.value;
                desaEl.innerHTML = '<option value="">Pilih Desa</option>';

                const filteredDesa = allDesa.filter(d => d.kec_id == kecId);
                filteredDesa.forEach(d => {
                    desaEl.innerHTML += `<option value="${d.id}">${d.desa}</option>`;
                });
            });
        });
    </script>
</x-layouts.app>

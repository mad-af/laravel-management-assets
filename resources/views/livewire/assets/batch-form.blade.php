<div class="space-y-6">
    <div>
        <h3 class="mb-2 text-base font-semibold">Unduh Template</h3>
        <p class="mb-3 text-sm text-base-content/70">Gunakan template ini untuk menyusun data batch aset. Format yang didukung: .xlsx</p>
        <x-button label="Unduh Template" icon="o-document-arrow-down" class="btn-primary btn-sm" wire:click="downloadTemplate" />
    </div>

    <div class="divider"></div>

    <div>
        <h3 class="mb-2 text-base font-semibold">Upload Data Batch</h3>
        <p class="mb-3 text-sm text-base-content/70">Unggah file data batch dalam format .xlsx, .xls, atau .csv sesuai kolom template.</p>
        <input type="file" class="w-full file-input file-input-bordered file-input-sm" accept=".xlsx,.xls,.csv" wire:model.live="file" />
        <div class="mt-2 text-xs text-base-content/70" wire:loading.delay wire:target="file,uploadBatch">
            Mengunggah & memeriksa file...
        </div>
        <div class="mt-3">
            <x-button label="Unggah" icon="o-arrow-up-tray" class="btn-secondary btn-sm" wire:click="save" />
        </div>
        @error('file')
            <div class="mt-2 text-sm text-error">{{ $message }}</div>
        @enderror
    </div>

    @if($summaryTotal > 0)
        <div class="divider"></div>
        <div>
            <h3 class="mb-2 text-base font-semibold">Ringkasan Pengecekan</h3>
            <div class="text-sm text-base-content/70">Berikut hasil validasi berdasarkan aturan template.</div>

            <div class="grid grid-cols-3 gap-3 mt-3">
                <div class="stat bg-base-200 rounded-box">
                    <div class="stat-title">Total Data</div>
                    <div class="text-base stat-value">{{ $summaryTotal }}</div>
                </div>
                <div class="stat bg-base-200 rounded-box">
                    <div class="stat-title">Valid / Diterima</div>
                    <div class="stat-value text-success">{{ $summaryValid }}</div>
                </div>
                <div class="stat bg-base-200 rounded-box">
                    <div class="stat-title">Tidak Valid</div>
                    <div class="stat-value text-error">{{ $summaryInvalid }}</div>
                </div>
            </div>

            @if($summaryInvalid > 0)
                <div class="mt-4">
                    <h4 class="mb-2 font-semibold">Detail Error Per Baris</h4>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Baris Excel</th>
                                    <th>Kesalahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rowErrors as $rowError)
                                    <tr>
                                        <td class="w-24">{{ $rowError['row'] }}</td>
                                        <td>
                                            <ul class="ml-4 list-disc">
                                                @foreach($rowError['errors'] as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
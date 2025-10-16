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
        <input type="file" class="file-input file-input-bordered w-full file-input-sm" accept=".xlsx,.xls,.csv" wire:model="file" />
        <div class="mt-3">
            <x-button label="Unggah" icon="o-arrow-up-tray" class="btn-secondary btn-sm" wire:click="uploadBatch" />
        </div>
        @error('file')
            <div class="mt-2 text-sm text-error">{{ $message }}</div>
        @enderror
    </div>
</div>
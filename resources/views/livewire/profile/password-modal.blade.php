<x-modal wire:model="passwordModal" title="Ubah Password">
    <x-form no-separator>
        <x-password label="Password Saat Ini" wire:model.defer="current_password" right />
        <x-password label="Password Baru" wire:model.defer="password" right />
        <x-password label="Konfirmasi Password Baru" wire:model.defer="password_confirmation" right />

        <x-slot:actions>
            <x-button label="Batal" class="btn-ghost" @click="$wire.passwordModal = false" />
            <x-button label="Update Password" class="btn-primary" wire:click="updatePassword" />
        </x-slot:actions>
    </x-form>
</x-modal>
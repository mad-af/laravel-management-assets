@extends('layouts.dashboard')

@section('title', 'Assets Management')

@section('content')
    <livewire:dashboard-content-header title='Assets Management' description='Kelola data aset dalam sistem.'
        buttonText='Tambah Asset' buttonIcon='o-plus' buttonAction='openAssetDrawer' :additional-buttons="[
                [
                    'text' => 'Unduh Data Asset',
                    'icon' => 'o-document-arrow-down',
                    'class' => ' btn-sm',
                    'action' => 'downloadAsset'
                ],
                [
                    'text' => 'Print QR/Barcode',
                    'icon' => 'o-qr-code',
                    'class' => ' btn-sm',
                    'action' => 'printQrBarcode'
                ],
                [
                    'text' => 'Tambah Batch',
                    'icon' => 'o-folder-plus',
                    'class' => ' btn-sm',
                    'action' => 'openBatchDrawer'
                ]
            ]"/>

    <livewire:assets.table />

    <livewire:assets.drawer />
@endsection
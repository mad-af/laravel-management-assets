<?php

namespace App\Livewire\Components;

use Carbon\Carbon;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class DayMonthPicker extends Component
{
    #[Modelable]
    public ?string $value = null; // Format: YYYY-mm-dd

    // Properti untuk binding komponen x-select pada Blade saat ini
    public $selectedUser = null; // representasi bulan terpilih dari x-select

    public ?int $day = null;

    public ?int $month = null;

    public ?int $year = null; // tahun untuk menghitung jumlah hari dalam bulan (default: tahun sekarang)

    public ?string $label = null;

    public bool $required = false;

    public bool $disabled = false;

    // Opsi untuk select bulan dan tanggal (akan dipakai oleh Blade bila di-binding)
    public array $monthOptions = [];

    public array $dayOptions = [];

    protected array $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public function mount($label = null, $required = false, $disabled = false, $year = null): void
    {
        $this->label = $label;
        $this->required = (bool) $required;
        $this->disabled = (bool) $disabled;
        $this->year = is_numeric($year) ? (int) $year : Carbon::now()->year;

        // Bangun opsi bulan 1..12
        $this->monthOptions = $this->buildMonthOptions();

        // Inisialisasi dari value bila ada (format YYYY-mm-dd)
        if (is_string($this->value) && preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $this->value)) {
            [$y, $m, $d] = explode('-', $this->value);
            $this->year = (int) $y;
            $this->day = $this->sanitizeDay($d);
            $this->month = $this->sanitizeMonth($m);
            $this->selectedUser = $this->month; // sinkronkan dengan x-select di Blade
        }

        // Bangun opsi tanggal berdasarkan bulan & tahun
        $this->rebuildDayOptions();
        $this->syncValue();
    }

    // Dipanggil saat bulan dari x-select berubah
    public function updatedSelectedUser($val): void
    {
        $this->month = $this->sanitizeMonth($val);
        $this->rebuildDayOptions();
        // Clamp day bila melebihi
        $max = $this->daysInMonth($this->month ?? 1, $this->year ?? Carbon::now()->year);
        if ($this->day && $this->day > $max) {
            $this->day = $max;
        }
        $this->syncValue();
    }

    public function updatedMonth($val): void
    {
        $this->month = $this->sanitizeMonth($val);
        $this->selectedUser = $this->month; // jaga agar sinkron dengan x-select
        // Reset hari saat bulan berubah agar tidak nyangkut
        $this->day = null;
        $this->rebuildDayOptions();
        $this->recalcYearFromDayMonth();
        $this->syncValue();
    }

    public function updatedDay($val): void
    {
        $this->day = $this->sanitizeDay($val);
        $this->recalcYearFromDayMonth();
        $this->syncValue();
    }

    public function updatedYear($val): void
    {
        $this->year = is_numeric($val) ? (int) $val : Carbon::now()->year;
        $this->rebuildDayOptions();
        // Clamp day jika perlu
        $max = $this->daysInMonth($this->month ?? 1, $this->year);
        if ($this->day && $this->day > $max) {
            $this->day = $max;
        }
        $this->syncValue();
    }

    protected function sanitizeMonth($val): ?int
    {
        $m = (int) $val;

        return ($m >= 1 && $m <= 12) ? $m : null;
    }

    protected function sanitizeDay($val): ?int
    {
        $d = (int) $val;

        return ($d >= 1 && $d <= 31) ? $d : null;
    }

    protected function buildMonthOptions(): array
    {
        $opts = [];
        foreach ($this->monthNames as $num => $name) {
            $opts[] = [
                'value' => $num,
                'label' => $name,
            ];
        }

        return $opts;
    }

    protected function rebuildDayOptions(): void
    {
        $month = $this->month ?? 1;
        $year = $this->year ?? Carbon::now()->year;
        $days = $this->daysInMonth($month, $year);
        $this->dayOptions = [];
        for ($i = 1; $i <= $days; $i++) {
            $this->dayOptions[] = [
                'value' => $i,
                'label' => (string) $i,
            ];
        }
    }

    protected function daysInMonth(int $month, int $year): int
    {
        return Carbon::create($year, $month, 1)->daysInMonth;
    }

    protected function syncValue(): void
    {
        if ($this->day && $this->month) {
            $this->value = sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
        } else {
            $this->value = null;
        }
    }

    protected function recalcYearFromDayMonth(): void
    {
        // Tahun mengikuti (day, month): jika tanggal-bulan sudah lewat tahun ini, pakai tahun depan
        if ($this->month && $this->day) {
            $today = Carbon::today();
            $candidate = Carbon::create($today->year, $this->month, $this->day);
            $this->year = $candidate->lt($today) ? $today->year + 1 : $today->year;
        } else {
            // Jika belum lengkap, default ke tahun sekarang
            $this->year = Carbon::now()->year;
        }
    }

    public function render()
    {

        return view('livewire.components.day-month-picker', [
            // Berikan opsi ke Blade (bila digunakan oleh komponen x-select Anda)
            'monthOptions' => $this->monthOptions,
            'dayOptions' => $this->dayOptions,
        ]);
    }
}

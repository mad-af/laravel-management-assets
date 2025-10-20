<?php

namespace App\Livewire\Components;

use Illuminate\Support\Arr;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class Combobox extends Component
{
    public string $name;

    #[Modelable]
    public $value = null;

    public $selected = [];

    public string $id = '';

    public ?string $label = null;

    public string $placeholder = 'Pilih...';

    /**
     * Options can be array of arrays/objects. Each option should have keys specified
     * by $optionValue and $optionLabel (default: id, name)
     */
    public array $options = [];

    public string $optionValue = 'id';

    public string $optionLabel = 'name';

    public string $search = '';

    public string $class = '';

    public bool $showDropdown = false;

    // Menandai loading saat pencarian berjalan
    public bool $isLoading = false;

    public bool $clearable = true;

    public bool $disabled = false;

    public bool $required = false;

    public bool $multiple;

    public ?string $emptyText = 'Tidak ada hasil';

    // Tambahkan properti header yang bisa diubah
    public string $headerText = 'Pilihan yang tersedia';

    // Kunci opsional untuk avatar, sublabel, dan meta pada opsi
    public ?string $optionAvatar = null;

    public ?string $optionSubLabel = null;

    public ?string $optionMeta = null;

    public function mount(
        string $name,
        $value = null,
        array $options = [],
        $label = null,
        $placeholder = null,
        $optionValue = 'id',
        $optionLabel = 'name',
        $disabled = false,
        $clearable = true,
        $multiple = false,
        $headerText = null,
        $optionAvatar = null,
        $optionSubLabel = null,
        $optionMeta = null,
    ) {
        $this->id = uniqid('combobox-');
        $this->name = $name;
        $this->options = $options;
        $this->label = $label;
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
        $this->optionValue = $optionValue;
        $this->optionLabel = $optionLabel;
        $this->disabled = (bool) $disabled;
        $this->clearable = (bool) $clearable;
        $this->multiple = (bool) $multiple;

        // Set header jika dikirim dari atribut
        if ($headerText !== null) {
            $this->headerText = $headerText;
        }

        // Set kunci optional untuk avatar, sublabel, dan meta jika dikirim
        if ($optionAvatar !== null) {
            $this->optionAvatar = $optionAvatar;
        }
        if ($optionSubLabel !== null) {
            $this->optionSubLabel = $optionSubLabel;
        }
        if ($optionMeta !== null) {
            $this->optionMeta = $optionMeta;
        }

        if (! empty($this->value)) {
            $this->syncSelected();
        }
        // dd($this->value, $this->selected);
    }

    public function updatedSearch($search)
    {
        $this->showDropdown = true;
        $this->isLoading = true;

        // Emit event ke parent untuk memuat options hasil pencarian (opsional)
        $this->dispatch('combobox-load-'.$this->name, $search);
    }

    protected function getListeners()
    {
        return [
            'combobox-set-'.$this->name => 'onSetOptions',
        ];
    }

    public function onSetOptions($options)
    {
        // Terima data options dari parent dan set ke komponen
        $this->options = $options;
        $this->isLoading = false;
    }

    public function updatedValue($param)
    {
        if (empty($this->value)) {
            $this->selected = $this->multiple ? [] : null;

            return;
        }

        $this->syncSelected();
    }

    protected function syncSelected()
    {
        if ($this->multiple) {
            $this->selected = collect(Arr::wrap($this->value))
            ->map(function ($val) {
                return $this->findOptionByValue($val);
            });
        } else {
            dd($this->findOptionByValue($this->value));
            $this->showDropdown = false;
            $this->selected = collect([$this->findOptionByValue($this->value)]);
        }
    }

    public function updatedOptions()
    {
        // Jika options berubah dari proses lain, matikan indikator loading
        $this->isLoading = false;
    }

    #[On('combobox-clear')]
    public function clear()
    {
        $this->value = $this->multiple ? [] : null;
        $this->selected = [];
        $this->search = '';
        $this->showDropdown = false;
    }

    protected function normalizedOptions()
    {
        return collect($this->options)->map(function ($o) {
            if (is_array($o)) {
                return $o;
            }
            if (is_object($o)) {
                return (array) $o;
            }

            // Untuk opsi skalar, gunakan kunci dinamis sesuai konfigurasi
            $option = [
                $this->optionValue => $o,
                $this->optionLabel => (string) $o,
            ];

            // Tambahkan kunci opsional jika dikonfigurasi
            if ($this->optionAvatar) {
                $option[$this->optionAvatar] = null;
            }
            if ($this->optionSubLabel) {
                $option[$this->optionSubLabel] = null;
            }
            if ($this->optionMeta) {
                $option[$this->optionMeta] = null;
            }

            return $option;
        });
    }

    protected function findOptionByValue($val)
    {
        if ($val === null || $val === '') {
            return null;
        }

        return $this->normalizedOptions()->first(function ($o) use ($val) {
            return (string) data_get($o, $this->optionValue) === (string) $val;
        });
    }

    public function render()
    {
        $options = $this->normalizedOptions()
            ->filter(function ($o) {
                if ($this->search === '') {
                    return true;
                }
                $label = (string) data_get($o, $this->optionLabel, '');

                return str_contains(strtolower($label), strtolower($this->search));
            })
            ->take(20)
            ->values();

        return view('livewire.components.combobox', compact('options'));
    }
}

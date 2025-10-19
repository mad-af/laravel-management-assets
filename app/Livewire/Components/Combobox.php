<?php

namespace App\Livewire\Components;

use Illuminate\Support\Arr;
use Livewire\Component;

class Combobox extends Component
{
    public $value = null;

    public $selected = [];

    public string $id = '';

    public ?string $label = null;

    public string $placeholder = 'Pilih...';

    /**
     * Options can be array of arrays/objects. Each option should have keys specified
     * by $optionValue and $optionLabel (default: id, name)
     */
    public $options = [];

    public string $optionValue = 'id';

    public string $optionLabel = 'name';

    public string $search = '';

    public string $class = '';

    public bool $showDropdown = false;

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
        $value = null,
        $options = [],
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

        if ($this->multiple) {
            $this->value = $value ?? [];
        } else {
            $this->value = $value ?? null;
        }
    }

    public function updatedSearch()
    {
        $this->showDropdown = true;
    }

    public function updatedValue($coba)
    {
        if (! $this->multiple) {
            $this->showDropdown = false;
            $this->selected = collect([$this->findOptionByValue($this->value)]);
        } else {
            $this->selected = collect(Arr::wrap($this->value))
                ->map(function ($val) {
                    return $this->findOptionByValue($val);
                });
        }

    }

    public function clear()
    {
        $this->value = $this->multiple ? [] : null;
        $this->selected = [];
        $this->search = '';
        $this->showDropdown = false;
        $this->dispatch('combobox-cleared');
    }

    protected function normalizedOptions()
    {
        // Jika options adalah query builder, lakukan pencarian di server
        if ($this->options instanceof \Illuminate\Database\Eloquent\Builder
            || $this->options instanceof \Illuminate\Database\Query\Builder) {
            $builder = clone $this->options;

            // Tentukan kolom yang perlu diambil
            $columns = array_values(array_unique(array_filter([
                $this->optionValue,
                $this->optionLabel,
                $this->optionAvatar,
                $this->optionSubLabel,
                $this->optionMeta,
            ])));

            if (! empty($columns)) {
                $builder->select($columns);
            }

            if ($this->search !== '') {
                $builder->where($this->optionLabel, 'like', '%'.$this->search.'%');
            }

            $result = $builder->limit(20)->get();

            return collect($result)->map(function ($row) {
                if ($row instanceof \Illuminate\Database\Eloquent\Model) {
                    return $row->getAttributes();
                }
                if (is_array($row)) {
                    return $row;
                }

                return (array) $row;
            });
        }

        // Default: options berupa array/objek/scalar
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

        // Jika options adalah query builder, ambil langsung dari DB
        if ($this->options instanceof \Illuminate\Database\Eloquent\Builder
            || $this->options instanceof \Illuminate\Database\Query\Builder) {
            $builder = clone $this->options;

            $columns = array_values(array_unique(array_filter([
                $this->optionValue,
                $this->optionLabel,
                $this->optionAvatar,
                $this->optionSubLabel,
                $this->optionMeta,
            ])));

            if (! empty($columns)) {
                $builder->select($columns);
            }

            $row = $builder->where($this->optionValue, $val)->first();

            if (! $row) {
                return null;
            }

            if ($row instanceof \Illuminate\Database\Eloquent\Model) {
                return $row->getAttributes();
            }
            if (is_array($row)) {
                return $row;
            }

            return (array) $row;
        }

        // Default: cari di koleksi opsi yang sudah dinormalisasi
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

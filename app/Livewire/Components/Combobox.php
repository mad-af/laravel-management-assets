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
    public array $options = [];

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
    public string $headerText = 'Pilihan tersedia';

    public function mount(
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
        return collect($this->options)->map(function ($o) {
            if (is_array($o)) {
                return $o;
            }
            if (is_object($o)) {
                return (array) $o;
            }

            return ['id' => $o, 'name' => (string) $o];
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

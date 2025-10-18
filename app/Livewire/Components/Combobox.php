<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Combobox extends Component
{
    public $value = null;

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

    public bool $showDropdown = false;

    public bool $clearable = true;

    public bool $disabled = false;

    public bool $multiple;

    public ?string $emptyText = 'Tidak ada hasil';

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

        if ($this->multiple) {
            $this->value = $value ?? [];
        } else {
            $this->value = $value ?? null;
        }

        // Normalize value shape according to multiple mode
        // if ($this->multiple) {
        //     $this->value = is_array($value) ? $value : ($value === null ? [] : [$value]);
        // } else {
        //     $this->value = is_array($value) ? (count($value) ? $value[0] : null) : $value;
        // }

        // Initialize search with selected label if any (single mode)
        // if (! $this->multiple && $this->value) {
        //     $selected = $this->findOptionByValue($this->value);
        //     if ($selected) {
        //         $this->search = data_get($selected, $this->optionLabel);
        //     }
        // }
    }

    public function updatedSearch()
    {
        $this->showDropdown = true;
    }

    public function select($id)
    {
        $opt = $this->findOptionByValue($id);
        if ($opt) {
            $this->value = data_get($opt, $this->optionValue);
            $this->search = data_get($opt, $this->optionLabel);
            $this->showDropdown = false;
            $this->dispatch('combobox-selected', value: $this->value);
        }
    }

    public function clear()
    {
        $this->value = $this->multiple ? [] : null;
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

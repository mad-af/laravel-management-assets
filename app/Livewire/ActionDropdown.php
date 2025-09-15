<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;

class ActionDropdown extends Component
{
    public Model $model;
    public array $actions = [];
    public string $editEvent = 'edit-item';
    public string $deleteEvent = 'item-deleted';
    public string $viewEvent = 'view-item';
    public string $duplicateEvent = 'duplicate-item';
    public string $confirmMessage = 'Are you sure you want to delete this item?';

    public function mount(
        Model $model, 
        array $actions = ['edit', 'delete'],
        string $editEvent = 'edit-item',
        string $deleteEvent = 'item-deleted',
        string $viewEvent = 'view-item',
        string $duplicateEvent = 'duplicate-item',
        string $confirmMessage = 'Are you sure you want to delete this item?'
    ) {
        $this->model = $model;
        $this->actions = $actions;
        $this->editEvent = $editEvent;
        $this->deleteEvent = $deleteEvent;
        $this->viewEvent = $viewEvent;
        $this->duplicateEvent = $duplicateEvent;
        $this->confirmMessage = $confirmMessage;
    }

    public function render()
    {
        return view('livewire.action-dropdown');
    }

    public function edit()
    {
        $this->dispatch($this->editEvent, $this->model->id);
    }

    public function delete()
    {
        $this->model->delete();
        $this->dispatch($this->deleteEvent);
    }

    public function view()
    {
        $this->dispatch($this->viewEvent, $this->model->id);
    }

    public function duplicate()
    {
        $this->dispatch($this->duplicateEvent, $this->model->id);
    }

    public function hasAction(string $action): bool
    {
        return in_array($action, $this->actions);
    }
}
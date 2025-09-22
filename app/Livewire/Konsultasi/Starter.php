<?php

namespace App\Livewire\Konsultasi;

use Livewire\Component;

class Starter extends Component
{
    public $showConfirmationModal = false;

    public function openConfirmationModal()
    {
        $this->showConfirmationModal = true;
    }

    public function closeConfirmationModal()
    {
        $this->showConfirmationModal = false;
    }

    public function startConsultation()
    {
        // Emit an event or redirect to the next step of the consultation
        // For now, we'll just close the modal and emit an event.
        $this->closeConfirmationModal();
        $this->dispatch('consultationStarted');
    }

    public function render()
    {
        return view('livewire.konsultasi.starter');
    }
}
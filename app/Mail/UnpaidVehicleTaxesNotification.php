<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UnpaidVehicleTaxesNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Company $company;
    public Collection $histories;

    public function __construct(Company $company, Collection $histories)
    {
        $this->company = $company;
        $this->histories = $histories;
    }

    public function build(): self
    {
        return $this
            ->subject('Pemberitahuan Pajak Kendaraan Belum Dibayar')
            ->view('emails.vehicle-taxes.unpaid');
    }
}
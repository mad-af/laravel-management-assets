<?php

namespace App\Console\Commands;

use App\Mail\UnpaidVehicleTaxesNotification;
use App\Models\User;
use App\Models\VehicleTaxHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyUnpaidVehicleTaxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle-tax:notify-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email ke semua user di perusahaan jika terdapat pajak kendaraan yang belum dibayar';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan pajak kendaraan yang belum dibayar...');

        // Ambil histori pajak terbaru per tipe pajak, lalu filter yang belum dibayar
        $latestHistoryIds = VehicleTaxHistory::select('id')->where('paid_date', null);

        $histories = VehicleTaxHistory::with(['asset.company', 'vehicleTaxType'])
            ->whereIn('id', $latestHistoryIds)
            ->orderBy('due_date', 'asc')
            ->get();

        if ($histories->isEmpty()) {
            $this->info('Tidak ada pajak kendaraan yang belum dibayar.');

            return Command::SUCCESS;
        }

        // Kelompokkan per perusahaan
        $grouped = $histories->groupBy(fn ($h) => optional($h->asset)->company_id);

        // Loop per perusahaan: kirim email berbeda untuk setiap company yang memiliki data belum dibayar
        foreach ($grouped as $companyId => $companyHistories) {
            if (! $companyId || $companyHistories->isEmpty()) {
                continue;
            }

            $company = optional(optional($companyHistories->first())->asset)->company;

            // Kumpulkan penerima untuk perusahaan ini via pivot user_companies
            $recipients = User::whereHas('userCompanies', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->where('email_verified_at', '!=', null);
            })->pluck('email')->filter(
                fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
            )->unique()->values();

            // Tentukan alamat 'to' utama: pakai email perusahaan jika valid, jika tidak gunakan salah satu user penerima
            $to = $recipients;
            $to->add("madaf@yopmail.com");
            if ($to->isEmpty()) {
                $this->warn('Lewati perusahaan ID: '.$companyId.' karena tidak ada penerima email (to) yang valid.');

                continue;
            }

            // Ambil daftar CC dari konfigurasi (env MAIL_CC, dipisah dengan koma)
            $ccRaw = (string) config('mail.cc');
            $ccList = collect(explode(',', $ccRaw))
                ->map(fn ($e) => trim($e))
                ->filter(fn ($e) => $e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values();

            // Ambil daftar BCC dari env (env MAIL_BCC, dipisah dengan koma)
            $bccRaw = (string) config('mail.bcc');
            $bccList = collect(explode(',', $bccRaw))
                ->map(fn ($e) => trim($e))
                ->filter(fn ($e) => $e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values();

            try {
                $mailer = Mail::to($to);
                if ($ccList->isNotEmpty()) {
                    $mailer->cc($ccList->all());
                }
                if (! empty($bccList)) {
                    $mailer->bcc($bccList);
                }
                $mailer->send(new UnpaidVehicleTaxesNotification(
                    $company ?? optional(optional($companyHistories->first())->asset)->company,
                    $companyHistories
                ));
                $this->info('Mengirim notifikasi ke perusahaan: '.(optional($company)->name ?? 'ID '.$companyId).' (to: '.$to.', bcc: '.count($bccList).', cc: '.$ccList->count().').');
            } catch (\Exception $e) {
                $this->error('Gagal mengirim email untuk perusahaan '.(optional($company)->name ?? 'ID '.$companyId).': '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}

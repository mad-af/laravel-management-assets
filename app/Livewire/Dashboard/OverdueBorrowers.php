<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Employee;
use App\Support\SessionKey;
use Carbon\Carbon;
use Livewire\Component;

class OverdueBorrowers extends Component
{
    public bool $useDummy = false;

    protected function dummyLoans()
    {
        $loan1 = new AssetLoan(['due_at' => now()->subDays(3)]);
        $loan1->setRelation('asset', new Asset(['name' => 'Laptop Dell Latitude 5520']));
        $loan1->setRelation('employee', new Employee(['full_name' => 'Andi Saputra']));

        $loan2 = new AssetLoan(['due_at' => now()->subDays(7)]);
        $loan2->setRelation('asset', new Asset(['name' => 'Printer Epson L3150']));
        $loan2->setRelation('employee', new Employee(['full_name' => 'Budi Santoso']));

        return collect([$loan1, $loan2]);
    }

    protected function computeDueStatus(?Carbon $due): array
    {
        if (! $due) {
            return ['text' => '—', 'badge' => 'badge badge-ghost'];
        }

        $now = now();
        $isOverdue = $now->greaterThan($due);

        $from = $isOverdue ? $due : $now;
        $to = $isOverdue ? $now : $due;

        $diff = $from->diff($to);

        $parts = [];
        if ($diff->y) {
            $parts[] = $diff->y.' tahun';
        }
        if ($diff->m) {
            $parts[] = $diff->m.' bulan';
        }
        if ($diff->d || empty($parts)) {
            $parts[] = $diff->d.' hari';
        }

        $label = implode(' ', $parts);

        if ($diff->y === 0 && $diff->m === 0 && $diff->d === 0) {
            return ['text' => 'Jatuh tempo hari ini', 'badge' => 'text-warning'];
        }

        if ($isOverdue) {
            return ['text' => 'Terlambat '.$label, 'badge' => 'font-medium text-error'];
        }

        return ['text' => 'Sisa '.$label, 'badge' => 'text-primary'];
    }

    protected function computeInitials(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '—';
        }
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($words[0], 0, 2));
    }

    public function getOverdueLoans()
    {
        if ($this->useDummy) {
            return $this->dummyLoans();
        }

        $currentBranchId = session_get(SessionKey::BranchId);

        return AssetLoan::query()
            ->overdue()
            ->with(['asset', 'employee'])
            ->whereHas('asset', function ($q) use ($currentBranchId) {
                $q->forBranch($currentBranchId);
            })
            ->orderBy('due_at')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        $loans = $this->getOverdueLoans();

        // Enrich loans with computed attributes for the view
        foreach ($loans as $loan) {
            $due = $loan->due_at ? Carbon::parse($loan->due_at) : null;
            $status = $this->computeDueStatus($due);
            $loan->setAttribute('due_status_text', $status['text']);
            $loan->setAttribute('color', $status['badge']);
            $loan->setAttribute('employee_initials', $this->computeInitials($loan->employee?->full_name));
        }

        $count = $loans->count();

        return view('livewire.dashboard.overdue-borrowers', compact('loans', 'count'));
    }
}

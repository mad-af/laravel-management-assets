<?php

namespace App\Livewire\Companies;

use App\Models\Branch;
use App\Models\Company;
use App\Models\UserCompany;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;

    public $companyId;

    public $name = '';

    public $code = '';

    public $tax_id = '';

    public $phone = '';

    public $email = '';

    public $website = '';

    public $image;

    public $is_active = true;

    public $isEdit = false;

    public $hq_branch_id = '';

    public $branches = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:companies,code',
        'tax_id' => 'nullable|string|max:50',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'website' => 'nullable|url|max:255',
        'image' => 'nullable|image|max:2048',
        'is_active' => 'boolean',
    ];

    protected $listeners = [
        'editCompany' => 'edit',
        'resetForm' => 'resetForm',
    ];

    public function mount($companyId = null)
    {
        // Set companyId lebih dulu agar loadBranches terfilter dengan benar
        $this->companyId = $companyId;

        if ($this->companyId) {
            $this->loadCompany($this->companyId);
        }

        // Muat daftar cabang setelah companyId di-set
        $this->loadBranches();
    }

    public function loadCompany(string $companyId)
    {
        $company = Company::find($companyId);
        if (! $company) {
            $this->error('Data Tidak Ditemukan', 'Perusahaan tidak ditemukan.');

            return;
        }

        $this->companyId = $company->id;
        $this->name = $company->name;
        $this->code = $company->code;
        $this->hq_branch_id = $company->hq_branch_id ?? '';
        $this->tax_id = $company->tax_id ?? '';
        $this->phone = $company->phone ?? '';
        $this->email = $company->email ?? '';
        $this->website = $company->website ?? '';
        $this->is_active = (bool) $company->is_active;

        // Saat edit, abaikan record saat ini untuk validasi unik code
        $this->rules['code'] = 'required|string|max:10|unique:companies,code,'.$company->id.',id';

        $this->isEdit = true;
    }

    public function loadBranches()
    {
        $this->branches = Branch::where('is_active', true)
            ->where('company_id', $this->companyId)
            ->orderBy('name')
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                ];
            })
            ->toArray();
    }

    public function resetForm()
    {
        $this->reset([
            'companyId', 'name', 'code', 'hq_branch_id', 'tax_id',
            'phone', 'email', 'website', 'image', 'is_active', 'isEdit',
        ]);
        $this->is_active = true;
        $this->hq_branch_id = '';
        $this->rules['code'] = 'required|string|max:10|unique:companies,code';
        $this->loadBranches();
    }

    public function generateCode()
    {
        // Ambil huruf pertama dari setiap kata
        $initial = collect(explode(' ', $this->name))
            ->map(fn ($word) => strtoupper(Str::substr($word, 0, 1)))
            ->join('');

        // Jika kurang dari 4 huruf, ambil huruf tambahan dari nama pertama
        if (strlen($initial) < 4) {
            $firstWord = strtoupper(Str::replace(' ', '', $this->name));
            $initial = strtoupper(Str::substr($firstWord, 0, 4));
        }

        // Jika lebih dari 4 huruf, potong jadi 4
        $code = Str::upper(Str::substr($initial, 0, 4));

        // Pastikan unik di database
        $original = $code;
        $counter = 1;
        while (Company::where('code', $code)->exists()) {
            // Tambahkan angka jika bentrok, potong supaya tetap 4
            $suffix = (string) $counter++;
            $code = Str::substr($original, 0, 4 - strlen($suffix)).$suffix;
        }

        $this->code = $code;
    }

    public function save()
    {
        // Adjust validation rules for update (unique code should ignore current record)
        $rules = $this->rules;
        if ($this->companyId) {
            $rules['code'] = 'required|string|max:10|unique:companies,code,'.$this->companyId.',id';
        }

        $this->validate($rules);

        // Optional image upload handling
        $imagePath = null;
        if ($this->image instanceof UploadedFile) {
            try {
                $uploader = app(ImageUploadService::class);
                $imagePath = $uploader->upload($this->image, 'images/companies');
            } catch (\Exception $e) {
                $this->error('Upload Gagal', $e->getMessage());

                return;
            }
        }

        // Create or update in one method
        if ($this->companyId) {
            $company = Company::find($this->companyId);
            if (! $company) {
                $this->error('Data Tidak Ditemukan', 'Perusahaan tidak ditemukan.');

                return;
            }

            $company->name = $this->name;
            $company->code = $this->code;
            $company->hq_branch_id = $this->hq_branch_id ?: null;
            $company->tax_id = $this->tax_id ?: null;
            $company->phone = $this->phone ?: null;
            $company->email = $this->email ?: null;
            $company->website = $this->website ?: null;
            $company->is_active = (bool) $this->is_active;

            if ($imagePath && Schema::hasColumn('companies', 'image')) {
                $company->image = $imagePath;
            }

            $company->save();

            $this->success('Berhasil Diperbarui', 'Data perusahaan telah diperbarui.');
            $this->dispatch('company-saved', id: $company->id);
            $this->isEdit = true;
            $this->loadBranches();
        } else {
            $company = Company::create([
                'name' => $this->name,
                'code' => $this->code,
                'hq_branch_id' => $this->hq_branch_id ?: null,
                'tax_id' => $this->tax_id ?: null,
                'phone' => $this->phone ?: null,
                'email' => $this->email ?: null,
                'website' => $this->website ?: null,
                'is_active' => (bool) $this->is_active,
            ]);

            // Hubungkan otomatis user pembuat ke perusahaan via pivot UserCompany
            $userId = Auth::id();
            if ($userId) {
                UserCompany::firstOrCreate([
                    'user_id' => $userId,
                    'company_id' => $company->id,
                ]);
            }

            if ($imagePath && Schema::hasColumn('companies', 'image')) {
                $company->image = $imagePath;
                $company->save();
            }

            $this->companyId = $company->id;
            $this->isEdit = true;
            $this->success('Berhasil Ditambahkan', 'Perusahaan baru telah dibuat.');
            $this->dispatch('company-saved', id: $company->id);
            $this->loadBranches();
        }
    }

    public function render()
    {
        return view('livewire.companies.form');
    }
}

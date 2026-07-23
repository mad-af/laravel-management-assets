# Database Schema - Laravel Management Assets

> Dokumentasi schema database untuk project Asset Management System
> PT. Gaya Sukses Mandiri Kaseindo

**Generated:** 2026-07-12
**Database Driver:** SQLite (default), supports MySQL/PostgreSQL
**Total Tables:** 28

---

## Table of Contents

1. [Overview](#overview)
2. [Domain: Organization](#domain-organization)
   - [companies](#companies)
   - [branches](#branches)
   - [user_companies](#user_companies)
   - [user_branches](#user_branches)
3. [Domain: HR/Employees](#domain-hremployees)
   - [employees](#employees)
4. [Domain: Assets](#domain-assets)
   - [assets](#assets)
   - [categories](#categories)
   - [asset_logs](#asset_logs)
   - [asset_maintenances](#asset_maintenances)
   - [asset_loans](#asset_loans)
   - [asset_transfers](#asset_transfers)
   - [asset_transfer_items](#asset_transfer_items)
   - [asset_branch_history](#asset_branch_history)
5. [Domain: Vehicles](#domain-vehicles)
   - [vehicle_profiles](#vehicle_profiles)
   - [vehicle_odometer_logs](#vehicle_odometer_logs)
   - [vehicle_tax_types](#vehicle_tax_types)
   - [vehicle_tax_histories](#vehicle_tax_histories)
6. [Domain: Insurance](#domain-insurance)
   - [insurances](#insurances)
   - [insurance_policies](#insurance_policies)
   - [insurance_claims](#insurance_claims)
7. [Domain: Feedback](#domain-feedback)
   - [feedback](#feedback)
8. [Domain: Infrastructure](#domain-infrastructure)
   - [users](#users)
   - [sessions](#sessions)
   - [cache](#cache)
   - [cache_locks](#cache_locks)
   - [jobs](#jobs)
   - [job_batches](#job_batches)
   - [failed_jobs](#failed_jobs)
9. [ER Diagram](#er-diagram)

---

## Overview

### Statistics

| Metric | Value |
|--------|-------|
| Total Tables | 28 |
| Business Domain Tables | 21 |
| Infrastructure Tables | 7 |
| Total Relationships | 94 |

### Domain Distribution

| Domain | Tables |
|--------|--------|
| Organization | 4 tables (companies, branches, user_companies, user_branches) |
| HR/Employees | 1 table (employees) |
| Assets | 8 tables (assets, categories, asset_logs, asset_maintenances, asset_loans, asset_transfers, asset_transfer_items, asset_branch_history) |
| Vehicles | 4 tables (vehicle_profiles, vehicle_odometer_logs, vehicle_tax_types, vehicle_tax_histories) |
| Insurance | 3 tables (insurances, insurance_policies, insurance_claims) |
| Feedback | 1 table (feedback) |
| Infrastructure | 7 tables (users, sessions, cache, cache_locks, jobs, job_batches, failed_jobs) |

---

## Domain: Organization

### companies

Perusahaan dalam sistem. Supports multi-tenant dengan multiple branches.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| name | string | No | - | Unique |
| code | string | No | - | Unique |
| hq_branch_id | uuid | Yes | NULL | - |
| tax_id | string | Yes | NULL | - |
| phone | string | Yes | NULL | - |
| email | string | Yes | NULL | - |
| website | string | Yes | NULL | - |
| is_active | boolean | No | true | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Company Model):**
```php
users(): HasMany(User::class)
branches(): HasMany(Branch::class)
employees(): HasMany(Employee::class)
hqBranch(): BelongsTo(Branch::class, 'hq_branch_id')
userCompanies(): HasMany(UserCompany::class)
```

---

### branches

Cabang perusahaan. Setiap branch milik satu company.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| company_id | uuid | No | - | Foreign Key → companies(id), ON DELETE CASCADE |
| name | string | No | - | - |
| address | text | Yes | NULL | - |
| is_active | boolean | No | true | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Branch Model):**
```php
company(): BelongsTo(Company::class)
assets(): HasMany(Asset::class)
employees(): HasMany(Employee::class)
assetBranchHistories(): HasMany(AssetBranchHistory::class, 'to_branch_id')
fromBranchHistories(): HasMany(AssetBranchHistory::class, 'from_branch_id')
assetTransfersFrom(): HasMany(AssetTransfer::class, 'from_branch_id')
assetTransfersTo(): HasMany(AssetTransfer::class, 'to_branch_id')
userBranches(): HasMany(UserBranch::class)
users(): BelongsToMany(User::class, 'user_branches', 'branch_id', 'user_id')
```

---

### user_companies

Pivot table untuk relasi many-to-many antara users dan companies.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| user_id | uuid | No | - | Foreign Key → users(id), ON DELETE CASCADE |
| company_id | uuid | No | - | Foreign Key → companies(id), ON DELETE CASCADE |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

---

### user_branches

Pivot table untuk relasi many-to-many antara users dan branches.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| user_id | uuid | No | - | Foreign Key → users(id), ON DELETE CASCADE |
| branch_id | uuid | No | - | Foreign Key → branches(id), ON DELETE CASCADE |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

---

## Domain: HR/Employees

### employees

Data karyawan yang dapat dipinjamkan asset.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| company_id | uuid | No | - | Foreign Key → companies(id), ON DELETE CASCADE |
| branch_id | uuid | Yes | NULL | Foreign Key → branches(id), ON DELETE SET NULL |
| employee_number | string | Yes | NULL | Unique |
| full_name | string | No | - | - |
| email | string | Yes | NULL | - |
| phone | string | Yes | NULL | - |
| is_active | boolean | No | true | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Employee Model):**
```php
company(): BelongsTo(Company::class)
branch(): BelongsTo(Branch::class)
assetLoans(): HasMany(AssetLoan::class)
activeLoans(): HasMany(AssetLoan::class) // scope: whereNull('checkin_at')
```

---

## Domain: Assets

### categories

Kategori asset (misal: Elektronik, Kendaraan, Furniture, dll).

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| name | string | No | - | Unique |
| is_active | boolean | No | true | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Category Model):**
```php
assets(): HasMany(Asset::class)
```

---

### assets

Tabel utama asset inventory. Asset bisa berupa kendaraan (memiliki vehicle_profile) atau non-kendaraan.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| company_id | uuid | No | - | Foreign Key → companies(id), ON DELETE CASCADE |
| code | string | No | - | Unique |
| tag_code | string | Yes | NULL | Unique |
| name | string | No | - | - |
| category_id | uuid | No | - | Foreign Key → categories(id), ON DELETE RESTRICT |
| branch_id | uuid | No | - | Foreign Key → branches(id), ON DELETE RESTRICT |
| brand | string(64) | Yes | NULL | - |
| model | string(64) | Yes | NULL | - |
| image | string | Yes | NULL | - |
| status | string(32) | No | 'active' | Values: active, inactive, lost, maintenance, on_loan |
| condition | enum | No | 'good' | Values: good, fair, poor |
| value | decimal(15,2) | No | - | - |
| purchase_date | date | Yes | NULL | - |
| description | text | Yes | NULL | - |
| serial_number | string(128) | Yes | NULL | - |
| last_seen_at | timestamp | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Asset Model):**
```php
category(): BelongsTo(Category::class)
branch(): BelongsTo(Branch::class)
company(): BelongsTo(Company::class)
logs(): HasMany(AssetLog::class)
loans(): HasMany(AssetLoan::class)
currentLoan(): HasOne(AssetLoan::class) // scope: whereNull('checkin_at'), latestOfMany
activeLoans(): HasMany(AssetLoan::class) // scope: whereNull('checkin_at')
branchHistories(): HasMany(AssetBranchHistory::class)
vehicleProfile(): HasOne(VehicleProfile::class, 'asset_id')
vehicleOdometerLogs(): HasMany(VehicleOdometerLog::class)
odometerLogs(): HasMany // alias of vehicleOdometerLogs
maintenances(): HasMany(AssetMaintenance::class)
vehicleTaxTypes(): HasMany(VehicleTaxType::class)
vehicleTaxHistories(): HasMany(VehicleTaxHistory::class)
insurancePolicies(): HasMany(InsurancePolicy::class)
latestActiveInsurancePolicy(): HasOne(InsurancePolicy::class) // ofMany: ['start_date' => 'max']
```

---

### asset_logs

Audit trail untuk semua perubahan asset.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| user_id | uuid | No | - | Foreign Key → users(id), ON DELETE CASCADE |
| action | string | No | - | - |
| changed_fields | json | Yes | NULL | - |
| notes | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (AssetLog Model):**
```php
asset(): BelongsTo(Asset::class)
user(): BelongsTo(User::class)
```

---

### asset_maintenances

Work order untuk perawatan asset. Setiap maintenance record memiliki work order number (code) dengan format: `WO-YYYYMMDD-XXX`.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| code | string | Yes | NULL | Unique (Work Order Number: WO-YYYYMMDD-XXX) |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| employee_id | uuid | Yes | NULL | Foreign Key → employees(id), ON DELETE SET NULL |
| title | string | No | - | - |
| type | enum | No | - | Values: preventive, corrective |
| status | enum | No | 'open' | Values: open, in_progress, completed, cancelled |
| priority | enum | No | 'medium' | Values: low, medium, high |
| started_at | timestamp | Yes | NULL | - |
| estimated_completed_at | timestamp | Yes | NULL | - |
| completed_at | timestamp | Yes | NULL | - |
| cost | decimal(15,2) | No | 0.00 | - |
| technician_name | string | Yes | NULL | - |
| vendor_name | string | Yes | NULL | - |
| notes | text | Yes | NULL | - |
| odometer_km_at_service | integer | Yes | NULL | - |
| next_service_target_odometer_km | integer | Yes | NULL | - |
| next_service_date | date | Yes | NULL | - |
| invoice_no | string | Yes | NULL | - |
| service_tasks | json | Yes | NULL | - |
| service_details | json | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Note:** Ketika maintenance berstatus COMPLETED, `next_service_date` dan `next_service_target_odometer_km` secara otomatis di-propagate ke `vehicle_profiles` table.

**Relationships (AssetMaintenance Model):**
```php
asset(): BelongsTo(Asset::class)
employee(): BelongsTo(Employee::class)
assignedUser(): BelongsTo(User::class, 'technician_name', 'name')
```

---

### asset_loans

Pinjam asset ke karyawan. Support checkout/checkin workflow.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| employee_id | uuid | No | - | Foreign Key → employees(id), ON DELETE CASCADE |
| checkout_at | timestamp | No | - | - |
| due_at | timestamp | No | - | - |
| checkin_at | timestamp | Yes | NULL | - |
| condition_out | enum | No | - | Values: good, fair, poor |
| condition_in | enum | Yes | NULL | Values: good, fair, poor |
| notes | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (AssetLoan Model):**
```php
asset(): BelongsTo(Asset::class)
employee(): BelongsTo(Employee::class)
```

---

### asset_transfers

Transfer asset antar branches. Menggunakan workflow shipped → delivered.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| company_id | uuid | No | - | Foreign Key → companies(id), ON DELETE CASCADE |
| transfer_no | string | No | - | Unique |
| reason | string | Yes | NULL | - |
| status | enum | No | 'shipped' | Values: shipped, delivered |
| type | enum | No | 'branch' | Values: branch, company |
| from_branch_id | uuid | Yes | NULL | Foreign Key → branches(id), ON DELETE SET NULL |
| to_branch_id | uuid | Yes | NULL | Foreign Key → branches(id), ON DELETE SET NULL |
| delivery_by | uuid | No | - | Foreign Key → users(id), ON DELETE RESTRICT |
| accepted_by | uuid | Yes | NULL | Foreign Key → users(id), ON DELETE SET NULL |
| accepted_at | timestamp | Yes | NULL | - |
| delivery_at | timestamp | Yes | NULL | - |
| notes | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (AssetTransfer Model):**
```php
company(): BelongsTo(Company::class)
requestedBy(): BelongsTo(User::class, 'delivery_by')
approvedBy(): BelongsTo(User::class, 'accepted_by')
items(): HasMany(AssetTransferItem::class)
fromBranch(): BelongsTo(Branch::class, 'from_branch_id')
toBranch(): BelongsTo(Branch::class, 'to_branch_id')
```

---

### asset_transfer_items

Item-item yang ditransfer dalam satu asset_transfer.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_transfer_id | uuid | No | - | Foreign Key → asset_transfers(id), ON DELETE CASCADE |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| from_branch_id | uuid | No | - | Foreign Key → branches(id), ON DELETE RESTRICT |
| to_branch_id | uuid | No | - | Foreign Key → branches(id), ON DELETE RESTRICT |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (AssetTransferItem Model):**
```php
assetTransfer(): BelongsTo(AssetTransfer::class)
asset(): BelongsTo(Asset::class)
```

---

### asset_branch_history

History perpindahan asset antar branches untuk audit trail.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| from_branch_id | uuid | Yes | NULL | Foreign Key → branches(id), ON DELETE SET NULL |
| to_branch_id | uuid | No | - | Foreign Key → branches(id), ON DELETE RESTRICT |
| transfer_id | uuid | Yes | NULL | Foreign Key → asset_transfers(id), ON DELETE SET NULL |
| remark | string | Yes | NULL | - |

**Relationships (AssetBranchHistory Model):**
```php
asset(): BelongsTo(Asset::class)
fromBranch(): BelongsTo(Branch::class, 'from_branch_id')
toBranch(): BelongsTo(Branch::class, 'to_branch_id')
transfer(): BelongsTo(AssetTransfer::class, 'transfer_id')
```

---

## Domain: Vehicles

### vehicle_profiles

Data spesifik kendaraan yang extension dari assets. Menggunakan `asset_id` sebagai primary key (one-to-one dengan assets).

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| asset_id | uuid | No | (PK) | Primary Key, Foreign Key → assets(id), ON DELETE CASCADE |
| year_purchase | integer | Yes | NULL | - |
| year_manufacture | integer | Yes | NULL | - |
| current_odometer_km | integer | No | 0 | - |
| last_service_date | date | Yes | NULL | - |
| service_target_odometer_km | integer | Yes | NULL | - |
| next_service_date | date | Yes | NULL | - |
| plate_no | string(32) | Yes | NULL | - |
| vin | string(64) | Yes | NULL | - |
| owner | string(64) | Yes | NULL | - |
| type | enum | No | 'passenger' | Values: passenger, cargo, motorcycle |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Note:** `annual_tax_due_date` column telah dihapus.

**Relationships (VehicleProfile Model):**
```php
asset(): BelongsTo(Asset::class, 'asset_id')
vehicleTaxTypes(): HasMany(VehicleTaxType::class, 'asset_id', 'asset_id')
vehicleTaxHistories(): HasMany(VehicleTaxHistory::class, 'asset_id', 'asset_id')
```

---

### vehicle_odometer_logs

Log pembacaan odometer kendaraan. Source bisa 'manual' atau 'service'.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| odometer_km | integer | No | - | - |
| source | enum | No | - | Values: manual, service |
| notes | string | Yes | NULL | - |
| read_at | timestamp | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (VehicleOdometerLog Model):**
```php
asset(): BelongsTo(Asset::class, 'asset_id')
```

---

### vehicle_tax_types

Jenis pajak kendaraan (PKB Tahunan, KIR, dll).

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| tax_type | string | No | - | - |
| due_date | date | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (VehicleTaxType Model):**
```php
asset(): BelongsTo(Asset::class)
vehicleTaxHistories(): HasMany(VehicleTaxHistory::class)
```

---

### vehicle_tax_histories

History pembayaran pajak kendaraan.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| vehicle_tax_type_id | uuid | No | - | Foreign Key → vehicle_tax_types(id), ON DELETE CASCADE |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| year | integer | No | - | - |
| due_date | date | No | - | - |
| paid_date | date | Yes | NULL | - |
| amount | decimal(15,2) | Yes | NULL | - |
| receipt_no | string(64) | Yes | NULL | - |
| notes | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (VehicleTaxHistory Model):**
```php
vehicleTaxType(): BelongsTo(VehicleTaxType::class)
asset(): BelongsTo(Asset::class)
```

---

## Domain: Insurance

### insurances

Provider asuransi.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| name | string | No | - | - |
| phone | string | Yes | NULL | - |
| email | string | Yes | NULL | - |
| address | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Insurance Model):**
```php
insurancePolicies(): HasMany(InsurancePolicy::class)
```

---

### insurance_policies

Polis asuransi untuk asset.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| insurance_id | uuid | No | - | Foreign Key → insurances(id), ON DELETE CASCADE |
| policy_no | string | No | - | Unique |
| policy_type | enum | No | 'comprehensive' | Values: comprehensive, tlo, tpft, tpo |
| start_date | date | No | - | - |
| end_date | date | No | - | - |
| status | enum | No | 'active' | Values: active, inactive |
| notes | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (InsurancePolicy Model):**
```php
asset(): BelongsTo(Asset::class)
insurance(): BelongsTo(Insurance::class)
claims(): HasMany(InsuranceClaim::class, 'policy_id')
```

---

### insurance_claims

Klaim asuransi. Bisa berasal dari maintenance (source='maintenance') atau manual.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| policy_id | uuid | No | - | Foreign Key → insurance_policies(id), ON DELETE CASCADE |
| asset_id | uuid | No | - | Foreign Key → assets(id), ON DELETE CASCADE |
| claim_no | string | Yes | NULL | Unique |
| incident_date | date | Yes | NULL | - |
| incident_type | enum | Yes | NULL | Values: collision, theft, flood, fire, other |
| incident_other | string | Yes | NULL | - |
| description | text | Yes | NULL | - |
| source | enum | No | 'manual' | Values: manual, maintenance |
| asset_maintenance_id | uuid | Yes | NULL | Foreign Key → asset_maintenances(id), ON DELETE SET NULL |
| status | enum | No | 'draft' | Values: draft, submitted, approved, rejected |
| claim_documents | json | Yes | NULL | - |
| amount_approved | decimal(15,2) | Yes | NULL | - |
| amount_paid | decimal(15,2) | Yes | NULL | - |
| created_by | uuid | No | - | Foreign Key → users(id), ON DELETE RESTRICT |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (InsuranceClaim Model):**
```php
policy(): BelongsTo(InsurancePolicy::class, 'policy_id')
asset(): BelongsTo(Asset::class)
maintenance(): BelongsTo(AssetMaintenance::class, 'asset_maintenance_id')
createdBy(): BelongsTo(User::class, 'created_by')
```

---

## Domain: Feedback

### feedback

User feedback/rating system.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | bigint | No | (PK) | Primary Key, Auto Increment |
| user_id | uuid | No | - | Foreign Key → users(id), ON DELETE CASCADE |
| period | string(32) | Yes | NULL | Unique (composite with user_id) |
| rating | unsignedTinyInteger | No | - | - |
| message | text | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (Feedback Model):**
```php
user(): BelongsTo(User::class)
```

---

## Domain: Infrastructure

### users

User authentication dengan role-based access (admin, staff, auditor).

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | uuid | No | (PK) | Primary Key |
| name | string | No | - | - |
| email | string | No | - | Unique |
| email_verified_at | timestamp | Yes | NULL | - |
| role | enum | No | 'staff' | Values: admin, staff, auditor |
| password | string | No | - | - |
| remember_token | string | Yes | NULL | - |
| created_at | timestamp | No | - | - |
| updated_at | timestamp | No | - | - |

**Relationships (User Model):**
```php
assetLogs(): HasMany(AssetLog::class)
logs(): HasMany(AssetLog::class) // alias of assetLogs
company(): BelongsTo(Company::class)
userCompanies(): HasMany(UserCompany::class)
companies(): BelongsToMany(Company::class, 'user_companies', 'user_id', 'company_id')
userBranches(): HasMany(UserBranch::class)
branches(): BelongsToMany(Branch::class, 'user_branches', 'user_id', 'branch_id')
```

---

### sessions

Laravel session storage.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | string | No | (PK) | Primary Key |
| user_id | foreignUuid | Yes | NULL | Foreign Key → users(id), Indexed |
| ip_address | string(45) | Yes | NULL | - |
| user_agent | text | Yes | NULL | - |
| payload | longText | No | - | - |
| last_activity | integer | No | - | Indexed |

---

### cache

Laravel cache storage.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| key | string | No | (PK) | Primary Key |
| value | mediumText | No | - | - |
| expiration | integer | No | - | - |

---

### cache_locks

Laravel cache locks untuk distributed locking.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| key | string | No | (PK) | Primary Key |
| owner | string | No | - | - |
| expiration | integer | No | - | - |

---

### jobs

Laravel job queue.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | bigint | No | (PK) | Primary Key, Auto Increment |
| queue | string | No | - | Indexed |
| payload | longText | No | - | - |
| attempts | unsignedTinyInteger | No | - | - |
| reserved_at | unsignedInteger | Yes | NULL | - |
| available_at | unsignedInteger | No | - | - |
| created_at | unsignedInteger | No | - | - |

---

### job_batches

Laravel job batching.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | string | No | (PK) | Primary Key |
| name | string | No | - | - |
| total_jobs | integer | No | - | - |
| pending_jobs | integer | No | - | - |
| failed_jobs | integer | No | - | - |
| failed_job_ids | longText | No | - | - |
| options | mediumText | Yes | NULL | - |
| cancelled_at | integer | Yes | NULL | - |
| created_at | integer | No | - | - |
| finished_at | integer | Yes | NULL | - |

---

### failed_jobs

Laravel failed job logging.

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | bigint | No | (PK) | Primary Key, Auto Increment |
| uuid | string | No | - | Unique |
| connection | text | No | - | - |
| queue | text | No | - | - |
| payload | longText | No | - | - |
| exception | longText | No | - | - |
| failed_at | timestamp | No | CURRENT_TIMESTAMP | - |

---

## ER Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              ORGANIZATION DOMAIN                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌──────────────┐         ┌──────────────┐         ┌──────────────────┐  │
│   │   companies  │◄───────│   branches   │─────────►│ user_branches    │  │
│   │              │  1:N    │              │   1:N    │ (pivot)          │  │
│   │ - id (PK)    │         │ - id (PK)    │         │ - user_id (FK)   │  │
│   │ - name       │         │ - company_id │         │ - branch_id (FK) │  │
│   │ - code       │         │ - name       │         └────────┬─────────┘  │
│   │ - hq_branch  │         │ - address    │                  │           │
│   └──────┬───────┘         └──────┬───────┘                  │           │
│          │                        │                           │           │
│          │              ┌──────────┴──────────┐               │           │
│          │              │                     │               │           │
│          ▼              ▼                       ▼               ▼           │
│   ┌──────────────┐         ┌──────────────┐         ┌──────────────────┐  │
│   │ user_companies│         │  employees   │         │      users       │  │
│   │ (pivot)       │         │              │         │                  │  │
│   │ - user_id (FK)│        │ - id (PK)     │         │ - id (PK)        │  │
│   │ - company_id  │        │ - company_id  │         │ - name           │  │
│   └──────────────┘         │ - branch_id  │         │ - email          │  │
│                             │ - full_name   │         │ - role           │  │
│                             └───────┬───────┘         └──────────────────┘  │
│                                     │                                          │
└─────────────────────────────────────│──────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                               ASSETS DOMAIN                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌──────────────┐         ┌──────────────┐         ┌──────────────────┐     │
│   │ categories   │◄───────│    assets    │────────►│ vehicle_profiles │     │
│   │              │  1:N   │              │   1:1   │ (one-to-one)     │     │
│   │ - id (PK)    │        │ - id (PK)    │         │ - asset_id (PK)  │     │
│   │ - name       │        │ - code       │         │ - odometer_km    │     │
│   └──────────────┘        │ - name       │         │ - next_service   │     │
│                           │ - category_id │         │ - plate_no       │     │
│                           │ - branch_id   │         └────────┬─────────┘     │
│                           │ - status      │                  │              │
│                           └───────┬───────┘                  │              │
│                                   │                          │              │
│           ┌───────────────────────┼───────────────────────────┘              │
│           │                       │                                          │
│           ▼                       ▼                                          │
│   ┌───────────────────────────────────────────────┐                           │
│   │           ASSET OPERATIONS (1:N each)         │                           │
│   ├───────────────────────────────────────────────┤                           │
│   │  asset_logs       │  asset_maintenances       │                           │
│   │  asset_loans      │  vehicle_odometer_logs    │                           │
│   │  asset_transfers  │  vehicle_tax_types        │                           │
│   │  (via items)      │  insurance_policies       │                           │
│   └───────────────────────────────────────────────┘                           │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              VEHICLE DOMAIN                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌───────────────────┐         ┌──────────────────────┐                     │
│   │vehicle_profiles   │◄────────│ vehicle_odometer_logs│                     │
│   │ (asset_id PK,FK)  │   1:N  │                      │                     │
│   └─────────┬─────────┘         └──────────────────────┘                     │
│             │                                                               │
│             │   ┌──────────────────────────────────────────┐                │
│             ├──►│ vehicle_tax_types                        │                │
│             │   │ - id (PK)                                │                │
│             │   │ - asset_id (FK)                          │                │
│             │   │ - tax_type                               │                │
│             │   │ - due_date                               │                │
│             │   └───────────────┬──────────────────────────┘                │
│             │                   │ 1:N                                        │
│             │                   ▼                                            │
│             │   ┌──────────────────────────────────────────┐                │
│             └──►│ vehicle_tax_histories                    │                │
│                 │ - id (PK)                                │                │
│                 │ - vehicle_tax_type_id (FK)                │                │
│                 │ - asset_id (FK)                           │                │
│                 │ - year, due_date, paid_date               │                │
│                 │ - amount, receipt_no                      │                │
│                 └──────────────────────────────────────────┘                │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                             INSURANCE DOMAIN                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌──────────────┐         ┌──────────────────────┐                         │
│   │  insurances  │◄───────│  insurance_policies  │                         │
│   │              │  1:N   │                      │   1:N                    │
│   │ - id (PK)    │        │ - id (PK)            │────────► ┌─────────────┐ │
│   │ - name       │        │ - asset_id (FK)      │          │insurance_   │ │
│   │ - phone      │        │ - insurance_id (FK)  │          │claims       │ │
│   └──────────────┘        │ - policy_no          │          │             │ │
│                           │ - policy_type        │          │ - id (PK)   │ │
│                           │ - start_date         │          │ - policy_id │ │
│                           │ - end_date           │          │ - asset_id  │ │
│                           │ - status             │          │ - status    │ │
│                           └──────────────────────┘          └─────────────┘ │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                           INFRASTRUCTURE DOMAIN                             │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                   │
│   │    users     │    │   sessions   │    │    cache     │                   │
│   │              │    │              │    │              │                   │
│   │ - id (PK)    │    │ - id (PK)    │    │ - key (PK)   │                   │
│   │ - name       │    │ - user_id    │    │ - value      │                   │
│   │ - email      │    │ - payload    │    │ - expiration │                   │
│   │ - role       │    │ - last_act   │    └──────────────┘                   │
│   └──────────────┘    └──────────────┘                                      │
│                                                                              │
│   ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                   │
│   │     jobs     │    │  job_batches │    │ failed_jobs  │                   │
│   │              │    │              │    │              │                   │
│   │ - id (PK)    │    │ - id (PK)    │    │ - id (PK)    │                   │
│   │ - queue      │    │ - name       │    │ - uuid       │                   │
│   │ - payload    │    │ - total_jobs │    │ - connection │                   │
│   │ - attempts   │    │ - failed_    │    │ - exception  │                   │
│   └──────────────┘    └──────────────┘    └──────────────┘                   │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Relationship Summary

| Relationship Type | Count |
|-------------------|-------|
| HasMany | 47 |
| BelongsTo | 40 |
| BelongsToMany | 4 |
| HasOne | 3 |
| **Total** | **94** |

---

## Key Flows

### 1. Asset Maintenance → Next Service Update
```
1. User creates AssetMaintenance (Form)
2. When maintenance is COMPLETED:
   - AssetMaintenance::booted() observer fires
   - If asset has vehicleProfile:
     → next_service_date → vehicleProfiles.next_service_date
     → next_service_target_odometer_km → vehicleProfiles.service_target_odometer_km
     → completed_at → vehicleProfiles.last_service_date
```

### 2. Asset Transfer Workflow
```
1. AssetTransfer created (status: 'shipped')
2. AssetTransferItems created (link to assets)
3. AssetBranchHistory records created
4. When delivered (status: 'delivered'):
   → accepted_by, accepted_at, delivery_at updated
   → Asset's branch_id updated to to_branch_id
```

### 3. Insurance Claim from Maintenance
```
1. AssetMaintenance completed
2. User creates InsuranceClaim (source: 'maintenance')
3. claim_no auto-generated
4. Claim follows workflow: draft → submitted → approved/rejected
```

---

*Document generated from Laravel migrations and Eloquent models*

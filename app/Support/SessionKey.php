<?php

namespace App\Support;

enum SessionKey: string
{
    case BranchId = 'ctx.branch_id';   // pakai dot biar rapi namespace
    case CompanyId = 'ctx.company_id'; // untuk menyimpan company_id yang terpilih
    case Theme    = 'ctx.theme';
}
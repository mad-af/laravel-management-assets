<?php

namespace App\Support;

enum SessionKey: string
{
    case BranchId = 'ctx.branch_id';   // pakai dot biar rapi namespace
    case Theme    = 'ctx.theme';
}
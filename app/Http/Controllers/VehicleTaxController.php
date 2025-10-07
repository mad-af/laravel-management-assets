<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('dashboard.vehicle-taxes.index');
    }
  
}

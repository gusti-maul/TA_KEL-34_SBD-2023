<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Count incoming letters
        $masuk = DB::table('letters')
            ->where('letter_type', 'Surat Masuk')
            ->count();
    
        // Count outgoing letters
        $keluar = DB::table('letters')
            ->where('letter_type', 'Surat Keluar')
            ->count();
    
        return view('pages.admin.dashboard', [
            'masuk' => $masuk,
            'keluar' => $keluar
        ]);
    }
    
}

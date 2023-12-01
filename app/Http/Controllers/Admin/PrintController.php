<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
       public function index()
    {
        $items = DB::table('letters')
            ->join('departments', 'letters.department_id', '=', 'departments.id')
            ->join('senders', 'letters.sender_id', '=', 'senders.id')
            ->where('letter_type', 'Surat Masuk')
            ->orderBy('letters.created_at', 'desc')
            ->select(
                'letters.*',
                'departments.name as department_name',
                'senders.name as sender_name'
            )
            ->get();

        return view('pages.admin.letter.print-incoming', [
            'items' => $items
        ]);
    }

    public function outgoing()
    {
        $items = DB::table('letters')
            ->join('departments', 'letters.department_id', '=', 'departments.id')
            ->join('senders', 'letters.sender_id', '=', 'senders.id')
            ->where('letter_type', 'Surat Keluar')
            ->orderBy('letters.created_at', 'desc')
            ->select(
                'letters.*',
                'departments.name as department_name',
                'senders.name as sender_name'
            )
            ->get();

        return view('pages.admin.letter.print-outgoing', [
            'items' => $items
        ]);
    }
}

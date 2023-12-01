<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class LetterController extends Controller
{
    public function index()
    {
    //     if (request()->ajax()) {
    //         $query = DB::table('letters')
    //             ->with('department')
    //             ->select('letters.*', 'departments.name as department_name', 'senders.name as sender_name')
    //             ->join('departments', 'departments.id', '=', 'letters.department_id')
    //             ->join('senders', 'senders.id', '=', 'letters.sender_id')
    //             ->orderBy('letters.created_at', 'desc');

    //         return DataTables::of($query)
    //             ->addIndexColumn()
    //             ->addColumn('department_name', function ($item) {
    //                 return $item->department->name; // Access the 'name' property of the associated department
    //             })
    //             ->addColumn('action', function ($item) {
    //                 return '
    //                     <a href="' . route('detail-surat', $item->id) . '" class="btn btn-success btn-xs">Detail</a>
    //                     <a href="' . route('letter.edit', $item->id) . '" class="btn btn-warning btn-xs">Ubah</a>
    //                     <a href="' . route('letter.destroy', $item->id) . '" method="POST" onsubmit="return confirm(\'Anda akan menghapus data ini?\')">
    //                         ' . method_field('delete') . csrf_field() . '
    //                         <button class="btn btn-danger btn-xs">Hapus</button>
    //                     </a>
    //                 ';
    //             })
    //             ->rawColumns(['department_name', 'action'])
    //             ->make(true);
    //     }

    //     return view('pages.admin.letter.index');
    // 
    }

    public function create()
    {
        $departments = DB::table('departments')->get();
        $sender = DB::table('senders')->get();

        return view('pages.admin.letter.create', [
            'departments' => $departments,
            'sender' => $sender,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'letter_no' => 'required',
            'letter_date' => 'required',
            'date_received' => 'required',
            'regarding' => 'required',
            'department_id' => 'required',
            'sender_id' => 'required',
            'letter_file' => 'required|mimes:pdf|file',
            'letter_type' => 'required',
        ]);

        if ($request->file('letter_file')) {
            $validatedData['letter_file'] = $request->file('letter_file')->store('assets/letter-file');
        }

        $redirect = ($validatedData['letter_type'] == 'Surat Masuk') ? 'surat-masuk' : 'surat-keluar';

        DB::table('letters')->insert($validatedData);

        return redirect()
            ->route($redirect)
            ->with('success', 'Sukses! 1 Data Berhasil Disimpan');
    }

        public function incoming_mail()
    {
        if (request()->ajax()) {
            $query = DB::table('letters')
                ->join('departments', 'letters.department_id', '=', 'departments.id')
                ->join('senders', 'letters.sender_id', '=', 'senders.id')
                ->where('letter_type', 'Surat Masuk')
                ->orderBy('letters.created_at', 'desc')
                ->select(
                    'letters.*',
                    'departments.name as department.name',
                    'senders.name as sender.name'
                )
                ->get();

            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <a class="btn btn-success btn-xs" href="' . route('detail-surat', $item->id) . '">
                            <i class="fa fa-search-plus"></i> &nbsp; Detail
                        </a>
                        <a class="btn btn-primary btn-xs" href="' . route('letter.edit', $item->id) . '">
                            <i class="fas fa-edit"></i> &nbsp; Ubah
                        </a>
                        <form action="' . route('letter.destroy', $item->id) . '" method="POST" onsubmit="return confirm('."'Anda akan menghapus item ini dari situs anda?'".')">
                            ' . method_field('delete') . csrf_field() . '
                            <button class="btn btn-danger btn-xs">
                                <i class="far fa-trash-alt"></i> &nbsp; Hapus
                            </button>
                        </form>
                    ';
                })
                ->addIndexColumn()
                ->removeColumn('id')
                ->rawColumns(['action', 'post_status'])
                ->make();
        }

        return view('pages.admin.letter.incoming');
    }


    public function outgoing_mail()
    {
        if (request()->ajax()) {
            $query = DB::table('letters')
                ->join('departments', 'letters.department_id', '=', 'departments.id')
                ->join('senders', 'letters.sender_id', '=', 'senders.id')
                ->where('letter_type', 'Surat Keluar')
                ->orderBy('letters.created_at', 'desc')
                ->select(
                    'letters.*',
                    'departments.name as department.name',
                    'senders.name as sender.name'
                )
                ->get();

            return Datatables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <a class="btn btn-success btn-xs" href="' . route('detail-surat', $item->id) . '">
                            <i class="fa fa-search-plus"></i> &nbsp; Detail
                        </a>
                        <a class="btn btn-primary btn-xs" href="' . route('letter.edit', $item->id) . '">
                            <i class="fas fa-edit"></i> &nbsp; Ubah
                        </a>
                        <form action="' . route('letter.destroy', $item->id) . '" method="POST" onsubmit="return confirm('."'Anda akan menghapus item ini dari situs anda?'".')">
                            ' . method_field('delete') . csrf_field() . '
                            <button class="btn btn-danger btn-xs">
                                <i class="far fa-trash-alt"></i> &nbsp; Hapus
                            </button>
                        </form>
                    ';
                })
                ->addIndexColumn()
                ->removeColumn('id')
                ->rawColumns(['action', 'post_status'])
                ->make();
        }

        return view('pages.admin.letter.outgoing');
    }
    
    public function show($id)
    {
        $item = DB::table('letters')
            ->select('letters.*', 'departments.name as department_name', 'senders.name as sender_name')
            ->join('departments', 'departments.id', '=', 'letters.department_id')
            ->join('senders', 'senders.id', '=', 'letters.sender_id')
            ->where('letters.id', '=', $id)
            ->first();

        return view('pages.admin.letter.show', [
            'item' => $item,
        ]);
    }

    
    public function edit($id)
    {
        $departments = DB::table('departments')->get();
        $sender = DB::table('senders')->get();
    
        $item = DB::table('letters')
            ->where('letters.id', '=', $id)
            ->first();
    
        return view('pages.admin.letter.edit', [
            'departments' => $departments,
            'senders' => $sender,
            'item' => $item,
        ]);
    }
    
    public function download_letter($id)
    {
        $letter = DB::table('letters')
            ->where('letters.id', '=', $id)
            ->first();
    
        return Storage::download($letter->letter_file);
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'letter_no' => 'required',
            'letter_date' => 'required',
            'date_received' => 'required',
            'regarding' => 'required',
            'department_id' => 'required',
            'sender_id' => 'required',
            'letter_file' => 'mimes:pdf|file',
            'letter_type' => 'required',
        ]);
    
        $item = $request->file('letter_file');
        if ($item) {
            $validatedData['letter_file'] = $item->store('assets/letter-file');
        }

        $redirect = ($validatedData['letter_type'] == 'Surat Masuk') ? 'surat-masuk' : 'surat-keluar';
    
        DB::table('letters')
            ->where('letters.id', '=', $id)
            ->update($validatedData);
    
        return redirect()
            ->route($redirect)
            ->with('success', 'Sukses! 1 Data Berhasil Diubah');
    }
    
    public function destroy($id)
    {
        $item = DB::table('letters')->where('id', $id)->first();

        if (!$item) {
            // Item not found, handle accordingly (redirect, show error, etc.)
        }

        if ($item->letter_type == 'Surat Masuk') {
            $redirect = 'surat-masuk';
        } else {
            $redirect = 'surat-keluar';
        }

        // Hapus file surat
        if ($item->letter_file) {
            Storage::delete($item->letter_file);
        }

        DB::table('letters')->where('id', $id)->delete();

        return redirect()
            ->route($redirect)
            ->with('success', 'Sukses! 1 Data Berhasil Dihapus');
    }


}
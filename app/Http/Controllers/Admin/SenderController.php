<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SenderController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            return DataTables::of(DB::table('senders')->latest()->get())

                ->addColumn('action', function ($item) {
                    return '
                        <button class="btn btn-success btn-xs" data-bs-toggle="modal" data-bs-target="#updateModal' . $item->id . '">
                            <i class="fas fa-edit"></i> &nbsp; Ubah
                        </button>
                        <form action="' . route('sender.destroy', $item->id) . '" method="POST" onsubmit="return confirm(\'Anda akan menghapus data ini?\')">
                            ' . method_field('delete') . csrf_field() . '
                            <button class="btn btn-danger btn-xs">
                                <i class="far fa-trash-alt"></i> &nbsp; Hapus
                            </button>
                        </form>
                    ';
                })
                ->addIndexColumn()
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $senders = DB::table('senders')->get();

        return view('pages.admin.sender.index', [
            'senders' => $senders
        ]);

    }

    public function create()
    {
        return view('pages.admin.sender.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        DB::table('senders')->insert([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
        ]);

        return redirect()->route('sender.index')->with('success', 'Sukses! Data pengirim berhasil ditambahkan.');
    }

    public function show($id)
    {
        $sender = DB::table('senders')->where('id', $id)->first();

        if (!$sender) {
            return redirect()->route('sender.index')->with('error', 'Data pengirim tidak ditemukan.');
        }

        return view('pages.admin.sender.show', [
            'sender' => $sender
        ]);
    }

    public function edit($id)
    {
        $sender = DB::table('senders')->where('id', $id)->first();

        if (!$sender) {
            return redirect()->route('sender.index')->with('error', 'Data pengirim tidak ditemukan.');
        }

        return view('pages.admin.sender.edit', [
            'sender' => $sender
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        DB::table('senders')->where('id', $id)->update([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
        ]);

        return redirect()->route('sender.index')->with('success', 'Sukses! Data pengirim berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('senders')->where('id', $id)->delete();

        return redirect()->route('sender.index')->with('success', 'Sukses! Data pengirim berhasil dihapus.');
    }
}

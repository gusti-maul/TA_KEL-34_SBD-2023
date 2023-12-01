<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $query = DB::table('departments')
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    return '
                        <button class="btn btn-success btn-xs" data-bs-toggle="modal" data-bs-target="#updateModal' . $item->id . '">
                            <i class="fas fa-edit"></i> &nbsp; Ubah
                        </button>
                        <form action="' . route('department.destroy', $item->id) . '" method="POST" onsubmit="return confirm(\'Anda akan menghapus data ini dari situs Anda?\')">
                            ' . method_field('delete') . csrf_field() . '
                            <button class="btn btn-danger btn-xs">
                                <i class="far fa-trash-alt"></i> &nbsp; Hapus
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Change the variable name to $department
        $departments = DB::table('departments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.admin.department.index', [
            'department' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $name = $request->input('name');

        DB::table('departments')
            ->insert([
                'name' => $name
            ]);

        return redirect()
            ->route('department.index')
            ->with('success', 'Sukses! Data berhasil ditambahkan.');
    }
    
    public function create()
    {
        //
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        DB::table('departments')
            ->where('id', $id)
            ->update([
                'name' => $name
            ]);

        return redirect()
            ->route('department.index')
            ->with('success', 'Sukses! Data berhasil diubah.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $departments = DB::table('departments')->where('id', $id)->first();
    
        return redirect()
            ->route('department.edit');
    }
    
    public function destroy($id)
    {
        DB::table('departments')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('department.index')
            ->with('success', 'Sukses! Data berhasil dihapus.');
    }
}

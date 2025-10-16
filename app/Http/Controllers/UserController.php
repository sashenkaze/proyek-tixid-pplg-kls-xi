<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Str;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    // ====== USER REGISTRASI / LOGIN ======
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:8'
        ]);

        $createuser = User::create([
            'name' => $request->first_name . " " . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        if ($createuser) {
            return redirect()->route('login')->with('ok', 'Berhasil membuat akun! Silakan login');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silakan coba lagi');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $data = $request->only(['email', 'password']);

        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Login berhasil! ');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.promos.index')->with('login', 'Login berhasil!');
            } else {
                return redirect()->route('home')->with('success', 'Login berhasil! ');
            }
        } else {
            return redirect()->back()->with('error', 'Login gagal! Silakan coba lagi');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Berhasil logout!');
    }

    // ====== ADMIN CRUD USERS / STAFF ======
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    // ganti nama store jadi storeAdmin biar gak bentrok sama signup
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'staff',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Staff berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diubah!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil dihapus!');
    }

    public function exportExcel()
    {
        $file_name = 'data-user.xlsx';
        return Excel::download(new UserExport, $file_name);
    }

    public function trash()
    {
        // onlyTrashed() : filter data yang sudah di hps, yg deleted_at di phpmyadmin nya ada isi tanggal, hanya filter tetap gunakan get()/first() untuk ambilnya
        $users = User::onlyTrashed()->get();
        return view('admin.users.trash', compact('users'));
    }

    public function restore($id)
    {
        // sebelum dicari, difilter dulu diakses hanya yg sudah diihapus
        $user = User::onlyTrashed()->find($id);
        // restore() : mengembalikan data yg sudah dihapus
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Data berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        // forceDelete() : menghapus permanen dari db
        $user->forceDelete();
        return redirect()->route('admin.users.index')->with('success', 'Data berhasil dihapus permanen!');
    }

    public function dataForDatatables()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return DataTables::of($users)->addIndexColumn()
            ->addColumn('roleBadge', function ($user) {
                if ($user->role === 'admin' ) {
                    return '<span class="badge badge-info">' . e( $user->role ) . '</span>';
                } else {
                    return '<span class="badge badge-success">' . e( $user->role ) . '</span>';
                }
            })
            ->addColumn('buttons', function ($data) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $data['id']) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('admin.cinemas.destroy', $data['id']) . '" method="POST" class="m-0">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
                return '
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex gap-2" style="width: 100%; max-width: 250px;">
                            ' . $btnDelete . $btnEdit . '
                        </div>
                    </div>
        ';
            })
            ->rawColumns(['roleBadge', 'buttons'])
            ->make(true);
    }
}

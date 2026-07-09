<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $pesans = Pesan::orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        return view('admin.pesan.index', compact('pesans', 'perPage'));
    }

    public function markAsRead(Pesan $pesan)
    {
        if ($pesan->read_at === null) {
            $pesan->update(['read_at' => now()]);
        }

        return redirect()->route('admin.pesan.index')->with('success', 'Pesan ditandai sudah dibaca.');
    }

    public function destroy(Pesan $pesan)
    {
        $pesan->delete();
        return redirect()->route('admin.pesan.index')->with('success', 'Pesan berhasil dihapus');
    }
}

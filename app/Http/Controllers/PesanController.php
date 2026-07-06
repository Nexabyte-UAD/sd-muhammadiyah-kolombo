<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function index()
    {
        $pesans = Pesan::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pesan.index', compact('pesans'));
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

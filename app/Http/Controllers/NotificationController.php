<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NotificationsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class NotificationController extends Controller
{
    /**
     * Query dasar untuk mengambil notifikasi yang sesuai dengan role user
     * Notifikasi bisa memiliki target_role: null (semua), 'all', atau spesifik seperti 'admin'
     */
    private function baseQuery($user)
    {
        return Notification::where(function ($q) use ($user) {
            $q->whereNull('target_role')           // Notifikasi tanpa target_role (untuk semua)
              ->orWhere('target_role', 'all')      // Notifikasi untuk semua role
              ->orWhere('target_role', $user->role); // Notifikasi khusus role user
        });
    }

    /**
     * Menampilkan halaman daftar notifikasi dengan filter status (read/unread) dan tipe notifikasi
     */
    public function index(Request $request)
    {
        // Ambil user yang sedang login (guard default, biasanya web)
        $user = auth()->user();
    
        $query = \App\Models\Notification::query();
    
        // FILTER STATUS (read / unread)
        $status = $request->input('status');
        if ($status === 'read') {
            // Notifikasi yang sudah dibaca oleh user ini (id user ada di kolom JSON read_by)
            $query->whereJsonContains('read_by', $user->id);
        } elseif ($status === 'unread') {
            // Notifikasi yang belum dibaca: read_by null ATAU tidak mengandung id user
            $query->where(function ($q) use ($user) {
                $q->whereNull('read_by')
                  ->orWhereJsonDoesntContain('read_by', $user->id);
            });
        }
    
        // FILTER TYPE (misal: barang, lantai, ruangan, dll)
        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
    
        // FILTER TARGET ROLE (hanya notifikasi yang diperuntukkan bagi role user)
        $query->where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    
        // Ambil SEMUA notifikasi (tidak dipaginasi) dan urutkan dari terbaru
        $notifications = $query->latest()->get()->map(function($notif) {
            // Tambahkan atribut created_at_human untuk tampilan (contoh: "2 jam yang lalu")
            $notif->created_at_human = $notif->created_at->diffForHumans();
            return $notif;
        });
    
        // Tampilkan view notifikasi dengan data notifikasi dan user
        return view('notifications.index', compact('notifications', 'user'));
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca oleh user yang sedang login
     */
    public function markAsRead($id)
    {
        // Ambil user dari guard 'stafaset' (karena di aplikasi pakai guard khusus)
        $user = Auth::guard('stafaset')->user();

        // Cari notifikasi berdasarkan ID
        $notification = Notification::findOrFail($id);
        // Panggil method markReadBy pada model Notification (menambahkan id user ke kolom read_by JSON)
        $notification->markReadBy($user->id);

        // Kembali ke halaman sebelumnya
        return back();
    }

    /**
     * Endpoint API untuk mendapatkan jumlah notifikasi yang belum dibaca secara realtime (AJAX)
     */
    public function realtime()
    {
        // Ambil user yang login (guard stafaset)
        $user = Auth::guard('stafaset')->user();

        // Hitung notifikasi yang memenuhi baseQuery dan belum dibaca oleh user ini
        $unread = $this->baseQuery($user)
            ->get()
            ->filter(fn($n) => !$n->isReadBy($user->id))
            ->count();

        // Kembalikan response JSON { unread: jumlah }
        return response()->json([
            'unread' => $unread
        ]);
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
    
        $query = Notification::query();
    
        // Filter status
        $status = $request->input('status');
        if ($status === 'read') {
            $query->whereJsonContains('read_by', $user->id);
        } elseif ($status === 'unread') {
            $query->where(function ($q) use ($user) {
                $q->whereNull('read_by')
                  ->orWhereJsonDoesntContain('read_by', $user->id);
            });
        }
    
        // Filter type
        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
    
        // Filter target role
        $query->where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    
        $notifications = $query->latest()->get();
    
        $fileName = 'notifikasi_'
            . ($status ?? 'all') . '_'
            . ($type ?? 'all') . '_'
            . date('Y-m-d') . '.xlsx';
    
        // Kirim parameter tambahan ke export class
        return Excel::download(new NotificationsExport(
            $notifications,
            $user->id,
            $status,
            $type,
            $user->role
        ), $fileName);
    }

    /**
     * Export notifikasi ke PDF sesuai filter yang dipilih
     */
    public function exportPdf(Request $request)
    {
        // Gunakan guard yang sesuai dengan middleware
        $user = Auth::guard('stafaset')->user();
    
        $query = Notification::query();
    
        // Filter status
        $status = $request->input('status');
        if ($status === 'read') {
            $query->whereJsonContains('read_by', $user->id);
        } elseif ($status === 'unread') {
            $query->where(function ($q) use ($user) {
                $q->whereNull('read_by')
                  ->orWhereJsonDoesntContain('read_by', $user->id);
            });
        }
    
        // Filter jenis
        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
    
        // Filter target role
        $query->where(function ($q) use ($user) {
            $q->whereNull('target_role')
              ->orWhere('target_role', 'all')
              ->orWhere('target_role', $user->role);
        });
    
        // Ambil notifikasi dan tambahkan atribut tambahan
        $notifications = $query->latest()->get()->map(function($notif) use ($user) { // ✅ use ($user)
            $notif->created_at_human = $notif->created_at->diffForHumans();
            $notif->is_read = $notif->isReadBy($user->id);
            return $notif;
        });
    
        $data = [
            'notifications' => $notifications,
            'user'          => $user,
            'filter_status' => $status ?? 'all',
            'filter_type'   => $type ?? 'all',
            'export_date'   => now()->format('d/m/Y H:i:s')
        ];
    
        $pdf = Pdf::loadView('notifications.pdf', $data);
        $pdf->setPaper('a4', 'landscape');
    
        $fileName = 'notifikasi_'
            . ($status ?? 'all') . '_'
            . ($type ?? 'all') . '_'
            . date('Y-m-d') . '.pdf';
    
        return $pdf->download($fileName);
    }
}
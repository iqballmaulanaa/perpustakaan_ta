<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;
use App\Models\Peminjaman;
use App\Models\Book;
use App\Models\Kategori;
use App\Models\Denda;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $query = Peminjaman::with(['member', 'book']); // Changed 'books' to 'book' (singular)

    if ($search) {
        $query->whereHas('member', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%");
        });
    }

    // Remove the redundant query
    $peminjamans = $query->get();

    return view('Peminjaman.daftarpeminjaman', compact('peminjamans'));
}

    public function search()
    {
        return view('Peminjaman.search');
    }

    public function searchMemberByEmail(Request $request)
    {
        // Validasi input email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $email = $request->input('email');

        // Lakukan pencarian anggota berdasarkan alamat email
        $member = Member::where('email', $email)->first();

        // Mengembalikan respon dalam bentuk JSON
        return response()->json(['member' => $member]);
    }

    // Metode untuk memproses hasil pemindaian QR code
    public function scanMemberByQRCode(Request $request)
    {
        // Validasi input QR code
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $qrCodeContent = $request->input('qr_code');

        // Dekripsi data QR code
        try {
            $decryptedData = Crypt::decryptString($qrCodeContent);
            $data = json_decode($decryptedData, true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid QR code'], 400);
        }

        // Lakukan pencarian anggota berdasarkan user_id
        $member = Member::where('user_id', $data['user_id'])->first();

        if ($member) {
            $user = User::find($data['user_id']);

            // if ($user->qr_code !== $data['qr_code']) {
            //     return response()->json(['error' => 'QR code expired or invalid'], 400);
            // }


            // Kembalikan data anggota
            return response()->json(['member' => $member], 200);
        } else {
            return response()->json(['error' => 'Member not found'], 404);
        }
    }



 public function searchBookPage(Request $request)
{
    $searchTerm = $request->input('search');
    $memberId = $request->input('member_id');

    if ($searchTerm) {
        $books = Book::where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('author', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('publisher', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('isbn', 'LIKE', '%' . $searchTerm . '%')
            ->orWhereHas('category', function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
            ->with(['category', 'rack', 'bookStock']) // tambahkan eager loading
            ->get();
    } else {
        $books = Book::with(['category', 'rack', 'bookStock'])->get(); // tampilkan semua buku jika tidak ada search
    }

    $member = Member::find($memberId);

    return view('books.search', compact('books', 'member', 'memberId'));
}

    // Metode pengontrol untuk menangani peminjaman buku
public function storePeminjaman(Request $request)
{
    $request->validate([
    'member_id' => 'required|exists:tbl_members,id',
    'book_id' => 'required|exists:tbl_books,id',
    'jumlah' => 'required|integer|min:1',
    'return_date' => 'required|date|after:today',
]);


    $memberId = $request->member_id;
    $bookId = $request->book_id;
    $jumlah = $request->jumlah;

    $member = Member::find($memberId);
    $book = Book::with('bookStock')->find($bookId);

    if (!$member || !$book) {
        return redirect()->back()->with('error', 'Anggota atau buku tidak ditemukan.');
    }

    // Cek jumlah buku tersedia
    if ($book->bookStock->jmlh_tersedia < $jumlah) {
        return redirect()->back()->with('error', 'Jumlah buku yang tersedia tidak mencukupi.');
    }

    // Cek peminjaman aktif
    $borrowedBooksCount = Peminjaman::where('member_id', $memberId)
        ->whereNull('return_date')
        ->count();

    if ($borrowedBooksCount >= 3) {
        return redirect()->back()->with('error', 'Anggota masih memiliki 3 atau lebih buku yang belum dikembalikan.');
    }

    // Cek denda belum lunas
    $unpaidFinesCount = Denda::whereHas('peminjaman', function ($query) use ($memberId) {
        $query->where('member_id', $memberId);
    })->where('status', 'belum lunas')->count();

    if ($unpaidFinesCount > 0) {
        return redirect()->back()->with('error', 'Anggota masih memiliki tunggakan denda.');
    }

    // Cek apakah buku yang sama sedang dipinjam
    $existingLoan = Peminjaman::where('member_id', $memberId)
        ->where('book_id', $bookId)
        ->whereNull('return_date')
        ->first();

    if ($existingLoan) {
        return redirect()->back()->with('error', 'Anggota sudah meminjam buku yang sama.');
    }

    // Generate kode resi unik
    do {
        $resi = strtoupper(Str::random(10));
    } while (Peminjaman::where('resi_pjmn', $resi)->exists());

    // Simpan peminjaman
    Peminjaman::create([
        'member_id' => $memberId,
        'book_id' => $bookId,
        'jumlah' => $jumlah,
        'return_date' => $request->return_date,
        'resi_pjmn' => $resi,
    ]);

    // Kurangi stok buku
    $bookStock = $book->bookStock;
    $bookStock->jmlh_tersedia -= $jumlah;
    $bookStock->save();

    return redirect()->back()->with('success', 'Buku berhasil dipinjam.');
}

    public function destroy($id)
    {
        // Temukan data peminjaman berdasarkan ID
        $peminjaman = Peminjaman::find($id);

        // Pastikan data peminjaman ditemukan
        if (!$peminjaman) {
            return redirect()->back()->with('error', 'Data peminjaman tidak ditemukan.');
        }

        // Kembalikan jumlah buku yang tersedia saat peminjaman dihapus
        $bookStock = $peminjaman->book->bookStock;
        $bookStock->jmlh_tersedia += $peminjaman->jumlah;
        $bookStock->save();

        // Hapus data peminjaman
        $peminjaman->delete();

        return redirect()->back()->with('success', 'Data peminjaman berhasil dihapus.');
    }
}

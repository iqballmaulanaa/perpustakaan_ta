<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DendaController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\RakbukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\HistoryTransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
    Route::get('/tchacap/refresh', function () {
        return response()->json(['captcha' => captcha_src()]);
    });
});

Route::middleware(['auth', 'throttle:100,1', AdminMiddleware::class])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/Team', [DashboardController::class, 'team'])->name('teamFp');

    // Member Routes
    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('member.index');
        Route::get('/create', [MemberController::class, 'create'])->name('member.create');
        Route::post('/', [MemberController::class, 'store'])->name('member.store');
        Route::get('/{member}/edit', [MemberController::class, 'edit'])->name('member.edit');
        Route::put('/{member}', [MemberController::class, 'update'])->name('member.update');
        Route::delete('/{member}', [MemberController::class, 'destroy'])->name('member.destroy');
        Route::get('/member/{member}/edit', [MemberController::class, 'edit'])->name('member.edit');
    });

    // Peminjaman buku
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
    Route::get('/peminjaman/search', [PeminjamanController::class, 'search'])->name('Peminjaman.search');
    Route::get('/search-member-by-email', [PeminjamanController::class, 'searchMemberByEmail'])->name('search.member.by.email');
    Route::get('/scan/member', [PeminjamanController::class, 'scanMemberByQRCode'])->name('scan.member.by.qrcode');
    Route::get('/search-books', [PeminjamanController::class, 'searchBookPage'])->name('search.book.page');
    Route::post('/store-peminjaman', [PeminjamanController::class, 'storePeminjaman'])->name('createPinjaman');
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'storePeminjaman'])->name('peminjaman.store');

    // Pengembalian buku
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian');
    Route::get('/pengembalian/search', [PengembalianController::class, 'search'])->name('pengembalian.search');
    Route::get('/pengembalian/cari', [PengembalianController::class, 'cari'])->name('pengembalian.cari');
    Route::put('/pengembalian/simpan', [PengembalianController::class, 'simpan'])->name('pengembalian.simpan');
    Route::delete('pengembalian/hapus/{id}', [PengembalianController::class, 'hapus'])->name('pengembalian.hapus');

    // Daftar buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::get('/book/{id}/', [BookController::class, 'showDetail'])->name('Books.showDetail');
    Route::get('/books/{id}update', [BookController::class, 'getBook'])->name('books.getBook');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.books.update');
    Route::get('/books/search', [BookController::class, 'search'])->name('books.search');

    // Rak buku
    Route::get('/rak', [RakbukuController::class, 'index'])->name('Rak.showdata');
    Route::get('/rak/create', [RakbukuController::class, 'create'])->name('Rak.createRak');
    Route::post('/rak/create', [RakbukuController::class, 'store'])->name('Rak.storeRak');
    Route::delete('/racks/{rack}', [RakbukuController::class, 'destroy'])->name('racks.destroy');
    Route::put('/racks/{rack}', [RakbukuController::class, 'update'])->name('racks.update');

    // Kategori
    Route::get('/categories', [KategoriController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [KategoriController::class, 'create'])->name('categories.create');
    Route::post('/categories/create', [KategoriController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [KategoriController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [KategoriController::class, 'destroy'])->name('categories.destroy');
    Route::get('/categories/show', [KategoriController::class, 'index'])->name('categories.show');

    // Denda
    Route::get('/denda', [DendaController::class, 'index'])->name('denda');
    Route::post('/denda/bayar', [DendaController::class, 'bayarDenda'])->name('denda.bayar');

    // History Transaksi
    Route::get('/history-transaksi', [HistoryTransaksiController::class, 'index'])->name('history.transaksi');
});

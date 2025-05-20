@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Hasil Pencarian Buku</h3>

    @if ($books->count())
        <div class="row">
            @foreach ($books as $book)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset($book->book_cover) }}" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Cover Buku">
                        <div class="card-body">
                            <h5 class="card-title">{{ $book->title }}</h5>
                            <p class="card-text"><strong>Penulis:</strong> {{ $book->author }}</p>
                            <p class="card-text"><strong>Penerbit:</strong> {{ $book->publisher }}</p>
                            <p class="card-text"><strong>Kategori:</strong> {{ $book->category->name ?? '-' }}</p>
                            <p class="card-text"><strong>Rak:</strong> {{ $book->rack->name ?? '-' }}</p>
                            <p class="card-text"><strong>Jumlah:</strong> {{ $book->bookStock->jmlh_tersedia ?? 0 }}</p>

{{-- Tombol Pinjam --}}
@if (($book->bookStock->jmlh_tersedia ?? 0) > 0)
    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf
        <input type="hidden" name="member_id" value="{{ $memberId }}">
        <input type="hidden" name="book_id" value="{{ $book->id }}">

        {{-- Input Jumlah Buku --}}
        <div class="mb-2">
            <label for="jumlah" class="form-label">Jumlah Buku</label>
            <input type="number" name="jumlah" class="form-control" min="1" max="{{ $book->bookStock->jmlh_tersedia }}" required>
        </div>

        {{-- Input Tanggal Pengembalian --}}
        <div class="mb-2">
            <label for="return_date" class="form-label">Tanggal Pengembalian</label>
            <input type="date" name="return_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-2">Pinjam Buku</button>
    </form>
@else
    <div class="alert alert-danger mt-3 mb-0 text-center p-2">
        Buku tidak tersedia
    </div>
@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">Tidak ada buku ditemukan.</div>
    @endif
</div>
@endsection

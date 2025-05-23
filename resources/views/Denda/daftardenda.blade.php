@extends('layouts.app')

@section('title', 'Data Denda')

@section('content')
    <div class="pb-2">
        @if (session('msg'))
            <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show animate__animated animate__fadeInDown"
                role="alert">
                {{ session('msg') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <div class="card animate__animated animate__fadeIn">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-12 col-lg-5">
                    <h5 class="card-title fw-semibold mb-4 animate__animated animate__fadeInLeft">Data Denda</h5>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="d-flex gap-2 justify-content-md-end animate__animated animate__fadeInRight">
                        <!-- Tambahkan elemen tambahan jika diperlukan -->
                    </div>
                </div>
               <div class="table-responsive animate__animated animate__fadeIn" style="animation-duration: 1.5s; animation-timing-function: cubic-bezier(0.68, -0.55, 0.27, 1.55); animation-fill-mode: both;">
                    <table class="table datatable table-hover table-striped">
                        <thead class="table-white animate__animated animate__fadeIn" style="animation-duration: 1.5s; animation-timing-function: cubic-bezier(0.68, -0.55, 0.27, 1.55); animation-fill-mode: both;">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Resi Peminjaman</th>
                                <th scope="col">Nama Member</th>
                                <th scope="col">Email</th>
                                <th scope="col">Tanggal Pinjam</th>
                                <th scope="col">Telat (hari)</th>
                                <th scope="col">Denda</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $counter = 0 @endphp
                            @foreach ($peminjamans as $key => $peminjaman)
                                @php
                                    if (!$peminjaman->return_date) {
                                        continue;
                                    }
                                    $createdAt = \Carbon\Carbon::parse($peminjaman->created_at);
                                    $returnDate = \Carbon\Carbon::parse($peminjaman->return_date);
                                    $telatHari = max(0, $returnDate->diffInDays($createdAt) - 7); // Menentukan telat, jika kurang dari 7 hari, dianggap 0 hari telat
                                    $totalDenda = $telatHari * 5000;

                                    // Periksa apakah ada data denda yang sudah dibayar
                                    $status = 'belum bayar';
                                    if ($peminjaman->denda) {
                                        $status = $peminjaman->denda->status === 'lunas' ? 'lunas' : 'belum bayar';
                                    }
                                @endphp

                                @if ($telatHari > 0 && $status == 'belum bayar')
                                   <tr class="animate__animated animate__fadeIn" style="animation-duration: 1.5s; animation-delay: {{ $counter * 0.2 }}s; animation-timing-function: cubic-bezier(0.68, -0.55, 0.27, 1.55); animation-fill-mode: both;">
                                        @php $counter++ @endphp
                                        <td>{{ $counter }}</td>
                                        <td>{{ $peminjaman->resi_pjmn }}</td>
                                        <td>{{ $peminjaman->member->first_name ?? 'Unknown' }}
                                            {{ $peminjaman->member->last_name ?? '' }}</td>
                                        <td>{{ $peminjaman->member->email ?? 'Unknown' }}</td>
                                        <td>{{ $createdAt->format('Y-m-d') }}</td>
                                        <td>{{ $telatHari }}</td>
                                        <td>Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success mt-1 w-40"
                                                data-bs-toggle="modal" data-bs-target="#payModal{{ $peminjaman->id }}">
                                                <i class="ti ti-credit-card"></i> Bayar
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($peminjamans as $peminjaman)
        @php
            if (!$peminjaman->return_date) {
                continue;
            }
        $createdAt = new DateTime($peminjaman->created_at);
$returnDate = new DateTime($peminjaman->return_date);
$interval = $returnDate->diff($createdAt);
$telatHari = max(0, $interval->days - 7); // Menghitung keterlambatan
$totalDenda = $telatHari * 5000;

            // Periksa apakah ada data denda yang sudah dibayar
            $status = 'belum bayar';
            if ($peminjaman->denda) {
                $status = $peminjaman->denda->status === 'lunas' ? 'lunas' : 'belum bayar';
            }
        @endphp
        <!-- Modal -->
        <div class="modal fade" id="payModal{{ $peminjaman->id }}" tabindex="-1"
            aria-labelledby="payModalLabel{{ $peminjaman->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animate__animated animate__fadeIn">
                    <div class="modal-header">
                        <h5 class="modal-title" id="payModalLabel{{ $peminjaman->id }}">Bayar Denda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('denda.bayar') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id_pjmn" value="{{ $peminjaman->id }}">
                            <p>Nama Member: {{ $peminjaman->member->first_name ?? 'Unknown' }}
                                {{ $peminjaman->member->last_name ?? '' }}</p>
                            <p>Jumlah Telat: {{ $telatHari }} hari</p>
                            <p>Total Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>

                            <div class="mb-3">
                                <label for="uang_dibayarkan" class="form-label">Uang yang Dibayarkan</label>
                                <input type="number" class="form-control" id="uang_dibayarkan_{{ $peminjaman->id }}"
                                    name="uang_yg_dibyrkn" required min="{{ $totalDenda }}">
                                <div class="invalid-feedback">
                                    Uang yang dibayarkan harus tepat sejumlah Rp {{ number_format($totalDenda, 0, ',', '.') }} atau lebih.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="kembalian" class="form-label">Kembalian</label>
                                <input type="text" class="form-control" id="kembalian_{{ $peminjaman->id }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status_{{ $peminjaman->id }}"
                                    value="{{ $status }}" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-success"
                                id="payButton_{{ $peminjaman->id }}">Bayar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
    <style>
        .btn {
            background: linear-gradient(90deg, rgba(58, 123, 213, 1) 0%, rgba(0, 212, 255, 1) 100%);
            border: none;
            color: white;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn {
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(90deg, rgba(0, 212, 255, 1) 0%, rgba(58, 123, 213, 1) 100%);
            transform: scale(1.05);
        }

        .btn:hover .ti-plus {
            transform: rotate(90deg);
        }
    </style>
    

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var inputUangDibayarkan = document.getElementById("uang_dibayarkan_{{ $peminjaman->id }}");
                var kembalianInput = document.getElementById("kembalian_{{ $peminjaman->id }}");

                function formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number).replace(/Rp/g, 'Rp ');
                }

                inputUangDibayarkan.addEventListener("input", function() {
                    var uangDibayarkan = parseFloat(inputUangDibayarkan.value);
                    var kembalian = uangDibayarkan - {{ $totalDenda }};
                    var status = (kembalian >= 0) ? 'lunas' : 'belum bayar';

                    document.getElementById("status_{{ $peminjaman->id }}").value = status;
                    kembalianInput.value = formatRupiah((kembalian > 0) ? kembalian : 0);

                    if (uangDibayarkan < {{ $totalDenda }}) {
                        inputUangDibayarkan.classList.add('is-invalid');
                    } else {
                        inputUangDibayarkan.classList.remove('is-invalid');
                    }
                });
            });
        </script>
    @endforeach
@endsection

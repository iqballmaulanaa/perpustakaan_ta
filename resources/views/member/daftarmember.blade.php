@extends('layouts.app')

@section('title', 'Daftar Member')

@section('content')
    <div class="pb-2">
        @if (session('msg'))
            <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show animate__animated animate__fadeInDown"
                role="alert">
                {{ session('msg') }}
                <button type="button" class="btn-close btn-custom" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div id="anggota-wrapper" class="row mb-2 animate__animated animate__fadeInLeft">
        <div class="col-12 col-lg-5">
            <h5 class="card-title fw-semibold mb-4">Daftar Anggota Perpustakaan</h5>
        </div>
        <div class="col-12 col-lg-7">
            <div class="d-flex gap-2 justify-content-md-end">
                <div>
                    <a id="btn-tambah-anggota" href="{{ route('member.create') }}" class="btn btn-custom-new py-2 px-4 animate__animated animate__zoomIn">
                        <i class="ti ti-plus me-2"></i>
                        Tambah Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay putih -->
    <div id="page-overlay" class="overlay animate__animated" style="display: none;"></div>

    <div class="table-responsive animate__animated animate__fadeInUp" style="animation-duration: 1s; animation-timing-function: ease-in-out;">
        <table class="table datatable table-hover table-striped">
            <thead class="custom-thead">
                <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col" class="text-center">Foto Profil</th>
                    <th scope="col" class="text-center">Nama</th>
                    <th scope="col" class="text-center">Email</th>
                    <th scope="col" class="text-center">Telepon</th>
                    <th scope="col" class="text-center">Tanggal Lahir</th>
                    <th scope="col" class="text-center">Alamat</th>
                    <th scope="col" class="text-center">NIS</th>
                    <th scope="col" class="text-center">Kelas</th>
                    <th scope="col" class="text-center">Jenis Kelamin</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @forelse ($members as $index => $member)
                    <tr class="animate__animated animate__fadeIn" style="animation-duration: 1s; animation-delay: {{ $index * 0.2 }}s; animation-timing-function: ease-in-out;">
                        <th scope="row">{{ $index + 1 }}</th>
                        <td class="text-center">
                            @if ($member->imageProfile)
                                <img src="{{ asset('/profiles/' . $member->imageProfile) }}"
                                    alt="{{ $member->first_name }}"
                                    class="profile-img">
                            @else
                                <span>Tidak ada foto profil</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $member->email ?? 'N/A' }}</td>
                        <td class="text-center">{{ $member->phone ?? 'N/A' }}</td>
                        <td class="text-center">
                            {{ $member->tgl_lahir ? \Carbon\Carbon::parse($member->tgl_lahir)->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="text-center">{{ $member->address ?? 'N/A' }}</td>
                        <td class="text-center">{{ $member->nis ?? 'N/A' }}</td>
                        <td class="text-center">{{ $member->kelas ?? 'N/A' }}</td>
                        <td class="text-center">{{ $member->gender ?? 'N/A' }}</td>

                        <td class="text-center">
                            <a href="{{ route('member.edit', $member->id) }}" class="btn btn-warning btn-sm mb-1">
                                <i class="ti ti-edit"></i> Edit
                            </a>
                            <form action="{{ route('member.destroy', $member->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-custom btn-sm delete-btn" data-id="{{ $member->id }}">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">Belum ada data anggota.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus anggota ini?");
        }

        document.getElementById('btn-tambah-anggota').addEventListener('click', function(e) {
            e.preventDefault();
            const wrapper = document.getElementById('anggota-wrapper');
            const overlay = document.getElementById('page-overlay');

            wrapper.classList.remove('animate__fadeInLeft');
            wrapper.classList.add('animate__fadeOutLeft');

            overlay.style.display = 'block';
            overlay.classList.add('fade-in');

            setTimeout(() => {
                window.location.href = this.href;
            }, 1000);
        });

        document.querySelectorAll('.delete-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                if (!confirm("Apakah Anda yakin ingin menghapus anggota ini?")) {
                    return;
                }

                const form = this.closest('form');
                const row = this.closest('tr');

                row.classList.add('fade-out-red');

                setTimeout(function () {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endpush

@push('styles')
    <style>
        .btn-custom {
            background: linear-gradient(90deg, rgba(58, 123, 213, 1) 0%, rgba(0, 212, 255, 1) 100%);
            border: none;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background: linear-gradient(90deg, rgba(0, 212, 255, 1) 0%, rgba(58, 123, 213, 1) 100%);
            transform: scale(1.05);
        }

        .btn-custom.btn-close {
            padding: 0;
            border: none;
            background: none;
        }

        .profile-img {
            width: 20px;
            height: 20px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table-group-divider tr {
            transition: background-color 0.3s;
        }

        .table-group-divider tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .bg-new {
            background: linear-gradient(45deg, #4caf50, #81c784);
            box-shadow: 0px 4px 15px rgba(76, 175, 80, 0.4);
        }

        .bg-other {
            background: linear-gradient(45deg, #f44336, #e57373);
            box-shadow: 0px 4px 15px rgba(244, 67, 54, 0.4);
        }

        @keyframes fadeOutRed {
            0% {
                background-color: #f8d7da;
                opacity: 1;
            }
            100% {
                background-color: #f8d7da;
                opacity: 0;
                transform: scale(0.95);
            }
        }

        .fade-out-red {
            animation: fadeOutRed 0.5s ease forwards;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            z-index: 9999;
            opacity: 0;
        }

        .overlay.fade-in {
            animation: overlayFadeIn 1s forwards;
        }

        @keyframes overlayFadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 0.85;
            }
        }
    </style>
@endpush

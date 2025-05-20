@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card animate__animated animate__fadeIn">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Edit Anggota</h5>

                    @if (session('msg'))
                        <div class="alert {{ session('error') ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show">
                            {{ session('msg') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('member.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Tampilkan error validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Nama Depan</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Nama Belakang</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $member->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $member->phone) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $member->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $member->tgl_lahir ? \Carbon\Carbon::parse($member->tgl_lahir)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imageProfile" class="form-label">Foto Profil</label>
                            <input type="file" class="form-control" id="imageProfile" name="imageProfile">
                            @if($member->imageProfile)
                                <div class="mt-2">
                                    <img src="{{ asset('/profiles/' . $member->imageProfile) }}" alt="Current Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                                    <p class="text-muted mt-1">Foto saat ini</p>
                                </div>
                            @endif
                        </div>

                        <div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input type="text" class="form-control" id="nis" name="nis" value="{{ old('nis', $member->nis) }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" class="form-control" id="kelas" name="kelas" value="{{ old('kelas', $member->kelas) }}" required>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="gender" class="form-label">Jenis Kelamin</label>
    <select class="form-control" id="gender" name="gender" required>
        <option value="">-- Pilih Jenis Kelamin --</option>
        <option value="Laki-laki" {{ old('gender', $member->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
        <option value="Perempuan" {{ old('gender', $member->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
    </select>
</div>

                        <button type="submit" class="btn btn-custom">Update</button>
                        <a href="{{ route('member.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

        .animate__animated {
            animation-duration: 1s;
            animation-timing-function: ease-in-out;
        }
    </style>
@endsection

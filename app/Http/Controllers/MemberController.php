<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\File;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::all(); // Menghapus pemetaan status berdasarkan tanggal

        return view('member.daftarmember', compact('members'));
    }

    public function create()
    {
        return view('member.tambahmember');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:tbl_members,email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'tgl_lahir' => 'nullable|date',
        'nis' => 'nullable|string|max:20',
        'kelas' => 'nullable|string|max:50',
        'gender' => 'nullable|in:Laki-laki,Perempuan',
        'imageProfile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle file upload
    if ($request->hasFile('imageProfile')) {
        $imageName = time().'.'.$request->imageProfile->extension();
        $request->imageProfile->move(public_path('profiles'), $imageName);
        $validated['imageProfile'] = $imageName;
    }

    // Create user first
    $user = User::create([
        'email' => $validated['email'],
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'password' => bcrypt('defaultpassword') // password default
    ]);

    // Assign user_id to the member
    $validated['user_id'] = $user->id;

    // Create member
    Member::create($validated);

    return redirect()->route('member.index')
                     ->with('msg', 'Anggota berhasil ditambahkan')
                     ->with('error', false);
}

    public function edit(Member $member)
    {
        return view('member.editmember', compact('member'));
    }

 public function update(Request $request, Member $member)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:tbl_members,email,'.$member->id,
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'tgl_lahir' => 'nullable|date',
        'nis' => 'nullable|string|max:20',
        'kelas' => 'nullable|string|max:50',
        'gender' => 'nullable|in:Laki-laki,Perempuan',
        'imageProfile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('imageProfile')) {
        if ($member->imageProfile) {
            $oldImagePath = public_path('profiles/'.$member->imageProfile);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $imageName = time().'.'.$request->imageProfile->extension();
        $request->imageProfile->move(public_path('profiles'), $imageName);
        $validated['imageProfile'] = $imageName;
    }

    $member->update($validated);

    return redirect()->route('member.index')
                     ->with('msg', 'Anggota berhasil diperbarui')
                     ->with('error', false);
}

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        // Delete imageProfile if exists
        if ($member->imageProfile) {
            $imageProfilePath = public_path('profiles/' . $member->imageProfile);
            if (File::exists($imageProfilePath)) {
                File::delete($imageProfilePath);
            }
        }

        // Find and delete associated user if exists
        $user = User::find($member->user_id);
        if ($user) {
            // Delete QR code if exists
            if ($user->qr_codes) {
                $qrCodePath = public_path('qrcodes/' . $user->qr_code);
                if (File::exists($qrCodePath)) {
                    File::delete($qrCodePath);
                }
            }
            $user->delete();
        }

        $member->delete();

        return redirect()->route('member.index')
                         ->with('msg', 'Anggota dan pengguna terkait berhasil dihapus.')
                         ->with('error', false);
    }
}

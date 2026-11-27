<?php

namespace App\Http\Controllers;

use App\Support\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'provinces' => Wilayah::provinces(),
            'ordersCount' => $user->orders()->count(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'phone.required' => 'No. HP / WhatsApp wajib diisi.',
            'avatar.image' => 'Foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto profil: JPG, PNG, atau WebP.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if ($request->hasFile('avatar')) {
            $this->deleteOldAvatar($user->avatar);
            $dir = public_path('images/avatars');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file = $request->file('avatar');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = 'user-'.$user->id.'-'.time().'-'.Str::lower(Str::random(4)).'.'.$ext;
            $file->move($dir, $filename);
            $data['avatar'] = 'images/avatars/'.$filename;
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        $this->deleteOldAvatar($user->avatar);
        $user->update(['avatar' => null]);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus.');
    }

    public function updateAddress(Request $request)
    {
        $prov = $request->input('province');

        $data = $request->validate([
            'province' => ['required', Rule::in(Wilayah::provinces())],
            'city' => ['required', Rule::in(Wilayah::cities($prov))],
            'district' => ['required', Rule::in(Wilayah::districts($prov, $request->input('city')))],
            'zip' => ['required', 'digits:5'],
            'address' => ['required', 'string', 'max:500'],
        ], [
            'province.required' => 'Pilih provinsi.',
            'province.in' => 'Pilih provinsi dari daftar.',
            'city.required' => 'Pilih kota / kabupaten.',
            'city.in' => 'Kota / kabupaten tidak sesuai dengan provinsi.',
            'district.required' => 'Pilih kecamatan.',
            'district.in' => 'Kecamatan tidak sesuai dengan kota / kabupaten.',
            'zip.required' => 'Isi kode pos.',
            'zip.digits' => 'Kode pos harus 5 angka.',
            'address.required' => 'Isi detail alamat lengkap (jalan, nomor rumah, RT/RW, patokan).',
        ]);

        $request->user()->update($data);

        return redirect()->route('profile.edit')->with('success', 'Alamat default berhasil disimpan dan akan otomatis dipakai saat checkout.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'current_password.required' => 'Masukkan password saat ini.',
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.required' => 'Masukkan password baru.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $request->user()->update(['password' => $data['password']]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui.');
    }

    private function deleteOldAvatar(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http')) {
            return;
        }
        $file = public_path($path);
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

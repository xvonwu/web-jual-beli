<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest('id')->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:coupons,code'],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'integer', 'min:1'],
            'min_total' => ['nullable', 'integer', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ], [
            'code.required' => 'Isi kode kupon.',
            'code.unique' => 'Kode kupon sudah terdaftar.',
            'value.required' => 'Isi nilai diskon.',
        ]);

        if ($data['type'] === 'percent' && $data['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Diskon persen maksimal 100%.']);
        }

        Coupon::create([
            'code' => strtoupper(trim($data['code'])),
            'type' => $data['type'],
            'value' => $data['value'],
            'min_total' => $data['min_total'] ?? 0,
            'max_uses' => $data['max_uses'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kupon '.strtoupper(trim($data['code'])).' berhasil ditambahkan.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', 'Status kupon '.$coupon->code.' diubah menjadi '.($coupon->is_active ? 'Aktif' : 'Nonaktif').'.');
    }

    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        return back()->with('success', "Kupon {$code} berhasil dihapus.");
    }
}

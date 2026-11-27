<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index(Request $request)
    {
        $courier = $request->query('courier');
        $q = trim((string) $request->query('q', ''));

        $rates = ShippingRate::query()
            ->when($courier, fn ($query) => $query->where('courier', $courier))
            ->when($q !== '', fn ($query) => $query->where('province', 'like', '%'.$q.'%'))
            ->orderBy('courier')
            ->orderBy('province')
            ->paginate(20)
            ->withQueryString();

        return view('admin.shipping.index', compact('rates', 'courier', 'q'));
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        $data = $request->validate([
            'cost' => ['required', 'integer', 'min:0'],
        ], [
            'cost.required' => 'Tarif tidak boleh kosong.',
        ]);

        $shippingRate->update(['cost' => $data['cost']]);

        return back()->with('success', 'Tarif '.strtoupper($shippingRate->courier).' ke '.$shippingRate->province.' diperbarui.');
    }
}

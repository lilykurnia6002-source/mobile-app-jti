<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilAdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Profil admin/pemilik yang sedang login.
 */
class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        return view('profil.index', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfilAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'] ?? null,
        ]);

        // Password hanya diubah bila diisi (opsional).
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.profil.show')
            ->with('sukses', 'Profil berhasil diperbarui.');
    }
}

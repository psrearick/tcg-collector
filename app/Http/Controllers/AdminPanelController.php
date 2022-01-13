<?php

namespace App\Http\Controllers;

class AdminPanelController extends Controller
{
    public function edit() {
        $currentAdminPanel = session('admin-panel', false);
        $adminPanel = !$currentAdminPanel;
        session(['admin-panel' => $adminPanel]);
        if ($adminPanel) {
            return redirect()->route('stores.index');
        }

        return redirect()->route('collections.index');
    }
}
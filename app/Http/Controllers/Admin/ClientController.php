<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('departments')->get();
        return view('admin.clients', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'departments' => 'nullable|array',
            'departments.*.department' => 'nullable|string|max:255',
            'departments.*.email' => 'nullable|email|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $client = Client::create([
                'company' => $request->company,
            ]);

            if ($request->has('departments')) {
                foreach ($request->departments as $dept) {
                    if (!empty($dept['department']) && !empty($dept['email'])) {
                        ClientDepartment::create([
                            'client_id'  => $client->id,
                            'department' => $dept['department'], // ✅ match DB
                            'email'      => $dept['email'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client added successfully.');
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'departments' => 'nullable|array',
            'departments.*.department' => 'nullable|string|max:255',
            'departments.*.email' => 'nullable|email|max:255',
        ]);

        DB::transaction(function () use ($request, $client) {
            $client->update([
                'company' => $request->company,
            ]);

            $client->departments()->delete();

            if ($request->has('departments')) {
                foreach ($request->departments as $dept) {
                    if (!empty($dept['department']) && !empty($dept['email'])) {
                        ClientDepartment::create([
                            'client_id'  => $client->id,
                            'department' => $dept['department'], // ✅ match DB
                            'email'      => $dept['email'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}

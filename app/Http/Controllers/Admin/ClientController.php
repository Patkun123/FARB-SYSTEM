<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientDepartment;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
     /**
     * Show all clients with their departments.
     */
    public function index()
    {
        $clients = Client::with('departments')->get();
        return view('admin.clients', compact('clients'));
    }

    /**
     * Store a new client with departments.
     */
        public function store(Request $request)
        {
            $request->validate([
                'company' => 'required|string|max:255|unique:clients,company',
                'departments' => 'required|array|min:1',
                'departments.*.department' => 'required|string|max:255',
                'departments.*.email' => 'required|email|max:255|distinct',
                'departments.*.personnel' => 'required|string|max:255',
                'departments.*.position' => 'required|string|max:255',
            ]);

            // ✅ Check for existing department emails
            foreach ($request->departments as $dept) {
                $exists = \App\Models\ClientDepartment::where('email', $dept['email'])->exists();
                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['departments' => "The email {$dept['email']} is already in use by another department."]);
                }
            }

            DB::transaction(function () use ($request) {
                $client = Client::create(['company' => $request->company]);

                foreach ($request->departments as $dept) {
                    $client->departments()->create([
                        'department' => $dept['department'],
                        'email' => $dept['email'],
                        'personnel' => $dept['personnel'],
                        'position' => $dept['position'],
                    ]);
                }
            });

            return redirect()->route('admin.clients.index')->with('success', 'Client added successfully.');
        }


    /**
     * Update a client and its departments.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company' => 'required|string|max:255|unique:clients,company,' . $client->id,
            'departments' => 'required|array',
            'departments.*.department' => 'required|string|max:255',
            'departments.*.email' => 'required|email|max:255',
            'departments.*.personnel' => 'required|string|max:255',
            'departments.*.position' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $client) {
            $client->update(['company' => $request->company]);

            $client->departments()->delete();

            foreach ($request->departments as $dept) {
                $client->departments()->create($dept);
            }
        });

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Delete a client and its departments.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }
}

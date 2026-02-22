<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Person;

class PersonController extends Controller
{
    /**
     * Display all persons and chart data.
     */
    public function index(Request $request)
    {
        // Define person types
        $personTypes = ['کارمند', 'مشتری', 'همکار', 'سایر'];

        // Fetch all persons from database
        $persons = Person::all();

        // Prepare chart data grouped by type
        $chartData = [];
        foreach ($persons as $p) {
            $chartData[$p->type] = ($chartData[$p->type] ?? 0) + 1;
        }

        // Pass variables to the view
        return view('tenant.persons.index', compact('persons', 'personTypes', 'chartData'));
    }

    /**
     * Store a new person.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'desc' => 'nullable|string',
        ]);

        Person::create($request->only(['name', 'type', 'desc']));

        return redirect()->back()->with('success', 'Person created successfully.');
    }

    /**
     * Show the edit form for a person.
     */
    public function edit(Person $person)
    {
        $personTypes = ['کارمند', 'مشتری', 'همکار', 'سایر'];

        return view('tenant.persons.edit', compact('person', 'personTypes'));
    }

    /**
     * Update a person's data.
     */
    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'desc' => 'nullable|string',
        ]);

        $person->update($request->only(['name', 'type', 'desc']));

        return redirect()->route('tenant.persons.index')
            ->with('success', 'Person updated successfully.');
    }

    /**
     * Delete a person.
     */
    public function destroy(Person $person)
    {
        $person->delete();

        return redirect()->back()->with('success', 'Person deleted successfully.');
    }
}

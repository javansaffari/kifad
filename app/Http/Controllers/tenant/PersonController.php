<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Person;

class PersonController extends Controller
{
    /**
     * Display a listing of persons with chart data.
     */
    public function index(Request $request)
    {
        // Define available person types
        $personTypes = [
            'خانواده',
            'دوستان',
            'همکار',
            'مشتری',
            'کارمند',
            'همسایه',
            'سایر'
        ];
        // Search filter
        $search = $request->input('search');

        $personsQuery = Person::query();

        if ($search) {
            $personsQuery->where('name', 'like', "%{$search}%");
        }

        // Get all persons
        $persons = $personsQuery->latest()->get();

        // Prepare chart data grouped by type
        $chartData = $persons->groupBy('type')->map(function ($items) {
            return $items->count();
        });

        return view('tenant.person.index', compact(
            'persons',
            'personTypes',
            'chartData'
        ));
    }

    /**
     * Store a newly created person.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'desc' => 'nullable|string',
        ]);

        Person::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->desc,
        ]);

        return redirect()->route('tenant.person.index')
            ->with('success', 'شخص با موفقیت ایجاد شد.');
    }

    /**
     * Show the form for editing a person.
     */
    public function edit(Person $person)
    {
        $personTypes = [
            'خانواده',
            'دوستان',
            'همکار',
            'مشتری',
            'کارمند',
            'همسایه',
            'سایر'
        ];
        return view('tenant.person.edit', compact('person', 'personTypes'));
    }

    /**
     * Update the specified person.
     */
    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'desc' => 'nullable|string',

        ]);

        $person->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->desc,

        ]);

        return redirect()->route('tenant.person.index')
            ->with('success', 'شخص با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified person.
     */
    public function destroy(Person $person)
    {
        // Check if person has related transactions
        if ($person->transactions()->exists()) {
            return redirect()->route('tenant.person.index')
                ->with('error', 'این شخص قابل حذف نیست، زیرا در تراکنش‌ها استفاده شده است.');
        }

        $person->delete();

        return redirect()->route('tenant.person.index')
            ->with('success', 'شخص با موفقیت حذف شد.');
    }
}

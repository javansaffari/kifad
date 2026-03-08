<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Cheque;
use App\Models\Tenant\Person;
use App\Models\Tenant\Account;
use Illuminate\Support\Facades\Validator;

class ChequesController extends Controller
{
    /**
     * Display a listing of the cheques along with chart data.
     */
    public function index()
    {
        // Get all cheques with related person and account
        $checks = Cheque::with(['person', 'account'])->get();

        // Data for the form selects
        $persons = Person::all();
        $accounts = Account::all();
        $banks = [
            'بانک آینده',
            'بانک اقتصاد نوین',
            'بانک انصار',
            'بانک ایران زمین',
            'بانک پارسیان',
            'بانک پاسارگاد',
            'بانک تجارت',
            'بانک توسعه تعاون',
            'بانک توسعه صادرات ایران',
            'بانک صنعت و معدن',
            'بانک سینا',
            'بانک سپه',
            'بانک شهر',
            'بانک سرمایه',
            'بانک قرض‌الحسنه رسالت',
            'بانک قرض‌الحسنه مهر ایران',
            'بانک رفاه کارگران',
            'بانک حکمت ایرانیان',
            'بانک خاورمیانه',
            'بانک گردشگری',
            'بانک مسکن',
            'بانک ملت',
            'بانک ملی ایران',
            'بانک کارآفرین',
            'بانک صادرات ایران',
            'پست بانک ایران',
            'موسسه اعتباری کوثر',
            'موسسه اعتباری ملل',
            'موسسه اعتباری نور',
            'موسسه اعتباری توسعه',
            'سایر',
        ];
        $tags = Cheque::pluck('tags')->flatten()->unique();

        // Cheque types for filtering
        $checkTypes = ['صادر شده', 'دریافتی'];

        // Chart data by type
        $chartData = [
            'صادر شده' => $checks->where('type', 'صادر شده')->sum('amount'),
            'دریافتی' => $checks->where('type', 'دریافتی')->sum('amount'),
        ];

        return view('tenant.cheques.index', compact(
            'checks',
            'persons',
            'accounts',
            'banks',
            'tags',
            'checkTypes',
            'chartData'
        ));
    }

    /**
     * Store a newly created cheque in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:issued,received',
            'amount' => 'required|numeric|min:0',
            'serial' => 'required|string|max:255',
            'siyad_id' => 'nullable|string|size:16',
            'person' => 'nullable|exists:persons,id',
            'account' => 'required|exists:accounts,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'bank' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'desc' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create new cheque
        $cheque = new Cheque();
        $cheque->type = $request->type;
        $cheque->amount = $request->amount;
        $cheque->serial_number = $request->serial;
        $cheque->sayad_id = $request->siyad_id;
        $cheque->person_id = $request->person;
        $cheque->account_id = $request->account;
        $cheque->issue_date = $request->issue_date;
        $cheque->due_date = $request->due_date;
        $cheque->bank = $request->bank;
        $cheque->tags = $request->tags ? json_encode($request->tags) : null;
        $cheque->description = $request->desc;
        $cheque->reminder = false;
        $cheque->save();

        // Redirect with success message
        return redirect()->route('tenant.cheques.index')->with('success', 'چک با موفقیت ثبت شد.');
    }

    /**
     * Show the form for editing the specified cheque.
     */
    public function edit($id)
    {
        // Find cheque or fail
        $cheque = Cheque::findOrFail($id);

        // Data for selects
        $persons = Person::all();
        $accounts = Account::all();
        $banks = Cheque::select('bank')->distinct()->pluck('bank');
        $tags = Cheque::pluck('tags')->flatten()->unique();

        return view('tenant.cheques.edit', compact('cheque', 'persons', 'accounts', 'banks', 'tags'));
    }

    /**
     * Update the specified cheque in storage.
     */
    public function update(Request $request, $id)
    {
        $cheque = Cheque::findOrFail($id);

        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:صادر شده,دریافتی',
            'amount' => 'required|numeric|min:0',
            'serial' => 'required|string|max:255',
            'siyad_id' => 'required|string|size:16',
            'person' => 'required|exists:persons,id',
            'account' => 'required|exists:accounts,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'bank' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'desc' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update cheque
        $cheque->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'serial_number' => $request->serial,
            'sayad_id' => $request->siyad_id,
            'person_id' => $request->person,
            'account_id' => $request->account,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'bank' => $request->bank,
            'tags' => $request->tags ? json_encode($request->tags) : null,
            'description' => $request->desc,
        ]);

        return redirect()->route('cheques.index')->with('success', 'چک با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified cheque from storage.
     */
    public function destroy($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->delete();

        return redirect()->route('cheques.index')->with('success', 'چک با موفقیت حذف شد.');
    }

    /**
     * Display the specified cheque.
     */
    public function show($id)
    {
        $cheque = Cheque::with(['person', 'account'])->findOrFail($id);
        return view('tenant.cheques.show', compact('cheque'));
    }
}

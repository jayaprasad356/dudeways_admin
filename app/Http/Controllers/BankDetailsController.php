<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\BankDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankDetailsController extends Controller
{

public function index(Request $request)
{
    $query = BankDetails::query()->with('user'); // Eager load the user relationship

    if ($request->has('search') && !empty($request->input('search'))) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%$search%")
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('name', 'like', "%$search%");
              });
        });
    }

       // Check if the request is AJAX
       if ($request->wantsJson()) {
        return response($query->get());

    }

    $bankdetails = $query->latest()->paginate(10); // Paginate the results

    $users = Users::all(); // Fetch all users for the filter dropdown

    return view('bankdetails.index', compact('bankdetails', 'users')); // Pass friends and users to the view
}
    public function edit(BankDetails $bankdetails)
    {
        return view('bankdetails.edit', compact('bankdetails'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Points  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankDetails $bankdetails)

    {
        $bankdetails->account_holder_name = $request->account_holder_name;
        $bankdetails->account_number = $request->account_number;
        $bankdetails->ifsc_code = $request->ifsc_code;
        $bankdetails->bank_name = $request->bank_name;
        $bankdetails->branch_name = $request->branch_name;
        

        if (!$bankdetails->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('bankdetails.edit', $bankdetails->id)->with('success', 'Success, Bank Details has been updated.');
    }
}
<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // GET CUSTOMERS
        $customers = Customer::when(request()->q, function ($customers) {
            $customers = $customers->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        // RETURN VIEW
        return Inertia::render('Apps/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        // RETURN VIEW
        return Inertia::render('Apps/Customers/Create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // VALIDATE
        $this->validate($request, [
            'name'      => 'required',
            'no_telp'   => 'required|unique:customers',
            'address'   => 'required',
        ]);

        // CREATE RECORD CUSTOMER
        Customer::create([
            'name'      => $request->name,
            'no_telp'   => $request->no_telp,
            'address'   => $request->address,
        ]);

        // RETURN REDIRECT
        return redirect()->route('apps.customers.index');
    }

    /**
     * edit
     *
     * @param  mixed $customer
     * @return void
     */
    public function edit(Customer $customer)
    {
        // RETURN VIEW
        return Inertia::render('Apps/Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $customer
     * @return void
     */
    public function update(Request $request, Customer $customer)
    {
        // VALIDATE
        $this->validate($request, [
            'name'      => 'required',
            'no_telp'   => 'required|unique:customers,no_telp,' . $customer->id,
            'address'   => 'required',
        ]);

        // UPDATE CUSTOMER RECORD
        $customer->update([
            'name'      => $request->name,
            'no_telp'   => $request->no_telp,
            'address'   => $request->address,
        ]);

        // RETURN REDIRECT ROUTE
        return redirect()->route('apps.customers.index');
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // FIND BY ID
        $customer = Customer::findOrFail($id);

        // DELETE CUSTOMER
        $customer->delete();

        // RETURN REDIRECT
        return redirect()->route('apps.customers.index');
    }
}

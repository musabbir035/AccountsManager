<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\History;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'query' => 'string|max:255'
        ]);

        if ($request->has('query')) {
            $query = $request->input('query');
            $customers = Customer::where('name', 'like', '%' . $query . '%')
                ->orWhere('mobile', 'like', '%' . $query . '%')->get();
        } else {
            $customers = Customer::with('addedBy')->get();
        }

        return response([
            'customers' => CustomerResource::collection($customers)
        ], 200);
    }

    public function show(Request $request)
    {
        if ($request->has('id')) {
            $customer = Customer::findOrFail($request->input('id'));
        } else if ($request->has('mobile')) {
            $customer = Customer::where('mobile', $request->input('mobile'))->first();
        } else {
            return response([
                'message' => 'Invalid request'
            ], 500);
        }
        return response([
            'customer' => new CustomerResource($customer)
        ]);
    }

    public function store(CustomerRequest $request)
    {
        /**
         * Balance type determines wheather owner will be paid or will need to
         * pay the money (i.e. debit/credit). 1 is for 'will be paid by the customer'
         * and the initial_balance will be stored as positive whereas 2 is for 'will need to
         * pay the customer' and the initial_balance will be stored as negative
         */
        if ($request->input('initial_balance')) {
            if (!$request->input('type')) {
                return response([
                    'errors' => [
                        'type' => [trans('validation.custom.balance.null_balance_type')]
                    ],
                ], 422);
            }
            // if initial_balance type is not 1 multipling initial_balance by -1
            $customer = Auth::user()->customers()->create($request->safe()->except(['initial_balance', 'type']) + [
                'initial_balance' => $request->input('type') == 1 ? $request->input('initial_balance') : ($request->input('initial_balance') * -1)
            ]);
        } else {
            //if initial_balance field is null set defaul initial_balance to 0
            $customer = Auth::user()->customers()->create($request->safe()->except('initial_balance') + [
                'initial_balance' => 0
            ]);
        }

        $msg = trans('messages.customer_added');

        return response([
            'message' => $msg,
            'customer' => new CustomerResource($customer)
        ], 200);
    }

    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $history = new History();
        $history->action_by_id = Auth::id();
        $history->old_value = json_encode($customer);

        try {
            DB::transaction(function () use ($customer, $history, $request) {
                $customer->update($request->validated());
                $history->new_value = json_encode($customer);
                $customer->histories()->save($history);
            }, 3);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('messages.something_wrong')
            ], 500);
        }

        return response([
            'message' => trans('messages.customer_updated'),
            'customer' => new CustomerResource($customer)
        ], 200);
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 1) {
            $customer = Customer::findOrFail($id);

            if ($customer->receipts()->exists() || $customer->transactions()->exists()) {
                return response([
                    'message' => trans('messages.customer_delete_integrity_error')
                ], 500);
            }

            $customer->delete();
            return response([
                'message' => trans('messages.customer_deleted')
            ], 200);
        }

        return response([
            'message' => trans('messages.no_permission')
        ], 403);
    }
}

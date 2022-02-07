<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateLedgerRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\History;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('customerId')) {
            $transactions = Transaction::with('addedBy')->where('customer_id', $request->input('customerId'))->get();
        } else {
            $transactions = Transaction::with('addedBy')->get();
        }
        return response([
            'transactions' => TransactionResource::collection($transactions)
        ], 200);
    }

    public function store(TransactionRequest $request)
    {
        $transaction = Auth::user()->transactions()->create($request->validated());

        return response([
            'message' => trans('messages.transaction_added'),
            'transaction' => new TransactionResource($transaction)
        ], 200);
    }

    public function show($id)
    {
        return response([
            'message' => trans('messages.transaction_added'),
            'transaction' => new TransactionResource(Transaction::find($id))
        ], 200);
    }

    public function update(TransactionRequest $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $history = new History();
        $history->action_by_id = Auth::id();
        $history->old_value = json_encode($transaction);

        try {
            DB::transaction(function () use ($transaction, $history, $request) {
                $transaction->update($request->validated());
                $history->new_value = json_encode($transaction);
                $transaction->histories()->save($history);
            }, 3);
        } catch (Exception $e) {
            return response()->json([
                'message' => trans('messages.something_wrong')
            ], 500);
        }

        return response([
            'message' => trans('messages.transaction_updated'),
            'transaction' => new TransactionResource($transaction)
        ], 200);
    }

    public function destroy($id)
    {
    }

    public function generateLedgerPdf(GenerateLedgerRequest $request)
    {
        try {
            $start = $request->input('dateFrom');
            $end = $request->input('dateTo');
            $name = '';
            $mobile = '';
            $address = '';
            $id = '';
            $totalCr = 0.0;
            $totalDr = 0.0;
            $pdfName = $start . '_' . $end . '_' . time() . '.pdf';

            if ($request->input('customerId')) {
                $pdfName = $request->input('customerId') . '_' . $start . '_' . $end . '_' . time() . '.pdf';
                $customer = Customer::find($request->input('customerId'));
                $name = $customer->name;
                $mobile = $customer->mobile;
                $address = $customer->address;
                $id = $customer->id;
                $transactions = Transaction::where('customer_id', $id)
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->get();

                //get transactions that hapenned before start date
                $prevTrans = Transaction::where('customer_id', $id)
                    ->where('date', '<', $start)
                    ->get();

                $init_balance = $customer->initial_balance;
                /** 
                 * if there are transactions that happened before the start date,
                 * then calculate initial balance by adding or subtracting the amount
                 * based on transaction type
                 */
                if ($prevTrans && count($prevTrans) > 0) {
                    foreach ($prevTrans as $prevTran) {
                        if ($prevTran->type === Transaction::$INCOME) {
                            $init_balance = $init_balance - $prevTran->amount;
                        } else {
                            $init_balance = $init_balance + $prevTran->amount;
                        }
                    }
                }
            } else {
                $transactions = Transaction::where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->get();
                $init_balance = '0.0';
            }

            $pdfTableRows = '';
            $balance = $init_balance;
            foreach ($transactions as $trans) {
                $debit = 0.0;
                $credit = 0.0;

                /**
                 * Determine debit or credit based on transaction type.
                 * Also calculate total debit & total credit
                 */
                if ($trans->type === Transaction::$INCOME) {
                    $totalCr += $trans->amount;
                    $balance -= $trans->amount;
                    $type = 'Cr';
                    $credit = $trans->amount;
                } else {
                    $totalDr += $trans->amount;
                    $balance += $trans->amount;
                    $type = 'Dr';
                    $debit = $trans->amount;
                }

                // Generate row string and concate every time the loop runs
                $pdfTableRows .= '
                    <tr>
                        <td style="border-top: 1px solid #b0b0b0">' . date('d-m-Y', strtotime($trans->date)) . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . $trans->memo_number . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . $trans->description . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . $type . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . ($debit == 0 ? '' : number_format($debit, 2)) . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . ($credit == 0 ? '' : number_format($credit, 2)) . '</td>
                        <td style="border-top: 1px solid #b0b0b0">' . number_format($balance, 2) . '</td>
                    </tr>';
            }

            $pdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
            $pdf->SetHTMLHeader('<img src="img/header.png" alt="" style="width: 400px; margin: auto">');
            $pdf->SetHTMLFooter(
                '<div style="text-align: right; font-size: .75rem">
                    <div>Printed by: ' . Auth::user()->name . '</div>
                    <div>Date: ' . date('d-m-Y h:i a') . '</div>
                </div>'
            );
            $pdf->AddPage(
                '',
                '',
                '',
                '',
                '',
                5,
                5,
                40,
                18,
                0,
                5
            );
            $css = file_get_contents('css/pdf.css');
            $pdf->WriteHTML($css, 1);
            $pdf->WriteHTML(
                '<div class="main">
                    <h3 class="text-center">Ledger of Account</h3>
                    <table style="width: 100%; font-size: .85rem">
                        <tr>
                            <td><b>Account no: ' . $id . '</b></td>
                        </tr>
                    </table>
                    <table style="width: 100%; font-size: .85rem">
                        <tr>
                            <td>Name: ' . $name . '</td>
                            <td class="col-4">Mobile: ' . $mobile . '</td>
                            <td class="col-4">Address: ' . $address . '</td>
                        </tr>
                    </table>
                    <h4 class="text-center">Transactions from ' . date('d-M-Y', strtotime($start)) . ' to ' . date('d-M-Y', strtotime($end)) . '</h4>
                    <table>
                        <thead>
                            <tr style="text-align: left">
                                <th width="100" align="left">Date</th>
                                <th width="85" align="left">Memo no.</th>
                                <th align="left">Description</th>
                                <th align="left">Type</th>
                                <th align="left">Debit</th>
                                <th align="left">Credit</th>
                                <th align="left">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-bold">
                                <td colspan="6">Initial Balance</td>
                                <td>' . number_format($init_balance, 2) . '</td>
                            </tr>
                            ' . $pdfTableRows . '
                            <tr class="font-bold">
                                <td colspan="4" style="border-top: 1px solid black"></td>
                                <td style="border-top: 1px solid black"><b>' . number_format($totalDr, 2) . '</b></td>
                                <td style="border-top: 1px solid black"><b>' . number_format($totalCr, 2) . '</b></td>
                                <td style="border-top: 1px solid black"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>',
                2
            );

            $pdf->Output($pdfName, 'I');
            return response(['message' => 'Success', 200]);
        } catch (Exception $e) {
            return response([
                'message' => 'Something went wrong'
            ], 200);
        }
    }
}

<?php

namespace App\Exports;

use App\Models\Withdrawals;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WithdrawalsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Start with a query on Withdrawals
        $query = Withdrawals::query()
            ->select(
                'withdrawals.id',
                'users.name as user_name',
                'withdrawals.amount',
                'withdrawals.status', // Include status field
                'withdrawals.datetime',
                'bank_details.bank_name',
                'bank_details.branch_name',
                'bank_details.account_number',
                'bank_details.account_holder_name',
                'bank_details.ifsc_code'
            )
            ->join('users', 'withdrawals.user_id', '=', 'users.id')
            ->leftJoin('bank_details', 'users.id', '=', 'bank_details.user_id'); // Assuming bank_details is the correct table name

        // Apply filters if needed
        if (isset($this->filters['status'])) {
            $query->where('withdrawals.status', $this->filters['status']);
        }

        if (isset($this->filters['filter_date'])) {
            $query->whereDate('withdrawals.datetime', $this->filters['filter_date']);
        }

        // Get the withdrawals data with related user and bank details
        $withdrawalsData = $query->get();

        // Map through the data to format status as needed
        return $withdrawalsData->map(function ($withdrawal) {
            // Map numeric status to descriptive text
            $statusDescription = match($withdrawal->status) {
                0 => 'Pending',
                1 => 'Paid',
                2 => 'Cancelled',
                default => 'Unknown', // Fallback for any unexpected status
            };

            return [
                'id' => $withdrawal->id,
                'user_name' => $withdrawal->user_name,
                'amount' => $withdrawal->amount,
                'status' => $statusDescription, // Add descriptive status
                'datetime' => $withdrawal->datetime,
                'bank_name' => $withdrawal->bank_name,
                'branch_name' => $withdrawal->branch_name,
                'account_number' => $withdrawal->account_number,
                'account_holder_name' => $withdrawal->account_holder_name,
                'ifsc_code' => $withdrawal->ifsc_code,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Amount',
            'Status', // Column for descriptive status
            'Datetime',
            'Bank Name',
            'Branch Name',
            'Account Number',
            'Account Holder Name',
            'IFSC Code',
        ];
    }
}

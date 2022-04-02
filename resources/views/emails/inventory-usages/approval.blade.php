@extends('emails.template')

@section('content')
    <div class="body-text">
        Hello Mrs/Mr/Ms {{ $approver_name }},
        <br>
        You have an approval request for Inventory Usage. We would like to details as follows:
        <br>
        <table style="width: 100%; border-collapse: collapse;margin-top: 2rem; margin-bottom: 2rem" border="0">
            <tr>
                <th style="padding: .5rem">Form Number</th>
                <td style="padding: .5rem">
                    {{ $form->number }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Date Form</th>
                <td style="padding: .5rem">
                    {{ $day_time }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Employee</th>
                <td style="padding: .5rem">
                    {{ 'employee?' }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Warehouse</th>
                <td style="padding: .5rem">
                    {{ $warehouse->name }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Created At</th>
                <td style="padding: .5rem">
                    {{ $created_at }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Created By</th>
                <td style="padding: .5rem">
                    {{ $created_by }}
                </td>
            </tr>
            <tr>
                <th style="padding: .5rem">Notes</th>
                <td style="padding: .5rem">
                    {{ $notes }}
                </td>
            </tr>
        </table>
        <br>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem" border="1">
            <tr>
                <th style="padding: .5rem">No</th>
                <th style="padding: .5rem">Item</th>
                <th style="padding: .5rem">Chart of Account</th>
                <th style="padding: .5rem">Quantity Usage</th>
                <th style="padding: .5rem">Notes</th>
                <th style="padding: .5rem">Allocation</th>
            </tr>
            <tbody>
                @if (isset($items) && count($items) > 0)
                    @foreach ($items as $key => $item)
                        <tr>
                            <td style="padding: .5rem">
                                {{ $key + 1 }}
                            </td>
                            <td style="padding: .5rem">
                                {{ $item['item_name'] }}
                            </td>
                            <td style="padding: .5rem">
                                {{ $item['chart_of_account_name'] }}
                            </td>
                            <td style="padding: .5rem">
                                {{ $item['quantity'] }}
                            </td>
                            <td style="padding: .5rem">
                                {{ $item['notes'] }}
                            </td>
                            <td style="padding: .5rem">
                                {{ $item['allocation_name'] }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <br>
        <table style="width: 50%; border-collapse: collapse;" border="0">
            <tr>
                <th style="padding-right: 1rem">
                    <a href="{{ $urls['check_url'] }}"
                        style="background-color: #343a40; border: none; border-radius: 5px; color: white; margin:8px 0; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; ">
                        Check
                    </a>
                </th>
                <th style="padding-right: 1rem">
                    <a href="{{ $urls['approve_url'] }}"
                        style="background-color: #3f9ce8; border: none; border-radius: 5px; color: white; margin:8px 0; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; ">
                        Approve
                    </a>
                </th>
                <th style="padding-right: 1rem">
                    <a href="{{ $urls['reject_url'] }}"
                        style="background-color: #ef5350; border: none; border-radius: 5px; color: white; margin:8px 0; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; ">
                        Reject
                    </a>
                </th>
            </tr>
        </table>

        <br>
    </div>
@stop

@extends('emails.template')

@section('content')
    <div class="body-text">
        Hello Mrs/Mr/Ms {{ $payload['employee_name'] }},
        <br>
        The Receipt for your form inventory usage is attached to this email.
        <br>
        <br>
        <p>Thankyou</p>
        <br>
        <br>
        <p>
            {{ '<' . $payload['created_by'] . '>' }}
        </p>
    </div>
@stop

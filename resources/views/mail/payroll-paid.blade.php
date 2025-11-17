<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Paid</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: #4f46e5;
            color: white;
            text-align: center;
            padding: 25px 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .content {
            padding: 25px;
            line-height: 1.6;
        }

        .content h2 {
            margin-top: 0;
            color: #111827;
            font-size: 20px;
        }

        .details {
            margin-top: 15px;
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
        }

        .details p {
            margin: 6px 0;
            font-size: 15px;
        }

        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 15px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Payroll has been Paid 💰</h1>
        </div>

        <div class="content">
            <h2>Hai {{ $payroll->employee->full_name }},</h2>
            <p>
                We glad to let you know that your Payroll Period
                <strong>{{ $payroll->period ?? '-' }}</strong> has been <strong>Paid</strong>
                on {{ now()->format('d M Y') }}.
            </p>

            <div class="details">
                <p><strong>Name:</strong> {{ $payroll->employee->full_name }}</p>
                <p><strong>Position:</strong> {{ $payroll->employee->position ?? '-' }}</p>
                <p><strong>Total Salary:</strong> Rp{{ number_format($payroll->total_salary ?? 0, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ $payroll->status }}</p>
            </div>



            <p style="margin-top: 20px;">Thanks for your hardwork! 💪</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Confidential. All rights reserved</p>
        </div>
    </div>
</body>

</html>

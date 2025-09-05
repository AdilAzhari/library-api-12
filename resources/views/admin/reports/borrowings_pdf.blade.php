<!DOCTYPE html>
<html>
<head>
    <title>Borrowings Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .date-range {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Library Borrowings Report</h1>
    <div class="date-range">
        <p>{{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>Book Title</th>
        <th>Book Author</th>
        <th>User Name</th>
        <th>User Email</th>
        <th>Borrowed Date</th>
        <th>Due Date</th>
        <th>Returned Date</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($borrowings as $borrowing)
        <tr>
            <td>{{ $borrowing->book->title }}</td>
            <td>{{ $borrowing->book->author }}</td>
            <td>{{ $borrowing->user->name }}</td>
            <td>{{ $borrowing->user->email }}</td>
            <td>{{ $borrowing->borrowed_at->format('Y-m-d') }}</td>
            <td>{{ $borrowing->due_date->format('Y-m-d') }}</td>
            <td>{{ $borrowing->returned_at?->format('Y-m-d') ?? '' }}</td>
            <td>
                @if($borrowing->returned_at)
                    Returned
                @elseif($borrowing->due_date->isPast())
                    Overdue
                @else
                    Active
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer" style="margin-top: 20px; text-align: right;">
    <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
</div>
</body>
</html>

<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminReportController extends Controller
{
    public function borrowings()
    {
        $dateRange = request('date_range', 'month');
        $startDate = request('start_date');
        $endDate = request('end_date');

        // Set default dates based on range
        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonth();
                $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
                break;
            default:
                $startDate = Carbon::now()->subMonth();
                $endDate = Carbon::now();
        }

        $borrowings = Borrow::with(['book', 'user'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->orderBy('borrowed_at', 'desc')
            ->paginate(20);

        $chartData = $this->generateBorrowingsChartData($startDate, $endDate);
        $topBooks = $this->getTopBooks($startDate, $endDate);

        return Inertia::render('Admin/Reports/Borrowings', [
            'reportData' => [
                'chart' => $chartData,
                'topBooks' => $topBooks,
                'borrowings' => $borrowings
            ],
            'filters' => [
                'date_range' => $dateRange,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]
        ]);
    }

    public function overdue()
    {
        $overdueBorrowings = Borrow::with(['book', 'user'])
            ->overdue()
            ->orderBy('due_date')
            ->paginate(20);

        return Inertia::render('Admin/Reports/Overdue', [
            'borrowings' => $overdueBorrowings
        ]);
    }

    protected function generateBorrowingsChartData($startDate, $endDate)
    {
        $borrowingsByDay = Borrow::selectRaw('DATE(borrowed_at) as date, COUNT(*) as count')
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $returnsByDay = Borrow::selectRaw('DATE(returned_at) as date, COUNT(*) as count')
            ->whereNotNull('returned_at')
            ->whereBetween('returned_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $borrowingsData = [];
        $returnsData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $date = $current->format('Y-m-d');
            $dayLabel = $current->format('M j');

            $labels[] = $dayLabel;
            $borrowingsData[] = $borrowingsByDay->firstWhere('date', $date)?->count ?? 0;
            $returnsData[] = $returnsByDay->firstWhere('date', $date)?->count ?? 0;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Borrowings',
                    'data' => $borrowingsData,
                    'backgroundColor' => '#2c3e50'
                ],
                [
                    'label' => 'Returns',
                    'data' => $returnsData,
                    'backgroundColor' => '#3498db'
                ]
            ]
        ];
    }

    protected function getTopBooks($startDate, $endDate)
    {
        return Borrow::selectRaw('book_id, COUNT(*) as borrow_count')
            ->with('book')
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->groupBy('book_id')
            ->orderByDesc('borrow_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->book->title,
                    'author' => $item->book->author,
                    'borrow_count' => $item->borrow_count
                ];
            });
    }

    public function exportBorrowingsCSV()
    {
        $startDate = request('start_date', Carbon::now()->subMonth());
        $endDate = request('end_date', Carbon::now());

        $borrowings = Borrow::with(['book', 'user'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->orderBy('borrowed_at')
            ->get();

        $filename = "borrowings_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($borrowings) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Book Title',
                'Book Author',
                'User Name',
                'User Email',
                'Borrowed Date',
                'Due Date',
                'Returned Date',
                'Status'
            ]);

            // Data rows
            foreach ($borrowings as $borrowing) {
                fputcsv($file, [
                    $borrowing->book->title,
                    $borrowing->book->author,
                    $borrowing->user->name,
                    $borrowing->user->email,
                    $borrowing->borrowed_at->format('Y-m-d'),
                    $borrowing->due_date->format('Y-m-d'),
                    $borrowing->returned_at?->format('Y-m-d') ?? '',
                    $borrowing->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBorrowingsPDF()
    {
        $startDate = request('start_date', Carbon::now()->subMonth());
        $endDate = request('end_date', Carbon::now());

        $borrowings = Borrow::with(['book', 'user'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->orderBy('borrowed_at')
            ->get();

        $pdf = PDF::loadView('admin.reports.borrowings_pdf', [
            'borrowings' => $borrowings,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $filename = "borrowings_report_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}.pdf";

        return $pdf->download($filename);
    }
}

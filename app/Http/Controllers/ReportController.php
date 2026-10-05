<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ReadingPlanStatus;
use Illuminate\View\View;
use App\Services\ReportService;

class ReportController extends Controller
{
    /**
     *
     * @return View
     */
    public function index(ReportService $reportService): View
    {
        $user = Auth()->user();
        $stats = $reportService->getStats($user);

        return view('reports.index', compact('stats'));
    }
}

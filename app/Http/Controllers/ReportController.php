<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $total_reviews = $user->reviews()->count();
        $stats = [
            'summary' => [
                'total_reviews' => $total_reviews,
            ]
        ];
        return view('reports.index',compact('stats'));
    }
}

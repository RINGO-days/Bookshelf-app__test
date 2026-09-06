<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Enums\ReadingPlanStatus;
use App\Http\Requests\ReadingPlansCreateRequest;
use App\Http\Requests\ReadingPlansEditRequest;

class ReadingPlansController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = $request->query('status');
        $readingPlans = ReadingPlan::when($request->query('status'), function ($query) use ($currentStatus) {
            $query->where('status', $currentStatus);
        })->get();

        return view('reading-plans.index', compact('currentStatus', 'readingPlans'));
    }

    public function create()
    {
        $books = Book::all();
        return view('reading-plans.create', compact('books'));
    }

    public function store(ReadingPlansCreateRequest $request)
    {
        ReadingPlan::create([
            'user_id' => auth()->id(),
            'book_id' => $request->validated('book_id'),
            'target_date' => $request->validated('target_date')
        ]);

        return redirect('/reading-plans');
    }

    public function complete(ReadingPlan $plan)
    {
        $plan->update([
            'completed_at' => now(),
            'status' => ReadingPlanStatus::Completed
        ]);
        return redirect('/reading-plans')->with('success', '読書計画のステータスを「読了」にしました。');
    }

    public function edit($plan)
    {
        $readingPlan = ReadingPlan::find($plan);
        return view('reading-plans.edit', compact('readingPlan'));
    }

    public function update(ReadingPlansEditRequest $request, ReadingPlan $plan)
    {
        $plan->update([
            'target_date' => $request->target_date,
        ]);

        return redirect('reading-plans')->with('success', '期日を変更しました。');
    }

    public function destroy(ReadingPlan $plan)
    {
        $plan->delete();

        return redirect('/reading-plans')->with('success', '読書計画を削除しました。');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Enums\ReadingPlanStatus;
use App\Http\Requests\ReadingPlansCreateRequest;
use App\Http\Requests\ReadingPlansEditRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ReadingPlansController extends Controller
{
    /**
     * 読書計画画面にて計画一覧を表示する
     * クエリパラメータよりステータスに応じた通知を取得する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $currentStatus = $request->query('status');
        $readingPlans = ReadingPlan::when($currentStatus, function ($query) use ($currentStatus) {
            $query->where('status', $currentStatus);
        })->get();

        return view('reading-plans.index', compact('currentStatus', 'readingPlans'));
    }

    /**
     * 新規で読書計画を作成する画面
     *
     * @return View
     */
    public function create(): View
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画作成画面にて読書計画を作成するアクション
     *
     * @param ReadingPlansCreateRequest $request
     * @return RedirectResponse
     */
    public function store(ReadingPlansCreateRequest $request): RedirectResponse
    {
        DB::transaction(function()use($request){
            ReadingPlan::create([
                'user_id' => auth()->id(),
                'book_id' => $request->validated('book_id'),
                'target_date' => $request->validated('target_date')
            ]);
        });

        return redirect('/reading-plans')->with('success', '読書計画を作成しました。');
    }

    /**
     * 読書計画一覧画面にて、読書計画を読了に更新するアクション
     *
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function complete(ReadingPlan $plan): RedirectResponse
    {
        DB::transaction(function()use($plan){
            $this->authorize('update', $plan);
            $plan->update([
                'completed_at' => now(),
                'status' => ReadingPlanStatus::Completed
            ]);
        });

        return redirect('/reading-plans')->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画を編集する画面の表示
     *
     * @param int $plan
     * @return View
     */
    public function edit($plan): View
    {
        $this->authorize('update', $plan);
        $readingPlan = ReadingPlan::find($plan);

        return view('reading-plans.edit', compact('readingPlan'));
    }

    /**
     * 読書計画編集画面にて読書計画を更新するアクション
     *
     * @param ReadingPlansEditRequest $request
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function update(ReadingPlansEditRequest $request, ReadingPlan $plan): RedirectResponse
    {
        DB::transaction(function()use($request,$plan){
            $this->authorize('update', $plan);
            $plan->update([
                'target_date' => $request->target_date,
            ]);
        });

        return redirect('reading-plans')->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画一覧画面にて、読書計画を削除するアクション
     *
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function destroy(ReadingPlan $plan): RedirectResponse
    {
        DB::transaction(function()use($plan){
            $this->authorize('delete', $plan);
            $plan->delete();
        });

        return redirect('/reading-plans')->with('success', '読書計画を削除しました。');
    }
}

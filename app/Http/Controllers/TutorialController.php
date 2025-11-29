<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\TutorialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorialController extends Controller
{
    /**
     * Method SHOW (Yang Error Tadi)
     * Fungsinya: Saat user buka /tutorial/1, otomatis cari step pertama
     * lalu lempar ke halaman belajar.
     */
    public function show($tutorialId)
    {
        // Cari step pertama dari tutorial ini (urutan terkecil)
        $firstStep = TutorialStep::where('tutorial_id', $tutorialId)
                        ->orderBy('step_number', 'asc')
                        ->first();

        if ($firstStep) {
            // Kalau ada step-nya, langsung redirect ke halaman step
            return redirect()->route('tutorial.step', $firstStep->step_id);
        }

        // Kalau tutorialnya masih kosong (belum ada step)
        return abort(404, 'Tutorial ini belum memiliki materi.');
    }

    public function showStep($stepId)
    {
        $step = TutorialStep::with('tutorial')->findOrFail($stepId);

        $tutorialId = $step->tutorial_id;

        $totalSteps = TutorialStep::where('tutorial_id', $tutorialId)->count();

        $nextStep = TutorialStep::where('tutorial_id', $tutorialId)
                        ->where('step_number', '>', $step->step_number)
                        ->orderBy('step_number', 'asc')
                        ->first();

        $prevStep = TutorialStep::where('tutorial_id', $tutorialId)
                        ->where('step_number', '<', $step->step_number)
                        ->orderBy('step_number', 'desc')
                        ->first();

        $progress = TutorialProgress::where('user_id', Auth::id())
            ->where('step_id', $stepId)
            ->first();

        return view('tutorials.show', compact('step', 'progress', 'totalSteps', 'nextStep', 'prevStep'));
    }

    public function submitCode(Request $request, $stepId)
    {
        $request->validate([
            'user_code' => 'required'
        ]);

        $step = TutorialStep::findOrFail($stepId);

        $cleanUserCode = str_replace(["\r", "\n", " "], '', $request->user_code);
        $cleanSolution = str_replace(["\r", "\n", " "], '', $step->solution_code);

        $isCorrect = $cleanUserCode === $cleanSolution;

        TutorialProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'step_id' => $stepId
            ],
            [
                'user_code' => $request->user_code,
                'is_completed' => $isCorrect,
                'completed_at' => $isCorrect ? now() : null
            ]
        );

        if ($isCorrect) {
            return redirect()->back()->with('success', 'Selamat! Kode kamu benar. Silakan lanjut.');
        } else {
            return redirect()->back()->with('error', 'Kode belum tepat. Coba perhatikan petunjuk lagi.')->withInput();
        }
    }

    public function index()
    {
        $tutorials = Tutorial::with('course')->get();
        
        return view('tutorials.index', compact('tutorials'));
    }

}

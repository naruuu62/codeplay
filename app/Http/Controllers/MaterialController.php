<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\MaterialProgress;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{

    // Menampilkan daftar semua materi
    public function index()
    {
        // Ambil data materi, kita eager load 'course' agar efisien
        // paginate(10) artinya memunculkan 10 materi per halaman
        $materials = CourseMaterial::with('course')->latest()->paginate(10);

        // Pastikan kamu nanti membuat file view di: resources/views/materials/index.blade.php
        return view('materials.index', compact('materials'));
    }
    
    // Tampilkan materi
    public function show($materialId)
    {
        $material = CourseMaterial::where('material_id', $materialId)->firstOrFail();

        $course = $material->course;

        $course->load(['materials', 'tutorials', 'quizzes']);

        $enrollment = UserEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->course_id)
            ->firstOrFail();
        // Get or create progress
        $progress = MaterialProgress::firstOrCreate([
            'user_id' => Auth::id(),
            'material_id' => $materialId
        ]);

        return view('materials.show', compact('material', 'course', 'enrollment', 'progress'));
    }

    // Update progress materi
    public function updateProgress(Request $request, $materialId)
    {
        $progress = MaterialProgress::where('user_id', Auth::id())
            ->where('material_id', $materialId)
            ->first();

        if ($progress) {
            $progress->last_position = $request->input('position', 0);
            
            if ($request->has('completed') && $request->completed) {
                $progress->is_completed = true;
                $progress->completed_at = now();
            }
            
            $progress->save();

            // Update enrollment progress
            $this->updateEnrollmentProgress($materialId);
        }

        return response()->json(['success' => true]);
    }

    // Download materi
    public function download($materialId)
    {
        $material = CourseMaterial::findOrFail($materialId);

        // Log download
        \App\Models\Download::create([
            'user_id' => Auth::id(),
            'material_id' => $materialId
        ]);

        return response()->download(public_path($material->file_url));
    }

public function streamPdf($id)
{
    $material = \App\Models\CourseMaterial::findOrFail($id);
    
    // Asumsi di database isinya: "materials/laravel-cheatsheet.pdf"
    $filename = $material->file_url; 

    // PENTING: Kita paksa pakai disk 'public'
    if (Storage::disk('public')->exists($filename)) {
        
        // Ambil full path dari disk public
        $fullPath = Storage::disk('public')->path($filename);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
        ]);
    }

    // Debugging: Kalau masih error, kasih tau dia nyari dimana
    return response()->json([
        'error' => 'File tidak ditemukan',
        'lokasi_seharusnya' => storage_path('app/public/' . $filename),
        'cek_disk_public' => Storage::disk('public')->path($filename),
    ], 404);
}    private function updateEnrollmentProgress($materialId)
    {
        $material = CourseMaterial::findOrFail($materialId);
        $courseId = $material->course_id;

        $totalMaterials = CourseMaterial::where('course_id', $courseId)->count();
        $completedMaterials = MaterialProgress::where('user_id', Auth::id())
            ->whereHas('material', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->where('is_completed', true)
            ->count();

        $progress = $totalMaterials > 0 ? ($completedMaterials / $totalMaterials) * 100 : 0;

        UserEnrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->update(['progress_percentage' => $progress]);
    }
    
   
}
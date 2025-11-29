<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Halaman Utama Dashboard (Gateway)
     * Fungsinya: Mengarahkan user ke halaman dashboard spesifik berdasarkan role-nya.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Jaga-jaga kalau sesi habis
        if (!$user) {
            return redirect()->route('login');
        }

        // Cek Role dan Redirect ke route yang sesuai
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'mentor':
                return redirect()->route('mentor.dashboard');
            default: // Default untuk 'pelajar'
                return redirect()->route('user.dashboard');
        }
    }

    /**
     * Dashboard Khusus Pelajar (User)
     */
    public function userDashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil kursus yang published & verified
        $query = Course::where('is_published', true)
                       ->where('is_verified', true)
                       ->with('mentor', 'category');

        // Filter by Category
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filter by Level
        if ($request->has('level') && $request->level != '') {
            $query->where('level', $request->level);
        }

        // Filter Search
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->paginate(12);
        $categories = Category::all();

        // Ambil daftar ID kursus yang sudah diikuti user untuk cek status enroll
        // Menggunakan primary key user_id sesuai struktur tabel kamu
        $enrolledCourseIds = UserEnrollment::where('user_id', $user->user_id) 
            ->pluck('course_id')
            ->toArray();

        return view('user.dashboard', compact('user', 'courses', 'categories', 'enrolledCourseIds'));
    }
}
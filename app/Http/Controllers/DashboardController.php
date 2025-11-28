<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\UserEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

    }
    public function userDashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $query = Course::where('is_published', true)
                       ->where('is_verified', true)
                       ->with('mentor', 'category');

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('level') && $request->level != '') {
            $query->where('level', $request->level);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->paginate(12);

        $categories = Category::all();

        $enrolledCourseIds = UserEnrollment::where('user_id', $user->user_id) // Pastikan pake user_id atau id sesuai tabel
            ->pluck('course_id')
            ->toArray();

        return view('user.dashboard', compact('user', 'courses', 'categories', 'enrolledCourseIds'));
    }
}
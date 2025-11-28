<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard()
    {
        $pendingUsers = User::where('is_verified', false)->count();
        $pendingCourses = Course::where('is_verified', false)->count();
        $totalUsers = User::count();
        $totalCourses = Course::count();

        // Get all users with enrollments count
        $users = User::withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all courses with mentor, category, enrollments
        $courses = Course::with(['mentor', 'category'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'pendingUsers',
            'pendingCourses',
            'totalUsers',
            'totalCourses',
            'users',
            'courses'
        ));
    }

    // Verifikasi user
    public function verifyUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_verified = true;
        $user->save();

        // Log activity
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'verify_user',
            'target_type' => 'user',
            'target_id' => $userId,
            'description' => "Verified user: {$user->username}"
        ]);

        return redirect()->back()->with('success', "User {$user->full_name} berhasil diverifikasi!");
    }

    // Verifikasi course
    public function verifyCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->is_verified = true;
        $course->save();

        // Log activity
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'verify_course',
            'target_type' => 'course',
            'target_id' => $courseId,
            'description' => "Verified course: {$course->title}"
        ]);

        return redirect()->back()->with('success', "Kursus {$course->title} berhasil diverifikasi!");
    }

    // Delete user
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting yourself
        if ($userId == Auth::id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $userName = $user->full_name;
        $user->delete();

        // Log activity
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_user',
            'target_type' => 'user',
            'target_id' => $userId,
            'description' => "Deleted user: {$userName}"
        ]);

        return redirect()->back()->with('success', "User {$userName} berhasil dihapus!");
    }

    // Delete course
    public function deleteCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $courseTitle = $course->title;
        $course->delete();

        // Log activity
        AdminLog::create([
            'admin_id' => Auth::id(),
            'action' => 'delete_course',
            'target_type' => 'course',
            'target_id' => $courseId,
            'description' => "Deleted course: {$courseTitle}"
        ]);

        return redirect()->back()->with('success', "Kursus {$courseTitle} berhasil dihapus!");
    }
}
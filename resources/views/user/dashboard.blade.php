<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Dashboard — CodePlay</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-light">
  <header class="app-header">
    <div class="container app-header-inner">
      <a href="{{ route('user.dashboard') }}" class="brand">
        <img src="{{ asset('assets/logo.svg') }}" class="logo">
        <span class="brand-name">CodePlay</span>
      </a>
      <nav class="app-nav">
        <a href="{{ route('user.dashboard') }}" class="nav-link active">Courses</a>
        <a href="{{ route('materials.index') }}" class="nav-link">Materials</a>
        <a href="{{ route('progress.index')}}" class="nav-link">Progress</a>
        <a href="{{ route('forum.index')}}" class="nav-link">Forum</a>
      </nav>
      <div class="profile">
        {{-- Menggunakan UI Avatars sebagai fallback jika user tidak punya foto --}}
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->username) }}&background=random" alt="Profile" class="avatar" />
        <div class="profile-info">
          <span class="name">{{ $user->username }}</span>
          <span class="role text-muted">Student</span>
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    <section class="welcome card">
      <div>
        <h1 class="h3">Welcome back, {{ $user->username }}</h1>
        <p class="text-muted">Continue where you left off or explore new courses.</p>
      </div>
    </section>

    <form action="{{ route('user.dashboard') }}" method="GET" class="filter-bar card">
      
      {{-- Tambahan: Input Search --}}
      <div class="filter-group">
        <label for="search">Search</label>
        <input type="text" name="search" id="search" class="select" placeholder="Search courses..." value="{{ request('search') }}">
      </div>

      <div class="filter-group">
        <label for="category">Category</label>
        {{-- onchange="this.form.submit()" agar otomatis reload saat dipilih --}}
        <select name="category" id="category" class="select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->category_id }}" {{ request('category') == $cat->category_id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="filter-group">
        <label for="level">Level</label>
        <select name="level" id="level" class="select" onchange="this.form.submit()">
          <option value="">All Levels</option>
          <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
          <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
          <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
        </select>
      </div>

      {{-- Tombol Reset --}}
      <div class="filter-group">
          <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary" style="padding: 10px; text-decoration:none; border:1px solid #ccc; border-radius:8px;">Reset</a>
      </div>
    </form>

    {{-- 2. BAGIAN GRID COURSES (DINAMIS) --}}
    <section class="courses-grid">
      @forelse($courses as $course)
        <article class="course-card card card-elevated">
          {{-- Gambar Thumbnail (Pastikan ada kolom 'thumbnail' di database, atau pakai default) --}}
          <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : 'https://placehold.co/600x400?text=No+Image' }}" 
               alt="{{ $course->title }}" class="course-thumb" />
          
          <div class="course-content">
            <div class="course-top">
              <h3>{{ $course->title }}</h3>
              {{-- Badge Level (Warna bisa disesuaikan pakai if/switch kalau mau) --}}
              <span class="level tag">{{ ucfirst($course->level) }}</span>
            </div>
            
            <p class="text-muted">{{ Str::limit($course->description, 80) }}</p>
            
            {{-- Cek apakah user sudah terdaftar --}}
            @if(in_array($course->course_id, $enrolledCourseIds))
                {{-- TAMPILAN JIKA SUDAH ENROLL (Ada Progress Bar) --}}
                <div class="progress-wrap">
                    {{-- Catatan: Progress dinamis butuh logic tambahan di controller, sementara kita set statis atau ambil jika ada --}}
                    <div class="progress-bar"><span class="progress" style="width: 20%"></span></div>
                    <span class="progress-label">Enrolled</span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('course.learn', $course->slug) }}" class="btn btn-primary">Continue learning</a>
                </div>
            @else
                {{-- TAMPILAN JIKA BELUM ENROLL --}}
                <div class="progress-wrap" style="opacity: 0; visibility: hidden;">
                    <div class="progress-bar"></div> </div>
                <div class="card-actions">
                    <a href="{{ route('course.show', $course->slug) }}" class="btn btn-outline-primary w-100">View Details</a>
                </div>
            @endif

          </div>
        </article>
      @empty
        {{-- TAMPILAN JIKA TIDAK ADA COURSE --}}
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
            <i class="fa-solid fa-box-open" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
            <h3>No courses found</h3>
            <p class="text-muted">Try adjusting your filters or search keywords.</p>
        </div>
      @endforelse
    </section>

    {{-- 3. PAGINATION --}}
    <div style="margin-top: 32px; display: flex; justify-content: center;">
        {{ $courses->withQueryString()->links() }}
    </div>

  </main>

  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
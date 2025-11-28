<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Forum</title>
    <link href="{{ asset('css/forum.css') }}" rel="stylesheet">
</head>
<body>
<div class="forum-container">
    <header class="forum-header">
        <h1>Forum</h1>
        <a class="btn primary" href="{{ route('forum.create', $courseId ?? null) }}">Buat Thread Baru</a>
    </header>

    <div class="threads">
        @foreach($threads as $thread)
            <article class="thread {{ $thread->is_pinned ? 'pinned' : '' }}">
                <div class="thread-main">
                    <a class="thread-title" href="{{ route('forum.show', $thread->thread_id) }}">
                        {{ $thread->title }}
                    </a>
                    <p class="thread-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($thread->content), 180) }}</p>
                </div>
                <div class="thread-meta">
                    <div class="meta-left">
                        <span class="author">oleh {{ $thread->user->name ?? 'Guest' }}</span>
                        <span class="course">{{ $thread->course->name ?? '' }}</span>
                    </div>
                    <div class="meta-right">
                        @if($thread->is_pinned)<span class="badge pinned-badge">Pinned</span>@endif
                        <span class="count">💬 {{ $thread->replies_count ?? $thread->replies->count() }}</span>
                        <span class="count">👀 {{ $thread->view_count ?? 0 }}</span>
                        <time class="updated">{{ $thread->updated_at->diffForHumans() }}</time>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="pagination">
        {{ $threads->links() }}
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $step->title }} — CodePlay</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-box { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .editor-dark { background-color: #0f172a; color: #22c55e; border: none; font-family: 'Fira Code', monospace; width: 100%; height: 250px; padding: 16px; border-radius: 0 0 8px 8px; resize: vertical; outline: none; }
    .instruction-box { background-color: #eff6ff; border-radius: 8px; padding: 20px; color: #334155; line-height: 1.6; font-size: 15px; margin-top: 16px; margin-bottom: 24px; }
    .btn-nav { padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-prev { background: white; border: 1px solid #cbd5e1; color: #64748b; }
    .btn-prev:hover { border-color: #94a3b8; color: #475569; }
    .btn-next { background: #3b82f6; border: 1px solid #3b82f6; color: white; }
    .btn-next:hover { background: #2563eb; }
    .btn-run { background: #22c55e; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; }
    .btn-run:hover { background: #16a34a; }
  </style>
</head>
<body>

  <header class="app-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
      <a href="{{ route('dashboard') }}" class="brand" style="text-decoration: none; color: #1e293b; font-weight: 700; font-size: 20px; display: flex; align-items: center; gap: 8px;">
        <span>🏠 CodePlay</span>
      </a>
      <nav class="app-nav" style="display: flex; gap: 24px;">
        <span class="nav-link font-bold" style="color: #64748b;">{{ $step->tutorial->course->title ?? 'Belajar Coding' }}</span>
      </nav>
      <div class="profile" style="display: flex; align-items: center; gap: 12px;">
         <div style="text-align: right;">
            <div style="font-weight: 600; font-size: 14px; color: #1e293b;">{{ Auth::user()->full_name }}</div>
            <div style="font-size: 12px; color: #94a3b8;">Student</div>
         </div>
         <img src="https://ui-avatars.com/api/?name={{ Auth::user()->full_name }}&background=0D8ABC&color=fff" style="width: 40px; height: 40px; border-radius: 50%;">
      </div>
    </div>
  </header>
  <main style="max-width: 1200px; margin: 32px auto; padding: 0 24px;">
    
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
            <div style="display: flex; gap: 16px; align-items: center;">
                <div style="background: white; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-code" style="font-size: 24px; color: #3b82f6;"></i>
                </div>
                <div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">{{ $step->tutorial->title ?? 'Javascript Fundamentals' }}</h1>
                    <p style="color: #64748b; margin: 4px 0 0 0; font-size: 14px;">Materi Pembelajaran Interaktif</p>
                </div>
            </div>
            
            <a href="#" class="btn-prev" style="text-decoration: none; padding: 10px 24px; color: #1e293b; font-weight: 600;">
                Ambil Quiz
            </a>
        </div>

        @php $percentage = ($step->step_number / $totalSteps) * 100; @endphp
        <div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #475569;">
                <span>Progress Tutorial</span>
                <span>{{ round($percentage) }}%</span>
            </div>
            <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                <div style="width: {{ $percentage }}%; height: 100%; background: #3b82f6; border-radius: 4px; transition: width 0.5s;"></div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 40% 58%; gap: 2%;">
        
        <div class="card-box" style="display: flex; flex-direction: column; min-height: 500px;">
            
            <div style="color: #3b82f6; font-weight: 600; font-size: 14px; margin-bottom: 8px;">
                Langkah {{ $step->step_number }} dari {{ $totalSteps }}
            </div>

            <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">
                {{ $step->title }}
            </h2>

            <div class="instruction-box">
                {!! nl2br(e($step->instruction)) !!}
                
                <div style="margin-top: 16px; font-weight: 600; color: #1e293b;">Tugas Anda:</div>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li>Baca instruksi dengan teliti.</li>
                    <li>Tulis kode di editor sebelah kanan.</li>
                    <li>Klik tombol "Jalankan" untuk cek hasil.</li>
                </ul>

                @if($step->hint)
                    <div style="margin-top: 16px; border-top: 1px dashed #bfdbfe; padding-top: 12px; font-size: 13px; color: #2563eb;">
                        <i class="fa-solid fa-lightbulb"></i> <strong>Hint:</strong> {{ $step->hint }}
                    </div>
                @endif
            </div>

            <div style="margin-top: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    @if($prevStep)
                        <a href="{{ route('tutorial.step', $prevStep->step_id) }}" class="btn-nav btn-prev" style="text-decoration: none;">
                            <i class="fa-solid fa-chevron-left"></i> Sebelumnya
                        </a>
                    @else
                        <button class="btn-nav btn-prev" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fa-solid fa-chevron-left"></i> Sebelumnya
                        </button>
                    @endif

                    @if($nextStep)
                        @if(session('success') || ($progress && $progress->is_completed))
                            <a href="{{ route('tutorial.step', $nextStep->step_id) }}" class="btn-nav btn-next" style="text-decoration: none;">
                                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                             <button class="btn-nav btn-next" disabled style="opacity: 0.5; cursor: not-allowed; background: #94a3b8; border-color: #94a3b8;">
                                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('tutorials.index') }}" class="btn-nav btn-next" style="background: #10b981; border-color: #10b981; text-decoration: none;">
                            Selesai <i class="fa-solid fa-check"></i>
                        </a>
                    @endif
                </div>

                <div style="text-align: center; margin-top: 16px;">
                    <button onclick="alert('Solusi: \n{{ $step->solution_code }}')" style="background: none; border: none; color: #64748b; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: underline;">
                        Lihat Solusi
                    </button>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <form action="{{ route('tutorial.submit', $step->step_id) }}" method="POST">
                @csrf
                
                <div class="card-box" style="padding: 0; overflow: hidden; border: 1px solid #cbd5e1;">
                    <div style="background: white; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: #475569; font-size: 14px;">
                            {{ $step->title }}
                        </span>
                        
                        <button type="submit" class="btn-run">
                            <i class="fa-solid fa-play"></i> Jalankan
                        </button>
                    </div>

                    <textarea 
                        name="user_code" 
                        class="editor-dark" 
                        spellcheck="false"
                        placeholder="// Tulis kode Anda di sini..."
                    >{{ old('user_code', $progress->user_code ?? $step->code_template) }}</textarea>
                </div>
            </form>

            <div class="card-box" style="min-height: 200px; display: flex; flex-direction: column;">
                <div style="font-weight: 600; color: #475569; font-size: 14px; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                    Output
                </div>
                
                <div style="flex-grow: 1; font-family: 'Fira Code', monospace; font-size: 13px;">
                    
                    @if(session('success'))
                        <div style="color: #15803d;">
                            <i class="fa-solid fa-check"></i> System Output:<br>
                            > Process finished with exit code 0<br>
                            > <span style="font-weight: bold;">{{ session('success') }}</span>
                        </div>
                    @elseif(session('error'))
                        <div style="color: #b91c1c;">
                            <i class="fa-solid fa-times"></i> System Output:<br>
                            > Syntax Error / Logic Incorrect<br>
                            > <span style="font-weight: bold;">{{ session('error') }}</span>
                        </div>
                    @else
                        <span style="color: #94a3b8;">Output akan muncul disini setelah Anda klik "Jalankan"...</span>
                    @endif

                </div>
            </div>

        </div>

    </div>
  </main>

  <script>
    // Tab indent handler
    document.querySelector('textarea').addEventListener('keydown', function(e) {
      if (e.key == 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.substring(0, start) + "\t" + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 1;
      }
    });
  </script>
</body>
</html>
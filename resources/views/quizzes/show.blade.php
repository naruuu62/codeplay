<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mengerjakan {{ $quiz->title }} — CodePlay</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <style>
    /* Styling dari template HTML kamu */
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .quiz-header { background: white; border-bottom: 1px solid #e2e8f0; padding: 16px 0; position: sticky; top: 0; z-index: 50; }
    .quiz-header-inner { max-width: 800px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 24px; }
    .brand { display: flex; align-items: center; gap: 8px; font-weight: 700; color: #1e293b; text-decoration: none; font-size: 18px; }
    .brand-name { font-size: 16px; }
    .timer { font-family: monospace; font-size: 18px; font-weight: 600; background: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 6px; }
    
    .quiz-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-top: 32px; margin-bottom: 24px; }
    .question-title { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 24px; line-height: 1.5; }
    .question-badge { display: inline-block; background: #eff6ff; color: #3b82f6; font-size: 12px; font-weight: 600; padding: 4px 8px; border-radius: 4px; margin-bottom: 12px; }
    
    /* Styling Option/Jawaban */
    .option-card { display: flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; position: relative; }
    .option-card:hover { background-color: #f8fafc; border-color: #94a3b8; }
    .option-card input[type="radio"] { margin-right: 12px; accent-color: #3b82f6; width: 18px; height: 18px; cursor: pointer; }
    /* Highlight saat dipilih */
    .option-card.selected { border-color: #3b82f6; background-color: #eff6ff; box-shadow: 0 0 0 1px #3b82f6; }
    
    .btn-primary { background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; font-size: 16px; transition: background 0.2s; display: block; text-align: center; }
    .btn-primary:hover { background: #2563eb; }

    /* Coding Question Style */
    .coding-area { background: #1e293b; color: #e2e8f0; border-radius: 8px; padding: 16px; font-family: monospace; width: 100%; border: 1px solid #334155; margin-top: 8px; }
  </style>
</head>
<body class="bg-light">

  <!-- HEADER -->
  <header class="quiz-header">
    <div class="container quiz-header-inner">
      <div class="brand">
        <!-- Logo Kecil -->
        <span style="background: #3b82f6; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px;">CP</span>
        <span class="brand-name">{{ $quiz->title }}</span>
      </div>
      
      <!-- Timer (Mengambil data time_limit dari DB) -->
      <!-- Jika time_limit null, default 30 menit -->
      <div class="timer" id="timer" data-seconds="{{ ($quiz->time_limit ?? 30) * 60 }}">
        00:00
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="container" style="max-width: 800px; margin: 0 auto; padding: 0 24px; padding-bottom: 60px;">
    
    <!-- FORM SUBMIT KE CONTROLLER -->
    <!-- Action mengarah ke route 'quiz.submit' dengan parameter attemptId -->
    <form id="quizForm" action="{{ route('quiz.submit', $attempt->attempt_id) }}" method="POST">
        @csrf
        
        <!-- LOOPING PERTANYAAN -->
        @foreach($quiz->questions as $index => $question)
            <section class="card card-elevated quiz-card">
                <span class="question-badge">Soal {{ $index + 1 }} dari {{ $quiz->questions->count() }}</span>
                
                <h1 class="question-title">{!! nl2br(e($question->question_text)) !!}</h1>

                <!-- LOGIKA TAMPILAN BERDASARKAN TIPE SOAL -->
                @if($question->question_type === 'multiple_choice')
                    
                    <div class="options-group">
                        @foreach($question->options as $option)
                            <!-- Tambahkan onclick agar style berubah saat diklik -->
                            <label class="option-card" onclick="selectOption(this)">
                                <!-- Name: answers[question_id], Value: option_id -->
                                <input type="radio" name="answers[{{ $question->question_id }}]" value="{{ $option->option_id }}" required />
                                <span class="option-content">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>

                @elseif($question->question_type === 'coding')
                    
                    <!-- Area Koding Sederhana -->
                    <div class="form-group">
                        <label style="font-size: 14px; color: #64748b; margin-bottom: 8px; display: block;">Tulis kode jawaban Anda:</label>
                        <textarea 
                            name="answers[{{ $question->question_id }}]" 
                            class="coding-area" 
                            rows="6" 
                            placeholder="// Write your code here..."
                        ></textarea>
                    </div>

                @endif
            </section>
        @endforeach

        <div class="quiz-actions" style="margin-top: 32px;">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin mengumpulkan jawaban?')">
                Kirim Jawaban <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
            </button>
        </div>
    </form>

  </main>

  <!-- JAVASCRIPT -->
  <script>
    // 1. Logic Timer Mundur
    const timerEl = document.getElementById('timer');
    // Ambil total detik dari atribut data-seconds
    let time = parseInt(timerEl.getAttribute('data-seconds'));

    const interval = setInterval(() => {
        // Hitung Menit dan Detik
        const m = String(Math.floor(time / 60)).padStart(2, '0');
        const s = String(time % 60).padStart(2, '0');
        
        // Update Text
        timerEl.textContent = `${m}:${s}`;
        
        // Jika waktu habis
        if (time <= 0) {
            clearInterval(interval);
            alert("Waktu Habis! Jawaban Anda akan dikirim otomatis.");
            document.getElementById('quizForm').submit(); // Auto submit
        }
        time--;
    }, 1000);

    // 2. Logic Styling Pilihan Ganda
    function selectOption(label) {
        // Cari parent (quiz-card) dari label yang diklik
        const card = label.closest('.quiz-card');
        
        // Hapus class 'selected' dari semua opsi di dalam card ini saja
        const siblings = card.querySelectorAll('.option-card');
        siblings.forEach(el => el.classList.remove('selected'));
        
        // Tambahkan class 'selected' ke opsi yang diklik
        label.classList.add('selected');
    }
  </script>
</body>
</html>
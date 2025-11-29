<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bot Panel - Vision Community</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #00ff88;
      --secondary: #00cc6a;
      --dark: #0a0a0a;
      --light: #1a1a1a;
      --card-bg: #1e1e1e;
      --text-light: #ffffff;
      --text-gray: #888888;
    }
    
    body {
      background: linear-gradient(135deg, var(--dark) 0%, #1a1a2e 50%, #16213e 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      color: var(--text-light);
    }
    
    .navbar {
      background: rgba(30, 30, 30, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    }
    
    .card {
      background: var(--card-bg);
      border: 1px solid rgba(0, 255, 136, 0.1);
      border-radius: 15px;
      backdrop-filter: blur(10px);
    }
    
    .card-header {
      background: rgba(0, 255, 136, 0.1);
      border-bottom: 1px solid rgba(0, 255, 136, 0.2);
      color: var(--primary);
      font-weight: 600;
    }
    
    .btn-primary {
      background: var(--primary);
      border: none;
      color: #000;
      font-weight: 600;
      border-radius: 10px;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }
    
    .nav-tabs .nav-link {
      color: var(--text-gray);
      border: none;
    }
    
    .nav-tabs .nav-link.active {
      background: transparent;
      color: var(--primary);
      border-bottom: 2px solid var(--primary);
    }
    
    .project-card {
      transition: all 0.3s;
    }
    
    .project-card:hover {
      transform: translateY(-5px);
      border-color: var(--primary);
    }
    
    .status-running {
      color: var(--primary);
      animation: pulse 2s infinite;
    }
    
    .status-stopped {
      color: var(--text-gray);
    }
    
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }
    
    .terminal {
      background: #000;
      color: var(--primary);
      font-family: 'Courier New', monospace;
      border-radius: 10px;
      height: 300px;
      overflow-y: auto;
      padding: 15px;
      border: 1px solid var(--primary);
    }
    
    .log-entry {
      margin-bottom: 5px;
      font-size: 12px;
    }
    
    .log-time {
      color: var(--text-gray);
    }
    
    .upload-area {
      border: 2px dashed rgba(0, 255, 136, 0.3);
      border-radius: 15px;
      padding: 40px;
      text-align: center;
      transition: all 0.3s;
      cursor: pointer;
    }
    
    .upload-area:hover {
      border-color: var(--primary);
      background: rgba(0, 255, 136, 0.05);
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#">
        <span style="color: var(--primary)">🤖</span> Vision Bot Panel
      </a>
      <div class="navbar-nav ms-auto">
        <span class="nav-link text-light">Hoş geldin, {{ username }}! 👋</span>
        <a class="nav-link" href="/logout" style="color: var(--primary)">Çıkış Yap</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <!-- Flash Messages -->
    {% with messages = get_flashed_messages(with_categories=true) %}
      {% if messages %}
        {% for category, message in messages %}
          <div class="alert alert-{{ 'success' if category == 'success' else 'danger' if category == 'error' else 'info' }} alert-dismissible fade show">
            {{ message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        {% endfor %}
      {% endif %}
    {% endwith %}

    <ul class="nav nav-tabs mb-4" id="panelTabs">
      <li class="nav-item">
        <a class="nav-link {{ 'active' if active_tab == 'projects' }}" href="{{ url_for('panel', tab='projects') }}">📁 Projelerim</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ 'active' if active_tab == 'upload' }}" href="{{ url_for('panel', tab='upload') }}">📤 Dosya Yükle</a>
      </li>
    </ul>

    <div class="tab-content">
      <!-- Projects Tab -->
      {% if active_tab == 'projects' %}
      <div class="tab-pane fade show active">
        {% if projects %}
          <div class="row">
            {% for project in projects %}
            <div class="col-md-6 mb-4">
              <div class="card project-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title">📁 {{ project.name }}</h5>
                    <span class="badge {{ 'bg-success' if project.is_running else 'bg-secondary' }}">
                      {{ '🟢 Çalışıyor' if project.is_running else '⚪ Durduruldu' }}
                    </span>
                  </div>
                  
                  <p class="card-text text-muted">
                    <small>Oluşturulma: {{ project.created }}</small><br>
                    <small>Python Dosyaları: {{ project.file_count }}</small>
                  </p>
                  
                  <div class="mb-3">
                    <strong>Dosyalar:</strong>
                    {% for file in project.python_files %}
                      <span class="badge bg-dark me-1">🐍 {{ file }}</span>
                    {% endfor %}
                  </div>

                  <div class="btn-group w-100 mb-3">
                    {% if project.is_running %}
                      <a href="/stop_project/{{ project.name }}" class="btn btn-danger btn-sm">⏹️ Durdur</a>
                    {% else %}
                      <a href="/run_project/{{ project.name }}" class="btn btn-success btn-sm">🚀 Çalıştır</a>
                    {% endif %}
                    <button class="btn btn-info btn-sm" onclick="showLogs('{{ project.name }}')">📊 Loglar</button>
                    <a href="/delete_project/{{ project.name }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('Projeyi silmek istediğinize emin misiniz?')">🗑️ Sil</a>
                  </div>

                  <!-- Terminal Logları -->
                  <div id="logs-{{ project.name }}" class="terminal mt-3" style="display: none;">
                    <div class="text-center text-muted">Loglar yükleniyor...</div>
                  </div>
                </div>
              </div>
            </div>
            {% endfor %}
          </div>
        {% else %}
          <div class="text-center py-5">
            <h4>Henüz projeniz bulunmuyor</h4>
            <p class="text-muted">İlk Python dosyalarınızı yükleyerek başlayın!</p>
            <a href="{{ url_for('panel', tab='upload') }}" class="btn btn-primary">📤 Dosya Yükle</a>
          </div>
        {% endif %}
      </div>
      {% endif %}

      <!-- Upload Tab -->
      {% if active_tab == 'upload' %}
      <div class="tab-pane fade show active">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">🐍 Python Dosyası Yükle</h5>
              </div>
              <div class="card-body">
                <form action="/upload_python" method="post" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label class="form-label">Proje Adı</label>
                    <input type="text" name="project_name" class="form-control" placeholder="Yeni proje adı" required>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Python Dosyaları</label>
                    <input type="file" name="python_files" class="form-control" multiple accept=".py" required>
                    <div class="form-text">Birden fazla .py dosyası seçebilirsiniz</div>
                  </div>

                  <button type="submit" class="btn btn-primary w-100">
                    📤 Dosyaları Yükle ve Proje Oluştur
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      {% endif %}
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    async function showLogs(projectName) {
      const logElement = document.getElementById(`logs-${projectName}`);
      
      if (logElement.style.display === 'none') {
        logElement.style.display = 'block';
        
        // Logları getir
        try {
          const response = await fetch(`/get_logs/${projectName}`);
          const data = await response.json();
          
          if (data.logs.length > 0) {
            logElement.innerHTML = data.logs.map(log => 
              `<div class="log-entry">
                 <span class="log-time">[${log.time}]</span> ${log.message}
               </div>`
            ).join('');
            logElement.scrollTop = logElement.scrollHeight;
          } else {
            logElement.innerHTML = '<div class="text-center text-muted">Henüz log bulunmuyor</div>';
          }
        } catch (error) {
          logElement.innerHTML = '<div class="text-center text-danger">Loglar yüklenirken hata oluştu</div>';
        }
      } else {
        logElement.style.display = 'none';
      }
    }

    // Her 5 saniyede bir çalışan projelerin loglarını güncelle
    setInterval(() => {
      document.querySelectorAll('.terminal[style*="display: block"]').forEach(terminal => {
        const projectName = terminal.id.replace('logs-', '');
        showLogs(projectName);
      });
    }, 5000);

    // Dosya seçimi bildirimi
    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
      const files = Array.from(e.target.files);
      const label = this.previousElementSibling;
      
      if (files.length > 0) {
        const fileNames = files.map(f => f.name).join(', ');
        label.textContent = `Seçilen ${files.length} dosya: ${fileNames}`;
        label.style.color = 'var(--primary)';
      }
    });
  </script>
</body>
</html>

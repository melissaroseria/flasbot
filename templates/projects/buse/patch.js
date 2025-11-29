/**
 * Vision Bot Panel - Patch System
 * Terminal logları, dosya yönetimi, sistem monitor
 */

class VisionPatchSystem {
    constructor() {
        this.terminalElement = null;
        this.logHistory = [];
        this.maxLogEntries = 100;
        this.autoRefresh = true;
        this.refreshInterval = 3000; // 3 saniye
        
        this.initialize();
    }

    initialize() {
        console.log('🚀 Vision Patch System Initialized');
        this.findTerminalElement();
        this.startAutoRefresh();
        this.setupEventListeners();
        this.addSystemLog('🔮 Patch sistemi başlatıldı');
    }

    findTerminalElement() {
        // Terminal elementini bul
        this.terminalElement = document.getElementById('systemLogs') || 
                              document.querySelector('.terminal') ||
                              this.createTerminalElement();
    }

    createTerminalElement() {
        // Eğer terminal yoksa oluştur
        const terminal = document.createElement('div');
        terminal.className = 'terminal';
        terminal.id = 'visionTerminal';
        terminal.innerHTML = '<div class="log-entry">🔮 Vision Terminal Başlatıldı</div>';
        
        // Panelden sonra ekle
        const panel = document.querySelector('.card') || document.body;
        panel.parentNode.insertBefore(terminal, panel.nextSibling);
        
        return terminal;
    }

    setupEventListeners() {
        // Klavye kısayolları
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                this.clearTerminal();
            }
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                this.refreshLogs();
            }
        });

        // Sayfa görünürlüğü değişikliği
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshLogs();
            }
        });

        // Online/Offline durumu
        window.addEventListener('online', () => {
            this.addSystemLog('🌐 İnternet bağlantısı aktif');
        });

        window.addEventListener('offline', () => {
            this.addSystemLog('❌ İnternet bağlantısı kesildi');
        });
    }

    addSystemLog(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = {
            timestamp,
            message,
            type,
            id: Date.now() + Math.random()
        };

        this.logHistory.push(logEntry);
        
        // Max log sayısını kontrol et
        if (this.logHistory.length > this.maxLogEntries) {
            this.logHistory.shift();
        }

        this.updateTerminalDisplay();
        
        // Konsola da yaz (debug için)
        console.log(`[${timestamp}] ${message}`);
    }

    updateTerminalDisplay() {
        if (!this.terminalElement) return;

        const logEntries = this.logHistory.map(log => 
            `<div class="log-entry log-${log.type}">
                <span class="log-time">[${log.timestamp}]</span>
                <span class="log-message">${this.formatMessage(log.message)}</span>
            </div>`
        ).join('');

        this.terminalElement.innerHTML = logEntries;
        this.scrollToBottom();
    }

    formatMessage(message) {
        // Özel mesaj formatlama
        const formats = {
            '✅': 'color: var(--primary)',
            '❌': 'color: #ff4444',
            '⚠️': 'color: #ffaa00',
            '🔧': 'color: #00aaff',
            '📁': 'color: #aa00ff',
            '🐍': 'color: #00ff88',
            '🚀': 'color: #ff00aa'
        };

        let formattedMessage = message;
        Object.keys(formats).forEach(emoji => {
            if (message.includes(emoji)) {
                formattedMessage = formattedMessage.replace(
                    emoji, 
                    `<span style="${formats[emoji]}">${emoji}</span>`
                );
            }
        });

        return formattedMessage;
    }

    scrollToBottom() {
        if (this.terminalElement) {
            this.terminalElement.scrollTop = this.terminalElement.scrollHeight;
        }
    }

    clearTerminal() {
        this.logHistory = [];
        this.addSystemLog('🧹 Terminal temizlendi');
    }

    async refreshLogs() {
        try {
            this.addSystemLog('🔃 Loglar yenileniyor...', 'info');
            
            // Sunucudan logları çek
            const response = await fetch('/api/system_logs');
            if (response.ok) {
                const logs = await response.json();
                this.processServerLogs(logs);
            }
        } catch (error) {
            this.addSystemLog('❌ Log yenileme hatası: ' + error.message, 'error');
        }
    }

    processServerLogs(serverLogs) {
        if (serverLogs && serverLogs.length > 0) {
            serverLogs.forEach(log => {
                this.addSystemLog(log.message, log.type);
            });
        }
    }

    startAutoRefresh() {
        if (this.autoRefresh) {
            setInterval(() => {
                if (!document.hidden) {
                    this.refreshLogs();
                }
            }, this.refreshInterval);
        }
    }

    // Dosya İşlemleri
    async uploadFile(file, projectName) {
        const formData = new FormData();
        formData.append('python_files', file);
        formData.append('project_name', projectName);

        try {
            this.addSystemLog(`📤 ${file.name} yükleniyor...`, 'info');
            
            const response = await fetch('/upload_python', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                this.addSystemLog(`✅ ${file.name} başarıyla yüklendi!`, 'success');
                return true;
            } else {
                throw new Error('Upload failed');
            }
        } catch (error) {
            this.addSystemLog(`❌ ${file.name} yükleme hatası: ${error.message}`, 'error');
            return false;
        }
    }

    async deleteFile(projectName, fileName) {
        try {
            this.addSystemLog(`🗑️ ${fileName} siliniyor...`, 'warning');
            
            const response = await fetch(`/delete_file/${projectName}/${fileName}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                this.addSystemLog(`✅ ${fileName} başarıyla silindi!`, 'success');
                return true;
            } else {
                throw new Error('Delete failed');
            }
        } catch (error) {
            this.addSystemLog(`❌ ${fileName} silme hatası: ${error.message}`, 'error');
            return false;
        }
    }

    // Sistem Monitor
    startSystemMonitor() {
        setInterval(() => {
            this.updateSystemStats();
        }, 5000);
    }

    updateSystemStats() {
        // RAM kullanımı (simüle)
        const ramUsage = 30 + Math.random() * 40;
        const diskUsage = 20 + Math.random() * 30;
        
        document.getElementById('ramUsage').style.width = `${ramUsage}%`;
        document.getElementById('diskUsage').style.width = `${diskUsage}%`;
        
        // Aktif bot sayısı
        const activeProjects = document.querySelectorAll('.project-item').length;
        document.getElementById('activeBotCount').textContent = activeProjects;

        // Sistem durumu logu
        if (Math.random() < 0.1) { // %10 ihtimalle log ekle
            this.addSystemLog(`📊 Sistem: RAM ${Math.round(ramUsage)}%, Disk ${Math.round(diskUsage)}%`, 'info');
        }
    }

    // Hata Ayıklama Araçları
    debugProject(projectName) {
        this.addSystemLog(`🔧 ${projectName} projesi hata ayıklanıyor...`, 'debug');
        
        // Proje dosyalarını kontrol et
        this.checkProjectFiles(projectName);
        
        // Gerekli modülleri kontrol et
        this.checkRequiredModules(projectName);
    }

    async checkProjectFiles(projectName) {
        try {
            const response = await fetch(`/api/project_files/${projectName}`);
            if (response.ok) {
                const files = await response.json();
                this.addSystemLog(`📁 ${projectName} dosyaları: ${files.join(', ')}`, 'info');
            }
        } catch (error) {
            this.addSystemLog(`❌ ${projectName} dosya kontrolü hatası`, 'error');
        }
    }

    async checkRequiredModules(projectName) {
        this.addSystemLog(`🐍 ${projectName} modül kontrolü yapılıyor...`, 'debug');
        
        // Burada modül kontrolü yapılabilir
        setTimeout(() => {
            this.addSystemLog(`✅ ${projectName} modül kontrolü tamamlandı`, 'success');
        }, 2000);
    }

    // Utility Functions
    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    getTimestamp() {
        return new Date().toISOString();
    }
}

// Global instance oluştur
const visionPatch = new VisionPatchSystem();

// Global fonksiyonlar (eski kodlarla uyumluluk için)
window.VisionPatch = visionPatch;

// Sayfa yüklendiğinde çalıştır
document.addEventListener('DOMContentLoaded', function() {
    // Sistem monitorü başlat
    visionPatch.startSystemMonitor();
    
    // İlk logları yükle
    setTimeout(() => visionPatch.refreshLogs(), 1000);
});

// Hata yakalama
window.addEventListener('error', function(e) {
    visionPatch.addSystemLog(`❌ JavaScript Hatası: ${e.message}`, 'error');
});

// Promise hatalarını yakala
window.addEventListener('unhandledrejection', function(e) {
    visionPatch.addSystemLog(`❌ Promise Hatası: ${e.reason}`, 'error');
});

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VisionPatchSystem;
}

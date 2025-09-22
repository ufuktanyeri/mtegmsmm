#!/usr/bin/env python3
"""
SMM Documentation Dashboard
Simple web-based dashboard for monitoring SMM documentation status
"""

import json
import os
import subprocess
from datetime import datetime
from pathlib import Path

def get_doc_status():
    """Get current documentation status"""
    docs_dir = Path("_dev/docs")
    
    status = {
        "timestamp": datetime.now().isoformat(),
        "files": {},
        "alerts": [],
        "summary": {
            "total_files": 0,
            "missing_files": 0,
            "stale_files": 0,
            "total_tasks": 0,
            "completed_tasks": 0
        }
    }
    
    # Required documentation files
    required_files = [
        "smm-mvc-modernization-plan.md",
        "smm-complete-implementation-plan.md"
    ]
    
    for filename in required_files:
        file_path = docs_dir / filename
        file_status = {
            "exists": file_path.exists(),
            "path": str(file_path),
            "last_modified": None,
            "tasks": {"total": 0, "completed": 0}
        }
        
        if file_path.exists():
            # Get file modification time
            stat = file_path.stat()
            file_status["last_modified"] = datetime.fromtimestamp(stat.st_mtime).isoformat()
            
            # Count tasks
            content = file_path.read_text(encoding='utf-8')
            total_tasks = content.count("- [")
            completed_tasks = content.count("- [x]")
            
            file_status["tasks"] = {
                "total": total_tasks,
                "completed": completed_tasks,
                "completion_rate": (completed_tasks / total_tasks * 100) if total_tasks > 0 else 0
            }
            
            status["summary"]["total_tasks"] += total_tasks
            status["summary"]["completed_tasks"] += completed_tasks
        else:
            status["summary"]["missing_files"] += 1
            
        status["files"][filename] = file_status
        status["summary"]["total_files"] += 1
    
    # Load alerts if available
    alerts_file = docs_dir / ".alerts.json"
    if alerts_file.exists():
        try:
            status["alerts"] = json.loads(alerts_file.read_text())
        except:
            status["alerts"] = []
    
    return status

def generate_html_dashboard(status):
    """Generate HTML dashboard"""
    html = f"""
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMM Documentation Dashboard</title>
    <style>
        body {{ font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }}
        .container {{ max-width: 1200px; margin: 0 auto; }}
        .header {{ background: #2c3e50; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }}
        .stats {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }}
        .stat-card {{ background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }}
        .stat-number {{ font-size: 2em; font-weight: bold; color: #3498db; }}
        .files {{ background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }}
        .file-item {{ border-bottom: 1px solid #eee; padding: 15px 0; }}
        .file-name {{ font-weight: bold; font-size: 1.1em; }}
        .file-status {{ margin: 5px 0; }}
        .status-ok {{ color: #27ae60; }}
        .status-warning {{ color: #f39c12; }}
        .status-error {{ color: #e74c3c; }}
        .progress-bar {{ width: 100%; height: 20px; background: #ecf0f1; border-radius: 10px; overflow: hidden; }}
        .progress-fill {{ height: 100%; background: #3498db; transition: width 0.3s; }}
        .alerts {{ background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }}
        .alert-item {{ padding: 10px; margin: 10px 0; border-left: 4px solid #e74c3c; background: #fadbd8; }}
        .timestamp {{ color: #7f8c8d; font-size: 0.9em; }}
        .refresh-btn {{ background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }}
        .refresh-btn:hover {{ background: #2980b9; }}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 SMM Documentation Dashboard</h1>
            <p>MTEGM Sosyal Medya Yönetimi Portal - Dokümantasyon Takip Sistemi</p>
            <button class="refresh-btn" onclick="location.reload()">🔄 Yenile</button>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{status['summary']['total_files']}</div>
                <div>Toplam Dosya</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{status['summary']['missing_files']}</div>
                <div>Eksik Dosya</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{status['summary']['total_tasks']}</div>
                <div>Toplam Görev</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{status['summary']['completed_tasks']}</div>
                <div>Tamamlanan Görev</div>
            </div>
        </div>
        
        <div class="files">
            <h2>📄 Dokümantasyon Dosyaları</h2>"""
            
    for filename, file_info in status['files'].items():
        status_class = "status-ok" if file_info['exists'] else "status-error"
        status_text = "✅ Mevcut" if file_info['exists'] else "❌ Bulunamadı"
        
        completion_rate = file_info['tasks']['completion_rate'] if file_info['exists'] else 0
        
        html += f"""
            <div class="file-item">
                <div class="file-name">{filename}</div>
                <div class="file-status {status_class}">{status_text}</div>"""
                
        if file_info['exists']:
            html += f"""
                <div class="timestamp">Son güncelleme: {file_info['last_modified'][:19]}</div>
                <div style="margin: 10px 0;">
                    <div>Görev İlerlemesi: {file_info['tasks']['completed']}/{file_info['tasks']['total']} (%{completion_rate:.1f})</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {completion_rate}%"></div>
                    </div>
                </div>"""
                
        html += "</div>"
    
    html += """
        </div>
        
        <div class="alerts">
            <h2>🚨 Uyarılar</h2>"""
            
    if status['alerts']:
        for alert in status['alerts'][-10:]:  # Show last 10 alerts
            html += f"""
            <div class="alert-item">
                <strong>{alert['type']}</strong>: {alert['message']}
                <br><small>Dosya: {alert['file']}</small>
                <br><small class="timestamp">{alert['timestamp']}</small>
            </div>"""
    else:
        html += "<p>✅ Aktif uyarı bulunmuyor.</p>"
    
    html += f"""
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: #7f8c8d;">
            <small>Son güncelleme: {status['timestamp'][:19]} | SMM Documentation Tracking System v1.0</small>
        </div>
    </div>
</body>
</html>"""
    
    return html

def main():
    """Main function"""
    print("SMM Documentation Dashboard Generator")
    print("=" * 40)
    
    # Get current status
    status = get_doc_status()
    
    # Generate HTML dashboard
    html = generate_html_dashboard(status)
    
    # Save dashboard
    output_file = Path("_dev/docs/dashboard.html")
    output_file.write_text(html, encoding='utf-8')
    
    print(f"✅ Dashboard generated: {output_file}")
    print(f"📊 Total files: {status['summary']['total_files']}")
    print(f"⚠️  Alerts: {len(status['alerts'])}")
    print(f"📈 Overall progress: {status['summary']['completed_tasks']}/{status['summary']['total_tasks']} tasks")

if __name__ == "__main__":
    main()
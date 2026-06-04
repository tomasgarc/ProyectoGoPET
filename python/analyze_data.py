import os
import sys
import re
import json
import sqlite3
from datetime import datetime

# Enable loading packages installed in the user directory (e.g. via pip without admin privileges)
# since web servers like Apache/XAMPP often run without user profile environment variables (%APPDATA%).
version_dir = f"Python{sys.version_info.major}{sys.version_info.minor}"
user_site_path = os.path.expandvars(fr'%APPDATA%\Python\{version_dir}\site-packages')
if not os.path.exists(user_site_path):
    # Hardcoded fallback path for user 'ytuqu' in case APPDATA env var is missing in the web server process
    user_site_path = fr"C:\Users\ytuqu\AppData\Roaming\Python\{version_dir}\site-packages"

if os.path.exists(user_site_path) and user_site_path not in sys.path:
    sys.path.insert(0, user_site_path)


# Define paths
BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

# Ensure USERPROFILE / HOME environment variables are defined so matplotlib
# can find a home directory to write its cache. XAMPP/Apache doesn't provide these by default.
if 'USERPROFILE' not in os.environ and 'HOME' not in os.environ:
    os.environ['USERPROFILE'] = os.path.join(BASE_DIR, 'storage')
    os.environ['HOME'] = os.path.join(BASE_DIR, 'storage')

ENV_PATH = os.path.join(BASE_DIR, '.env')
OUTPUT_JSON = os.path.join(BASE_DIR, 'storage', 'app', 'analytics.json')
CHARTS_DIR = os.path.join(BASE_DIR, 'public', 'images', 'analytics')

def parse_env(file_path):
    """Simple parser for .env files without external dependencies."""
    env = {}
    if not os.path.exists(file_path):
        return env
    with open(file_path, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#'):
                continue
            match = re.match(r'^([^=]+)=(.*)$', line)
            if match:
                key = match.group(1).strip()
                val = match.group(2).strip()
                # Strip quotes if present
                if val.startswith('"') and val.endswith('"'):
                    val = val[1:-1]
                elif val.startswith("'") and val.endswith("'"):
                    val = val[1:-1]
                env[key] = val
    return env

def get_database_connection(env):
    """Determines and establishes connection to the SQLite database."""
    db_conn = env.get('DB_CONNECTION', 'sqlite')
    db_name = env.get('DB_DATABASE', 'database/database.sqlite')
    
    if db_conn != 'sqlite':
        print(f"Warning: env specifies DB_CONNECTION={db_conn}. Falling back to SQLite for local analysis.")
        
    # Resolve SQLite path
    if os.path.isabs(db_name):
        db_path = db_name
    else:
        db_path = os.path.join(BASE_DIR, db_name)
        
    if not os.path.exists(db_path):
        raise FileNotFoundError(f"Database file not found at: {db_path}")
        
    print(f"Connecting to SQLite database: {db_path}")
    return sqlite3.connect(db_path)

def extract_and_transform(conn):
    """Executes SQL queries to retrieve raw data and calculates aggregates."""
    cursor = conn.cursor()
    today_str = datetime.now().strftime('%Y-%m-%d')
    stats = {}
    
    # 1. Row Counts
    cursor.execute("SELECT COUNT(*) FROM users")
    stats['total_users'] = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM dogs")
    stats['total_dogs'] = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM care_requests")
    stats['total_requests'] = cursor.fetchone()[0]
    
    # 2. Active Care Requests
    cursor.execute("SELECT COUNT(*) FROM care_requests WHERE status IN ('pending', 'accepted') AND end_date >= ?", (today_str,))
    stats['active_requests'] = cursor.fetchone()[0]
    
    # 3. Reviews and Ratings
    cursor.execute("SELECT AVG(rating), COUNT(*) FROM reviews")
    avg_rating, reviews_count = cursor.fetchone()
    stats['average_rating'] = round(avg_rating, 2) if avg_rating is not None else 0.0
    stats['total_reviews'] = reviews_count
    
    # 4. Financial Metrics (Payments)
    # Total volume (released + escrow)
    cursor.execute("SELECT SUM(amount) FROM payments WHERE status IN ('released', 'escrow')")
    val = cursor.fetchone()[0]
    stats['total_volume'] = round(val, 2) if val is not None else 0.0
    
    # Platform commissions (total fees from released + escrow)
    cursor.execute("SELECT SUM(fee) FROM payments WHERE status IN ('released', 'escrow')")
    val = cursor.fetchone()[0]
    stats['platform_fees'] = round(val, 2) if val is not None else 0.0
    
    # Payment status breakdowns
    cursor.execute("SELECT status, SUM(amount) FROM payments GROUP BY status")
    payment_breakdown = {status: round(amt, 2) for status, amt in cursor.fetchall()}
    stats['escrow_amount'] = payment_breakdown.get('escrow', 0.0)
    stats['released_amount'] = payment_breakdown.get('released', 0.0)
    stats['refunded_amount'] = payment_breakdown.get('refunded', 0.0)
    
    # 5. Dog Sizes Distribution (Case-insensitive normalization)
    cursor.execute("SELECT size, COUNT(*) FROM dogs GROUP BY size")
    raw_sizes = cursor.fetchall()
    dog_sizes = {}
    for sz, count in raw_sizes:
        if sz:
            norm_sz = sz.strip().lower()
            dog_sizes[norm_sz] = dog_sizes.get(norm_sz, 0) + count
    stats['dog_sizes'] = dog_sizes

    
    # 6. Request Status Distribution
    cursor.execute("SELECT status, COUNT(*) FROM care_requests GROUP BY status")
    stats['request_statuses'] = dict(cursor.fetchall())
    
    # 7. Popular Breeds (Top 5)
    cursor.execute("SELECT breed, COUNT(*) as cnt FROM dogs GROUP BY breed ORDER BY cnt DESC LIMIT 5")
    stats['popular_breeds'] = dict(cursor.fetchall())
    
    # 8. Timeline (Care requests per month)
    cursor.execute("SELECT strftime('%Y-%m', start_date) as month, COUNT(*) FROM care_requests GROUP BY month ORDER BY month")
    stats['requests_timeline'] = dict(cursor.fetchall())
    
    # 9. Additional Metrics: Average Care Request Price
    cursor.execute("SELECT AVG(price) FROM care_requests")
    val = cursor.fetchone()[0]
    stats['average_price'] = round(val, 2) if val is not None else 0.0
    
    return stats

def generate_charts(stats):
    """Generates charts using matplotlib if available, otherwise skips silently."""
    try:
        import matplotlib
        matplotlib.use('Agg') # Set non-interactive backend
        import matplotlib.pyplot as plt
        import pandas as pd
        print("Matplotlib and Pandas successfully imported. Generating visual charts...")
    except ImportError:
        print("Matplotlib or Pandas not installed. Skipping chart image generation.")
        return False
        
    # Ensure charts output directory exists
    os.makedirs(CHARTS_DIR, exist_ok=True)
    
    # Color palette
    brand_colors = ['#4f46e5', '#818cf8', '#c7d2fe', '#fb7185', '#fda4af'] # Indigo/Rose pastel hues
    
    # 1. Chart: Dog Sizes Distribution (Pie Chart)
    sizes_data = stats.get('dog_sizes', {})
    if sizes_data:
        plt.figure(figsize=(6, 6))
        # Capitalize keys for labels
        labels = [k.capitalize() for k in sizes_data.keys()]
        values = list(sizes_data.values())
        
        plt.pie(values, labels=labels, autopct='%1.1f%%', startangle=140, colors=brand_colors[:len(values)],
                textprops={'fontsize': 12, 'weight': 'bold', 'color': '#1e1b4b'},
                wedgeprops={'edgecolor': 'white', 'linewidth': 2})
        plt.title('Distribución de Tamaños de Perros', fontsize=14, fontweight='black', color='#1e1b4b', pad=20)
        plt.tight_layout()
        plt.savefig(os.path.join(CHARTS_DIR, 'dog_sizes.png'), dpi=150, transparent=True)
        plt.close()
        
    # 2. Chart: Care Request Status Count (Bar Chart)
    status_data = stats.get('request_statuses', {})
    if status_data:
        plt.figure(figsize=(7, 4.5))
        # Map labels to Spanish for visual clarity
        label_mapping = {'pending': 'Pendiente', 'accepted': 'En Curso', 'finalized': 'Finalizada'}
        labels = [label_mapping.get(k, k.capitalize()) for k in status_data.keys()]
        values = list(status_data.values())
        
        bars = plt.bar(labels, values, color='#818cf8', width=0.5, edgecolor='#4f46e5', linewidth=1.5)
        plt.title('Peticiones de Cuidado por Estado', fontsize=14, fontweight='black', color='#1e1b4b', pad=15)
        plt.ylabel('Cantidad', fontsize=11, fontweight='bold', color='#4f5e74')
        plt.grid(axis='y', linestyle='--', alpha=0.5)
        
        # Add values on top of bars
        for bar in bars:
            height = bar.get_height()
            plt.text(bar.get_x() + bar.get_width()/2.0, height + 0.1, f'{int(height)}',
                     ha='center', va='bottom', fontsize=10, fontweight='bold', color='#1e1b4b')
                     
        # Clean up borders
        for spine in plt.gca().spines.values():
            spine.set_visible(False)
        plt.gca().spines['bottom'].set_visible(True)
        plt.gca().spines['bottom'].set_color('#cbd5e1')
        
        plt.tight_layout()
        plt.savefig(os.path.join(CHARTS_DIR, 'request_statuses.png'), dpi=150, transparent=True)
        plt.close()

    # 3. Chart: Platform Financial Stats (Bar Chart of Volume)
    finance_data = {
        'En Depósito (Escrow)': stats.get('escrow_amount', 0.0),
        'Liberado': stats.get('released_amount', 0.0),
        'Devuelto': stats.get('refunded_amount', 0.0)
    }
    
    plt.figure(figsize=(7, 4.5))
    labels = list(finance_data.keys())
    values = list(finance_data.values())
    
    colors = ['#fda4af', '#818cf8', '#cbd5e1'] # Escrow (Rose), Released (Indigo), Refunded (Gray)
    edge_colors = ['#fb7185', '#4f46e5', '#94a3b8']
    
    bars = plt.bar(labels, values, color=colors, width=0.5, edgecolor=edge_colors, linewidth=1.5)
    plt.title('Análisis de Transacciones Financieras (€)', fontsize=14, fontweight='black', color='#1e1b4b', pad=15)
    plt.ylabel('Importe total (€)', fontsize=11, fontweight='bold', color='#4f5e74')
    plt.grid(axis='y', linestyle='--', alpha=0.5)
    
    # Add values on top of bars
    for bar in bars:
        height = bar.get_height()
        plt.text(bar.get_x() + bar.get_width()/2.0, height + (max(values)*0.02 if max(values) > 0 else 0.1), f'{height:.2f}€',
                 ha='center', va='bottom', fontsize=10, fontweight='bold', color='#1e1b4b')
                 
    # Clean up borders
    for spine in plt.gca().spines.values():
        spine.set_visible(False)
    plt.gca().spines['bottom'].set_visible(True)
    plt.gca().spines['bottom'].set_color('#cbd5e1')
    
    plt.tight_layout()
    plt.savefig(os.path.join(CHARTS_DIR, 'revenue_stats.png'), dpi=150, transparent=True)
    plt.close()
    
    print("Charts generated successfully and saved in public/images/analytics/.")
    return True

def main():
    print("=== Pipeline de Análisis de Datos de GoPET (Python ETL) ===")
    try:
        env = parse_env(ENV_PATH)
        conn = get_database_connection(env)
        
        # ETL processing
        print("Extracting and transforming database records...")
        stats = extract_and_transform(conn)
        conn.close()
        
        # Save JSON output
        os.makedirs(os.path.dirname(OUTPUT_JSON), exist_ok=True)
        with open(OUTPUT_JSON, 'w', encoding='utf-8') as f:
            json.dump(stats, f, indent=4, ensure_ascii=False)
        print(f"Numerical analysis results saved to: {OUTPUT_JSON}")
        
        # Visual charts generation
        charts_result = generate_charts(stats)
        stats['charts_generated'] = charts_result
        
        # Rewrite JSON output with chart status flag
        with open(OUTPUT_JSON, 'w', encoding='utf-8') as f:
            json.dump(stats, f, indent=4, ensure_ascii=False)
            
        print("Pipeline execution completed successfully.")
        
    except Exception as e:
        print(f"Error executing python analytics: {e}")
        import traceback
        traceback.print_exc()
        exit(1)

if __name__ == '__main__':
    main()

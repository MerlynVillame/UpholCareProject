<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login System - UphoCare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 1000px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .test-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 2px solid #e3e6f0;
            transition: all 0.3s;
        }
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .test-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .test-card .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }
        .credentials {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .credentials strong {
            color: #667eea;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-info {
            background: #17a2b8;
        }
        .flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .flow-item {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #667eea;
            min-width: 150px;
            margin: 5px;
        }
        .flow-arrow {
            font-size: 2rem;
            color: #667eea;
            margin: 0 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 5px;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        .status-warning {
            background: #fff3cd;
            color: #856404;
        }
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        @media (max-width: 768px) {
            .flow {
                flex-direction: column;
            }
            .flow-arrow {
                transform: rotate(90deg);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 UphoCare Login System Test</h1>
        
        <div class="section">
            <h2>📊 Login Flow</h2>
            <div class="flow">
                <div class="flow-item">
                    <strong>Enter Credentials</strong>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-item">
                    <strong>Validate</strong>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-item">
                    <strong>Check Role</strong>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-item">
                    <strong>Redirect</strong>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>🧪 Database Setup Required</h2>
            
            <div class="test-card">
                <h3><span class="icon">📊</span> Create Database First</h3>
                <p>Your database is empty. You need to create the database structure first.</p>
                <div class="credentials">
                    <strong>Database:</strong> db_upholcare<br>
                    <strong>Status:</strong> Empty (no users exist)
                </div>
                <span class="status status-warning">Action Required: Create Database</span>
                <br><br>
                <a href="http://localhost/phpmyadmin" class="btn" target="_blank">📊 Open phpMyAdmin</a>
            </div>

            <div class="test-card">
                <h3><span class="icon">🔧</span> Setup Database</h3>
                <p>Run the SQL file to create tables and structure.</p>
                <div class="credentials">
                    <strong>File:</strong> database/setup_empty_database.sql<br>
                    <strong>Result:</strong> Creates tables (no test users)
                </div>
                <span class="status status-info">Step 1: Run SQL File</span>
                <br><br>
                <a href="database/setup_empty_database.sql" class="btn btn-success" target="_blank">📁 View SQL File</a>
            </div>

            <div class="test-card">
                <h3><span class="icon">👤</span> Register New User</h3>
                <p>After creating database, register a new user account.</p>
                <div class="credentials">
                    <strong>Action:</strong> Use Register Page<br>
                    <strong>Result:</strong> Create your own account
                </div>
                <span class="status status-success">Step 2: Register Account</span>
                <br><br>
                <a href="auth/register" class="btn">📝 Go to Register</a>
            </div>
        </div>

        <div class="section">
            <h2>🔧 Quick Actions</h2>
            <a href="auth/login" class="btn">🔑 Login Page</a>
            <a href="auth/register" class="btn btn-success">📝 Register Page</a>
            <a href="check_session.php" class="btn btn-info">🔍 Check Session</a>
            <a href="customer/dashboard" class="btn">📊 Customer Dashboard</a>
            <a href="admin/dashboard" class="btn btn-secondary">⚙️ Admin Dashboard</a>
        </div>

        <div class="section">
            <h2>📝 Setup Instructions</h2>
            <ol style="line-height: 2;">
                <li>Open phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
                <li>Click "SQL" tab</li>
                <li>Run SQL from: <code>database/setup_empty_database.sql</code></li>
                <li>Verify 6 tables are created</li>
                <li>Go to Register page to create your first user</li>
                <li>Login with your new account</li>
            </ol>
        </div>

        <div class="section">
            <h2>✅ What to Expect</h2>
            <div style="line-height: 2;">
                <p><strong>✅ After Database Setup:</strong> 6 tables created (users, services, bookings, etc.)</p>
                <p><strong>✅ After Registration:</strong> New user account created</p>
                <p><strong>✅ After Login:</strong> Redirects to appropriate dashboard</p>
                <p><strong>❌ Before Setup:</strong> 403 Unauthorized (no users exist)</p>
                <p><strong>⛔ Wrong Credentials:</strong> Shows error message</p>
            </div>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Sistem DesiTeks — Inventory & POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/desiteks.css') }}">
    <style>
        body {
            background-color: var(--dt-navy-dark);
            color: var(--dt-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .portal-container {
            max-width: 900px;
            width: 100%;
            background: var(--dt-navy);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        .portal-brand-panel {
            background: linear-gradient(135deg, var(--dt-navy-dark) 0%, var(--dt-navy) 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .portal-login-panel {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .portal-logo-icon {
            font-size: 3rem;
            color: var(--dt-gold);
            margin-bottom: 24px;
        }
        .system-status {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--dt-gold);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #2ecc71;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #2ecc71;
        }
    </style>
</head>
<body>

<div class="portal-container">
    <div class="row g-0">
        {{-- Sisi Kiri: Branding Sistem --}}
        <div class="col-md-6 portal-brand-panel">
            <div>
                <div class="system-status mb-3">
                    <span class="status-dot"></span>
                    Sistem Online
                </div>
                <h1 style="font-size: 32px; font-weight: 700; color: var(--dt-white); letter-spacing: -0.5px;">DesiTeks</h1>
                <p style="color: rgba(255,255,255,0.6); font-size: 14px; line-height: 1.6; margin-top: 12px;">
                    Sistem Terintegrasi Manajemen Inventaris Kain, Pembelian Gudang, dan Point of Sales (POS) Penjualan Toko.
                </p>
            </div>
            
            <div style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px;">
                <div style="font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px;">DesiTeks Enterprise v1.0</div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 4px;">&copy; {{ date('Y') }} Hak Cipta Dilindungi.</div>
            </div>
        </div>

        {{-- Sisi Kanan: Akses Masuk --}}
        <div class="col-md-6 portal-login-panel">
            <div class="portal-logo-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 style="font-size: 20px; font-weight: 600; color: var(--dt-white);">Akses Portal Sistem</h2>
            <p style="color: rgba(255,255,255,0.5); font-size: 13px; margin-top: 4px; margin-bottom: 24px;">
                Silakan masuk menggunakan akun kredensial Anda yang terdaftar pada sistem.
            </p>

            @auth
                <div class="d-grid gap-2">
                    <a href="{{ url('/dashboard') }}" class="dt-btn dt-btn-gold justify-content-center py-2" style="font-size: 14px;">
                        Masuk ke Dashboard <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="dt-btn dt-btn-gold justify-content-center py-2" style="font-size: 14px;">
                        Masuk Sistem <i class="bi bi-box-arrow-in-right ms-2"></i>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

</body>
</html>

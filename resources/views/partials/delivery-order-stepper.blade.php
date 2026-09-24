{{-- Component Stepper Tracking Pergerakan Surat Jalan (Clean & Minimal) --}}
<style>
    .stepper-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .stepper-header-sm {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Track line */
    .stepper-track-wrap {
        position: relative;
        display: flex;
        justify-content: space-between;
        margin: 6px 0 2px 0;
    }
    .stepper-track-line {
        position: absolute;
        top: 17px;
        left: 12.5%;
        right: 12.5%;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
        overflow: hidden;
    }
    .stepper-track-fill {
        height: 100%;
        background: #2563eb;
        z-index: 2;
        transition: width 0.3s ease;
    }

    /* Step Item */
    .st-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        text-align: center;
    }
    .st-dot {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 0 0 3px #ffffff;
    }
    .st-title {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-top: 8px;
        line-height: 1.3;
    }
    .st-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* States */
    .st-item.active .st-dot {
        border-color: #2563eb;
        background: #2563eb;
        color: #ffffff;
    }
    .st-item.active .st-title {
        color: #0f172a;
        font-weight: 700;
    }

    .st-item.done .st-dot {
        border-color: #16a34a;
        background: #16a34a;
        color: #ffffff;
    }
    .st-item.done .st-title {
        color: #16a34a;
    }

    .st-item.rejected .st-dot {
        border-color: #dc2626;
        background: #dc2626;
        color: #ffffff;
    }
    .st-item.rejected .st-title {
        color: #dc2626;
    }

    @media (max-width: 767.98px) {
        .stepper-track-wrap {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            padding-left: 6px;
        }
        .stepper-track-line, .stepper-track-fill {
            display: none;
        }
        .st-item {
            flex-direction: row;
            text-align: left;
            gap: 12px;
        }
        .st-title {
            margin-top: 0;
        }
    }

    @media print {
        .stepper-card {
            display: none !important;
        }
    }
</style>

@php
    $status = $deliveryOrder->status;

    $s1 = true;
    $s2 = in_array($status, ['disetujui_admin', 'dikirim', 'dalam_perjalanan', 'diterima']);
    $s3 = in_array($status, ['dalam_perjalanan', 'diterima']);
    $s4 = ($status === 'diterima');
    $isReject = ($status === 'ditolak');

    $fill = '0%';
    if ($s4) $fill = '100%';
    elseif ($s3) $fill = '66.66%';
    elseif ($s2) $fill = '33.33%';
@endphp

<div class="stepper-card">
    <div class="stepper-header-sm">
        <i class="bi bi-clock-history"></i>
        Progress Pengiriman
    </div>

    <div class="stepper-track-wrap">
        <div class="stepper-track-line">
            <div class="stepper-track-fill" id="stepper-fill" style="width: {{ $fill }};"></div>
        </div>

        <!-- Step 1 -->
        <div class="st-item {{ $s1 ? 'done' : '' }}" id="step-item-1">
            <div class="st-dot" id="step-dot-1"><i class="bi bi-check-lg"></i></div>
            <div>
                <div class="st-title">1. Pengajuan Surat Jalan</div>
                <div class="st-sub" id="step-sub-1">{{ $deliveryOrder->created_at ? $deliveryOrder->created_at->format('d M Y, H:i') : '-' }}</div>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="st-item {{ $isReject && !$s2 ? 'rejected' : ($s2 ? 'done' : ($status === 'menunggu_approval' ? 'active' : '')) }}" id="step-item-2">
            <div class="st-dot" id="step-dot-2">
                @if($isReject && !$s2)
                    <i class="bi bi-x-lg"></i>
                @elseif($s2)
                    <i class="bi bi-check-lg"></i>
                @else
                    <i class="bi bi-shield"></i>
                @endif
            </div>
            <div>
                <div class="st-title">2. Disetujui Admin</div>
                <div class="st-sub" id="step-sub-2">
                    @if($deliveryOrder->approved_at)
                        {{ $deliveryOrder->approved_at->format('d M Y, H:i') }}
                    @elseif($s2)
                        Disetujui
                    @elseif($status === 'menunggu_approval')
                        Menunggu ACC
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="st-item {{ $isReject && $s2 && !$s3 ? 'rejected' : ($s3 ? 'done' : ($status === 'disetujui_admin' || $status === 'dikirim' ? 'active' : '')) }}" id="step-item-3">
            <div class="st-dot" id="step-dot-3">
                @if($isReject && $s2 && !$s3)
                    <i class="bi bi-x-lg"></i>
                @elseif($s3)
                    <i class="bi bi-check-lg"></i>
                @else
                    <i class="bi bi-truck"></i>
                @endif
            </div>
            <div>
                <div class="st-title">3. Dalam Perjalanan</div>
                <div class="st-sub" id="step-sub-3">
                    @if($deliveryOrder->shipped_at)
                        {{ $deliveryOrder->shipped_at->format('d M Y, H:i') }}
                    @elseif($s3)
                        Dalam Pengiriman
                    @elseif($status === 'disetujui_admin' || $status === 'dikirim')
                        Menunggu Keberangkatan
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="st-item {{ $isReject && $s3 ? 'rejected' : ($s4 ? 'done' : ($status === 'dalam_perjalanan' ? 'active' : '')) }}" id="step-item-4">
            <div class="st-dot" id="step-dot-4">
                @if($isReject && $s3)
                    <i class="bi bi-x-lg"></i>
                @elseif($s4)
                    <i class="bi bi-check-lg"></i>
                @else
                    <i class="bi bi-box-seam"></i>
                @endif
            </div>
            <div>
                <div class="st-title">4. Diterima Gudang</div>
                <div class="st-sub" id="step-sub-4">
                    @if($deliveryOrder->received_at)
                        {{ $deliveryOrder->received_at->format('d M Y, H:i') }}
                    @elseif($status === 'dalam_perjalanan')
                        Menunggu Pembongkaran
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

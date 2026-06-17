@php
    $driver = $permit->driver;
    $driverNameCaps = extension_loaded('mbstring') && function_exists('mb_strtoupper')
        ? mb_strtoupper($driver?->full_name ?? '—')
        : strtoupper($driver?->full_name ?? '—');
    $statusLabel = match ($permit->status) {
        \App\Models\Permit::STATUS_VALID => $permit->expiry_date->lt(now()->startOfDay())
            ? __('EXPIRED')
            : __('VALID'),
        \App\Models\Permit::STATUS_EXPIRED => __('EXPIRED'),
        \App\Models\Permit::STATUS_REVOKED => __('REVOKED'),
        default => __('INVALID'),
    };
    $statusIsValid = $permit->status === \App\Models\Permit::STATUS_VALID && $permit->expiry_date->gte(now()->startOfDay());
    $statusClass = $statusIsValid ? 'st-ok' : 'st-no';

    $__pdfPgStyle = '';
    $__pdfPgPath = resource_path('css/pdf-certificate-page.css');
    if (is_readable($__pdfPgPath)) {
        // Split tag names so fragile IDE parsers do not open a fake stylesheet region here.
        $__pdfPgStyle = '<'.'style>'.file_get_contents($__pdfPgPath).'<'.'/style>';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $certificateTitle }} — {{ $permit->permit_number }}</title>
    {!! $__pdfPgStyle !!}
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            /* Page fill: must match paper — grey was visible below the short certificate on landscape A4 */
            background: #ffffff;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.5px;
            color: #1c1917;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table { border-collapse: collapse; border-spacing: 0; }
        img { border: none; vertical-align: middle; max-width: 100%; }

        .sheet { width: 100%; max-width: 100%; page-break-inside: avoid; table-layout: fixed; }

        /* Double frame — reads as a certificate, not a web table */
        .frame-gold { border: 2px solid #b8983d; background: #fffdf7; width: 100%; }
        .frame-green { border: 1px solid #14532d; margin: 2px; background: #fffdf7; }
        .frame-pad { padding: 6px 8px 8px 8px; }

        /* Compact authority bar */
        .bar {
            background: #14532d;
            color: #fffef5;
            width: 100%;
        }
        .bar td { padding: 5px 7px; vertical-align: middle; }
        .logo-ring {
            width: 44px;
            height: 44px;
            border: 2px solid #cfa855;
            text-align: center;
            overflow: hidden;
            background: rgba(255,255,255,0.06);
            line-height: 40px;
            border-radius: 50%;
        }
        .logo-ring img { max-width: 40px; max-height: 40px; vertical-align: middle; }
        .brand { font-family: DejaVu Serif, Georgia, serif; font-size: 12px; font-weight: bold; letter-spacing: 0.03em; }
        .brand-sub { font-size: 5.9px; color: #d9c894; margin-top: 2px; letter-spacing: 0.04em; line-height: 1.28; }
        .pill {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 6px;
            color: #14532d;
            background: #fefce8;
            border: 1px solid #e7d089;
            padding: 4px 6px;
            display: inline-block;
        }

        .title-box { padding: 6px 0 4px 0; text-align: center; }
        .t-kicker {
            font-size: 6px;
            letter-spacing: 0.2em;
            color: #78716c;
            text-transform: uppercase;
            font-weight: bold;
        }
        .t-main {
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 15px;
            font-weight: bold;
            color: #14532d;
            margin-top: 4px;
        }
        .rule { height: 1px; background: #d9c892; margin: 5px auto; width: 120px; }
        .recipient {
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.035em;
            color: #0c0a09;
            margin-top: 3px;
        }
        .blurb {
            text-align: center;
            margin: 0 auto 6px auto;
            font-size: 7px;
            line-height: 1.42;
            color: #57534e;
            padding: 0 42px;
        }

        /* “Record card” rows — divider lines only (no spreadsheet grid) */
        .rows { width: 100%; border: 1px solid #d6d3d1; background: #fff; }
        .rows td { padding: 3px 6px; vertical-align: middle; border-bottom: 1px solid #e7e5e4; }
        .rows tr:last-child td { border-bottom: none; }
        .rows td.lbl {
            width: 15%;
            font-size: 5.8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #78716c;
            background: #fafaf9;
        }
        .rows td.val { width: 35%; font-size: 8.5px; font-weight: bold; color: #1c1917; }
        .mono { font-family: DejaVu Sans Mono, monospace; font-size: 7.8px; }
        .st-ok { color: #15803d !important; }
        .st-no { color: #c2410c !important; }

        .ft { margin-top: 8px; border-top: 1px solid #d6d3d1; padding-top: 8px; }
        .sig-img { max-height: 34px; max-width: 200px; display: block; margin-top: 2px; }
        .sig-line { border-top: 1px solid #292524; width: 200px; margin-top: 5px; }
        .sig-n { margin-top: 3px; font-size: 8px; font-weight: bold; }
        .sig-r { margin-top: 2px; font-size: 6.5px; color: #57534e; }
        .qr {
            border: 1px solid #d6d3d1;
            padding: 4px;
            background: #fff;
            display: inline-block;
        }
        .qr img { width: 62px !important; height: 62px !important; display: block; }

        .meta {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #e7e5e4;
            font-size: 5.8px;
            color: #78716c;
            line-height: 1.38;
            text-align: center;
        }

        .foot-slogan {
            margin-top: 6px;
            background: #14532d;
            color: #fefce8;
            text-align: center;
            font-size: 6.8px;
            font-weight: bold;
            letter-spacing: 0.12em;
            padding: 5px;
            border-top: 2px solid #b8983d;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<table cellpadding="0" cellspacing="0" width="100%" class="sheet"><tr><td align="center" style="padding:2px;">
    <table cellpadding="0" cellspacing="0" width="100%" class="frame-gold sheet"><tr><td>
        <div class="frame-green">
            <div class="frame-pad">

                <table cellpadding="0" cellspacing="0" class="bar sheet">
                    <tr>
                        <td width="50" valign="middle">
                            <div class="logo-ring">
                                @if(!empty($logoDataUri))
                                    <img src="{{ $logoDataUri }}" alt="">
                                @else
                                    <span style="font-weight:bold;color:#fef9c3;font-size:10px;font-family:'DejaVu Serif',serif;">Z</span>
                                @endif
                            </div>
                        </td>
                        <td valign="middle" style="padding-left:6px;">
                            <div class="brand">{{ __('ZAFFICO') }}</div>
                            <div class="brand-sub">{{ __('Vehicle Operator Certification (VOCS)') }} · {{ __('DPMS') }}</div>
                            <div class="brand-sub" style="margin-top:3px;color:#e7e5e4;">{{ __('Zambia Forestry And Forest Industries Corporation PLC') }}</div>
                        </td>
                        <td width="125" align="right" valign="middle">
                            <span class="pill">{{ $permit->permit_number }}</span>
                        </td>
                    </tr>
                </table>

                <div class="title-box">
                    <div class="t-kicker">{{ __('Official certificate') }}</div>
                    <div class="t-main">{{ $certificateTitle }}</div>
                    <div class="rule"></div>
                    <div class="recipient">{{ $driverNameCaps }}</div>
                </div>

                <p class="blurb">{{ __('This certifies that the named operator is authorized to drive company vehicles in line with ZAFFICO transport and safety policies.') }}</p>

                <table cellpadding="0" cellspacing="0" class="rows sheet" width="100%">
                    <tr>
                        <td class="lbl">{{ __('Employee ID') }}</td>
                        <td class="val mono">{{ $driver?->employee_id ?? '—' }}</td>
                        <td class="lbl">{{ __('NRC') }}</td>
                        <td class="val mono">{{ $driver?->nrc ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">{{ __('Licence number') }}</td>
                        <td class="val">{{ $driver?->license_number ?? '—' }}</td>
                        <td class="lbl">{{ __('Licence class') }}</td>
                        <td class="val">{{ $driver?->license_class ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">{{ __('Department') }}</td>
                        <td class="val">{{ $driver?->department ?? '—' }}</td>
                        <td class="lbl">{{ __('Certificate no.') }}</td>
                        <td class="val mono">{{ $permit->permit_number }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">{{ __('Issue date') }}</td>
                        <td class="val">{{ $permit->issue_date->format('d M Y') }}</td>
                        <td class="lbl">{{ __('Permit expiry') }}</td>
                        <td class="val">{{ $permit->expiry_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">{{ __('Status') }}</td>
                        <td class="val {{ $statusClass }}">{{ $statusLabel }}</td>
                        <td class="lbl">{{ __('Phone') }}</td>
                        <td class="val">{{ $driver?->phone ?? '—' }}</td>
                    </tr>
                </table>

                <table cellpadding="0" cellspacing="0" width="100%" class="ft sheet">
                    <tr valign="top">
                        <td width="43%">
                            @if(!empty($signatureDataUri))
                                <img src="{{ $signatureDataUri }}" alt="" class="sig-img">
                            @endif
                            <div class="sig-line"></div>
                            <div class="sig-n">{{ $permit->issuer?->name ?? '—' }}</div>
                            <div class="sig-r">{{ $issuedByRole }}, {{ __('ZAFFICO PLC') }}</div>
                        </td>
                        <td width="57%" align="right">
                            @if(!empty($qrDataUri))
                                <div class="qr" style="margin-left:auto;"><img src="{{ $qrDataUri }}" alt="" width="62" height="62"></div>
                            @endif
                            <div style="margin-top:5px;font-size:6px;font-weight:bold;color:#14532d;">{{ __('Authenticate') }}</div>
                            <div style="font-size:5.85px;color:#78716c;max-width:260px;line-height:1.25;text-align:right;margin-left:auto;margin-top:2px;">{{ $verificationUrl }}</div>
                        </td>
                    </tr>
                </table>

                <div class="meta">
                    {{ __('Printed :time — ZAFFICO PLC. Valid with authorized signature and QR verification.', ['time' => now()->format('d M Y H:i')]) }}
                </div>

                <div class="foot-slogan">{{ __('Drive safely · Work responsibly · Return home') }}</div>

            </div>
        </div>
    </td></tr></table>
</td></tr></table>

</body>
</html>

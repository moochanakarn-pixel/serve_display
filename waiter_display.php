<?php require_once __DIR__ . '/config.php'; require_once __DIR__ . '/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="#0a3a70">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="เสิร์ฟ">
<title>Serve Display</title>
<link rel="manifest" href="manifest.json">
<link rel="icon" type="image/png" href="favicon.png">
<link rel="apple-touch-icon" href="icon-512.png">
<style>
:root{
    --bg:#edf5ff;--bg-2:#fff7ed;
    --surface:#ffffff;--surface-soft:#f8fbff;
    --text:#122033;--muted:#6b7a90;
    --line:#dbe8f7;--line-strong:#c3d5ea;
    --primary:#1683ff;--primary-dark:#0f69cf;--primary-deep:#0a3a70;
    --secondary:#ff8a1f;--secondary-soft:#fff1e4;
    --success:#12a150;--success-soft:#e6f8ee;
    --warning:#d97706;--warning-soft:#fffbeb;
    --danger:#e44c3a;--danger-soft:#ffe8e4;
    --shadow:0 12px 28px rgba(15,23,42,.10);
    --shadow-soft:0 8px 18px rgba(22,131,255,.08);
    --radius:20px;--radius-sm:12px;
    --grn:var(--success);
}
*{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
html,body{min-height:100%;font-family:Tahoma,Arial,sans-serif;color:var(--text)}
body{
    background:
        radial-gradient(circle at top left,rgba(22,131,255,.12),transparent 28%),
        radial-gradient(circle at top right,rgba(255,138,31,.14),transparent 24%),
        linear-gradient(180deg,var(--bg),var(--bg-2));
}

/* HEADER */
.hdr{
    position:sticky;top:0;z-index:30;
    padding:8px 14px 7px;
    background:linear-gradient(135deg,rgba(8,58,112,.96),rgba(22,131,255,.92),rgba(255,138,31,.88));
    color:#fff;box-shadow:0 8px 20px rgba(8,58,112,.18);
}
.hdr-inner{max-width:1920px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
.hdr-l{display:flex;align-items:center;gap:10px}
.hdr-icon{width:40px;height:40px;background:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;box-shadow:0 4px 10px rgba(0,0,0,.12)}
.hdr-title{font-size:18px;font-weight:700;line-height:1.1}
.hdr-sub{font-size:11px;opacity:.9;font-weight:700;margin-top:2px}
.hdr-r{display:flex;align-items:center;gap:8px}
.live{width:7px;height:7px;background:#4ade80;border-radius:50%;box-shadow:0 0 6px #4ade80;animation:blink 2s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.clock{font-size:13px;font-weight:700;color:rgba(255,255,255,.9)}

/* SUMMARY BAR */
.sticky-bar{
    position:sticky;top:56px;z-index:20;
    background:linear-gradient(180deg,rgba(237,245,255,.98),rgba(237,245,255,.95));
    backdrop-filter:blur(8px);
    border-bottom:1px solid var(--line);
    padding-bottom:2px;
}

/* FILTER BAR */
.fbar{display:flex;gap:6px;padding:6px 12px 4px;overflow-x:auto;scrollbar-width:none;max-width:1920px;margin:0 auto}
.fbar::-webkit-scrollbar{display:none}
.fbtn{
    flex-shrink:0;padding:6px 14px;border-radius:999px;
    border:1.5px solid var(--line-strong);background:#fff;
    color:var(--muted);font-family:Tahoma,Arial,sans-serif;
    font-size:12px;font-weight:700;cursor:pointer;
    display:flex;align-items:center;gap:5px;transition:all .15s;
}
.fbtn .cnt{
    background:var(--line);color:var(--muted);border-radius:999px;
    padding:1px 7px;font-size:10px;font-weight:700;min-width:18px;text-align:center;
}
.fbtn.on{background:var(--primary);border-color:var(--primary);color:#fff}
.fbtn.on .cnt{background:rgba(255,255,255,.25);color:#fff}

/* SEARCH BAR */
.sbar{display:flex;align-items:center;gap:8px;padding:4px 12px 6px;max-width:1920px;margin:0 auto}
.sbar-wrap{position:relative;flex:1;max-width:320px}
.sbar-inp{
    width:100%;padding:7px 30px 7px 32px;border-radius:999px;
    border:1.5px solid var(--line-strong);background:#fff;
    font-size:13px;font-family:Tahoma,Arial,sans-serif;color:var(--text);
    outline:none;transition:border-color .15s;
}
.sbar-inp:focus{border-color:var(--primary)}
.sbar-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;font-size:14px}
.sbar-clr{
    position:absolute;right:9px;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:var(--muted);
    font-size:15px;line-height:1;padding:0;display:none;
}
.sbar-clr.show{display:block}

/* TABLE CHIPS */
.tchips{display:flex;gap:5px;overflow-x:auto;scrollbar-width:none;flex:1}
.tchips::-webkit-scrollbar{display:none}
.tchip{
    flex-shrink:0;padding:5px 12px;border-radius:999px;
    border:1.5px solid var(--line-strong);background:#fff;color:var(--muted);
    font-size:11px;font-weight:700;cursor:pointer;transition:all .15s;
    font-family:Tahoma,Arial,sans-serif;white-space:nowrap;
}
.tchip:active{transform:scale(.95)}
.tchip.t-wait{border-color:#bfeacc;color:var(--success);background:var(--success-soft)}
.tchip.t-part{border-color:#fcd34d;color:var(--warning);background:var(--warning-soft)}
.tchip.t-done{border-color:var(--line);color:var(--muted);opacity:.6}

/* REFRESH BTN */
.rfbtn{
    appearance:none;border:none;border-radius:12px;min-height:34px;padding:0 12px;
    font-size:12px;font-weight:700;cursor:pointer;
    background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.2);
    color:#fff;font-family:Tahoma,Arial,sans-serif;
    display:flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s;
}
.rfbtn:active{background:rgba(255,255,255,.28)}
.rfbtn.spin svg{animation:rot .7s linear infinite}
@keyframes rot{to{transform:rotate(360deg)}}

/* CONTENT */
.content{padding:8px 12px 16px;max-width:1920px;margin:0 auto;display:grid;gap:10px;
  grid-template-columns:1fr;
}
@media(min-width:640px){.content{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1100px){.content{grid-template-columns:repeat(3,1fr)}}
@media(min-width:1600px){.content{grid-template-columns:repeat(4,1fr)}}


/* CARD */
.card{
    background:rgba(255,255,255,.92);border:1.5px solid var(--line);
    border-radius:var(--radius);overflow:hidden;
    box-shadow:var(--shadow);transition:border-color .2s,box-shadow .2s;
}
.card.c-rdy{border-color:#bfeacc;box-shadow:0 0 0 2px rgba(18,161,80,.12),var(--shadow)}
.card.c-part{border-color:#fcd34d;box-shadow:0 0 0 2px rgba(217,119,6,.10),var(--shadow)}
.card.c-done{border-color:var(--line);opacity:.55}

.c-hdr{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 14px;border-bottom:1px solid var(--line);
    background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(245,250,255,.9));
}
.tbl-badge{display:flex;align-items:center;gap:8px}
.tbl-num{
    background:linear-gradient(135deg,var(--primary-deep),var(--primary));
    color:#fff;font-weight:800;font-size:14px;border-radius:8px;padding:4px 10px;
}
.tbl-name{font-weight:700;font-size:15px;color:var(--primary-deep)}
.queue-badge{background:#f0f6ff;border:1px solid var(--line);border-radius:999px;padding:2px 8px;font-size:11px;font-weight:700;color:var(--muted)}

.pill{font-size:10px;font-weight:700;padding:4px 10px;border-radius:999px;display:flex;align-items:center;gap:4px;white-space:nowrap}
.p-rdy{background:var(--success-soft);color:var(--success);border:1px solid #bfeacc}
.p-part{background:var(--warning-soft);color:var(--warning);border:1px solid #fcd34d}
.p-done{background:var(--line);color:var(--muted);border:1px solid var(--line-strong)}

.c-meta{
    display:flex;gap:10px;padding:6px 14px;
    background:var(--surface-soft);border-bottom:1px solid var(--line);
}
.m-item{font-size:10px;color:var(--muted);display:flex;align-items:center;gap:3px;font-weight:700}
.m-item b{color:var(--text);font-weight:700}

/* SET DIVIDER */
.set-divider{
    display:flex;align-items:center;justify-content:space-between;
    padding:7px 14px;gap:10px;
    background:linear-gradient(90deg,rgba(22,131,255,.08),transparent);
    border-bottom:1px solid var(--line);
    border-top:2px solid rgba(22,131,255,.15);
}
.set-divider:first-child{border-top:none}
.set-divider:active{background:rgba(22,131,255,.14)}
.set-divider.served{opacity:.4}
.view-done .set-divider.served{opacity:1}
.set-label{font-size:12px;font-weight:800;color:var(--primary-deep);display:flex;align-items:center;gap:5px;flex:1;min-width:0}
.set-divider.served .set-label{text-decoration:line-through;color:var(--muted)}
.view-done .set-divider.served .set-label{text-decoration:none;color:var(--primary-deep)}
.set-qty{font-size:11px;font-weight:700;color:var(--primary);flex-shrink:0}
.set-pid{font-size:10px;font-weight:700;color:var(--muted);font-family:monospace;flex-shrink:0;opacity:.7}
.nonkds-badge{font-size:10px;font-weight:600;color:#6b7280;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;padding:1px 7px;flex-shrink:0}

/* ITEM ROW */
.irow{
    display:flex;align-items:center;padding:11px 14px;gap:10px;
    border-bottom:1px solid var(--line);cursor:pointer;user-select:none;
    transition:background .1s;-webkit-user-select:none;
}
.irow:last-child{border-bottom:none}
.irow:active{background:var(--surface-soft)}
.irow.served{opacity:.4}
.irow.locked{cursor:default}
.irow.locked:active{background:transparent}

/* ในหน้า "เสิร์ฟแล้ว" แสดงข้อความปกติ ไม่ต้องซีด+ขีดฆ่า */
.view-done .irow.served{opacity:1}
.view-done .irow.served .i-name{text-decoration:none;color:var(--text)}
.view-done .irow.served .i-time{opacity:1}
.view-done .irow.served .i-staff{opacity:1}

.chk{
    width:28px;height:28px;flex-shrink:0;border-radius:8px;
    border:2px solid var(--line-strong);display:flex;align-items:center;justify-content:center;
    transition:all .2s;position:relative;overflow:hidden;background:#fff;
}
.chk::after{
    content:'';position:absolute;inset:0;
    background:var(--success);transform:scale(0);border-radius:6px;
    transition:transform .2s cubic-bezier(.34,1.56,.64,1);
}
.chk-ico{position:relative;z-index:1;opacity:0;transform:scale(.5);transition:all .2s}
.irow.served .chk{border-color:var(--success)}
.irow.served .chk::after{transform:scale(1)}
.irow.served .chk-ico{opacity:1;transform:scale(1)}

.i-info{flex:1;min-width:0}
.i-name{font-size:14px;font-weight:700;line-height:1.3;color:var(--text)}
.irow.served .i-name{text-decoration:line-through;color:var(--muted)}
.i-comment{font-size:11px;color:var(--warning);font-weight:600;margin-top:2px;line-height:1.3}
.i-tags{display:flex;gap:4px;margin-top:3px;flex-wrap:wrap}
.tag{font-size:9px;padding:2px 7px;border-radius:4px;font-weight:700}
.tag.set{background:#eef6ff;color:#1758a5;border:1px solid #d5e7ff}
.tag.sub{background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe}
.tag.add{background:var(--success-soft);color:var(--success);border:1px solid #bfeacc}

.i-qty{
    font-size:13px;font-weight:700;color:#9a5200;
    background:var(--secondary-soft);border:1px solid #ffd8b0;
    border-radius:8px;padding:3px 10px;flex-shrink:0;
}
.i-time{
    font-size:11px;font-weight:700;color:var(--muted);
    background:var(--surface-soft);border:1px solid var(--line);
    border-radius:6px;padding:2px 7px;flex-shrink:0;white-space:nowrap;
}
.irow.served .i-time{opacity:.5}
.i-staff{
    font-size:11px;font-weight:700;color:var(--success);
    background:var(--success-soft);border:1px solid #bfeacc;
    border-radius:6px;padding:2px 7px;flex-shrink:0;white-space:nowrap;
}

/* CONFIRM MODAL */
.modal-backdrop{
    position:fixed;inset:0;z-index:500;
    background:rgba(10,30,60,.45);backdrop-filter:blur(3px);
    display:flex;align-items:center;justify-content:center;
    opacity:0;pointer-events:none;transition:opacity .18s;
}
.modal-backdrop.show{opacity:1;pointer-events:all}
.modal-box{
    background:#fff;border-radius:20px;padding:28px 24px 20px;
    width:300px;max-width:90vw;box-shadow:0 24px 60px rgba(0,0,0,.22);
    transform:scale(.92);transition:transform .18s cubic-bezier(.34,1.56,.64,1);
    text-align:center;
}
.modal-backdrop.show .modal-box{transform:scale(1)}
.modal-ico{font-size:36px;margin-bottom:10px}
.modal-title{font-size:16px;font-weight:800;color:var(--text);margin-bottom:6px}
.modal-msg{font-size:13px;color:var(--muted);line-height:1.5;margin-bottom:20px}
.modal-btns{display:flex;gap:8px}
.modal-btn{
    flex:1;padding:11px;border:none;border-radius:12px;
    font-size:14px;font-weight:700;cursor:pointer;
    font-family:Tahoma,Arial,sans-serif;transition:opacity .15s;
}
.modal-btn:active{opacity:.8}
.modal-btn.cancel{background:var(--line);color:var(--muted)}
.modal-btn.confirm{background:linear-gradient(135deg,var(--warning),#b45309);color:#fff}

/* PROGRESS */
.prog-wrap{padding:8px 14px 10px}
.prog-track{height:4px;background:var(--line);border-radius:2px;overflow:hidden}
.prog-fill{height:100%;background:var(--success);border-radius:2px;transition:width .3s ease}
.prog-lbl{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-top:4px;font-weight:700}
.prog-lbl .pc{color:var(--success)}

/* COOKING BADGE */
.cook-badge{
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;
    background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;
    white-space:nowrap;
}

/* SERVE ALL BTN */
.srv-btn{
    margin:0 14px 13px;padding:11px;border:none;border-radius:var(--radius-sm);
    background:linear-gradient(135deg,var(--success),#0f8c45);
    color:#fff;font-family:Tahoma,Arial,sans-serif;font-size:14px;font-weight:700;
    cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;
    box-shadow:0 4px 12px rgba(18,161,80,.25);width:calc(100% - 28px);transition:all .15s;
}
.srv-btn:active{transform:scale(.98);box-shadow:none}
.srv-btn.done-btn{background:var(--line);color:var(--muted);box-shadow:none;cursor:default}
.srv-btn.unserve-btn{background:linear-gradient(135deg,var(--warning),#b45309);box-shadow:0 4px 12px rgba(217,119,6,.25)}
.srv-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}

/* EMPTY */
.empty{text-align:center;padding:64px 20px;color:var(--muted);grid-column:1/-1}
.empty .ico{font-size:48px;margin-bottom:16px}
.empty h3{font-size:16px;color:var(--text);font-weight:700}
.empty p{font-size:12px;margin-top:6px;line-height:1.7}

/* SALE-MODE SECTION SEPARATOR */
.mode-sep{grid-column:1/-1;display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--bg);border:1.5px solid var(--line);border-radius:10px;font-size:13px;font-weight:700;color:var(--text);letter-spacing:.3px;margin-top:4px}
.mode-sep-icon{font-size:16px;line-height:1}

/* LOADING */
.loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;gap:14px;color:var(--muted);grid-column:1/-1}
.spinner{width:28px;height:28px;border:2.5px solid var(--line);border-top-color:var(--primary);border-radius:50%;animation:rot .7s linear infinite}

/* TOAST */
.toast{
    position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(80px);
    padding:11px 22px;border-radius:24px;font-size:13px;font-weight:700;
    z-index:9999;transition:transform .3s cubic-bezier(.34,1.56,.64,1);
    white-space:nowrap;box-shadow:var(--shadow);pointer-events:none;background:#fff;
}
.toast.t-ok{border:1px solid #bfeacc;color:var(--success)}
.toast.t-err{border:1px solid #ffb3ab;color:var(--danger)}
.toast.show{transform:translateX(-50%) translateY(0)}

/* ERROR BANNER */
.err-banner{
    background:var(--danger-soft);border:1px solid #ffb3ab;color:var(--danger);
    font-size:12px;font-weight:700;padding:10px 14px;margin:8px 12px;
    border-radius:var(--radius-sm);display:none;
}
.err-banner.show{display:block}

        /* ── Fullscreen Button ── */
        .btn-fullscreen{
            display:inline-flex;align-items:center;gap:5px;
            padding:5px 11px;border-radius:9px;border:none;cursor:pointer;
            font-size:13px;font-weight:600;white-space:nowrap;
            background:rgba(255,255,255,0.18);color:#fff;
            backdrop-filter:blur(6px);
            transition:background .18s,transform .12s;
            flex-shrink:0;
        }
        .btn-fullscreen:hover{background:rgba(255,255,255,0.30);transform:scale(1.04)}
        .btn-fullscreen svg{width:15px;height:15px;flex-shrink:0}

/* ── Staff Info ── */
.staff-info{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border-radius:9px;padding:4px 10px}
.staff-name{font-size:12px;font-weight:700;color:#fff;white-space:nowrap}
.btn-logout{background:rgba(255,255,255,.2);border:none;border-radius:7px;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;cursor:pointer;font-family:Tahoma,Arial,sans-serif;transition:background .15s}
.btn-logout:hover{background:rgba(255,255,255,.35)}

/* ── Login Overlay ── */
.login-overlay{
    position:fixed;inset:0;z-index:999;
    background:linear-gradient(135deg,rgba(8,58,112,.97),rgba(22,131,255,.95),rgba(255,138,31,.90));
    display:flex;align-items:center;justify-content:center;
}
.login-overlay.hidden{display:none}
.login-box{
    background:#fff;border-radius:24px;padding:36px 32px;width:320px;max-width:90vw;
    box-shadow:0 24px 60px rgba(0,0,0,.25);text-align:center;
}
.login-icon{font-size:44px;margin-bottom:12px}
.login-title{font-size:20px;font-weight:800;color:var(--primary-deep);margin-bottom:4px}
.login-sub{font-size:12px;color:var(--muted);margin-bottom:24px}
.login-input{
    width:100%;padding:12px 14px;border:2px solid var(--line);border-radius:12px;
    font-size:16px;font-family:Tahoma,Arial,sans-serif;text-align:center;
    letter-spacing:.15em;outline:none;transition:border-color .2s;box-sizing:border-box;
}
.login-input:focus{border-color:var(--primary)}
.login-btn{
    width:100%;margin-top:14px;padding:13px;border:none;border-radius:12px;
    background:linear-gradient(135deg,var(--primary-deep),var(--primary));
    color:#fff;font-size:15px;font-weight:700;cursor:pointer;
    font-family:Tahoma,Arial,sans-serif;transition:opacity .15s;
}
.login-btn:active{opacity:.85}
.login-btn:disabled{opacity:.5;cursor:not-allowed}
.login-err{color:var(--danger);font-size:12px;font-weight:700;margin-top:10px;min-height:18px}

/* ── SETTINGS MODAL ── */
.settings-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s}
.settings-backdrop.show{opacity:1;pointer-events:all}
.settings-box{background:#fff;border-radius:18px;width:min(440px,95vw);max-height:88vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.28);display:flex;flex-direction:column}
.settings-head{padding:18px 20px 14px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px;position:sticky;top:0;background:#fff;z-index:1}
.settings-head-ico{font-size:22px}
.settings-head-title{font-size:16px;font-weight:800;color:var(--text);flex:1}
.settings-head-ver{font-size:11px;color:var(--muted);font-family:monospace}
.settings-close{background:none;border:none;font-size:20px;cursor:pointer;color:var(--muted);padding:2px 6px;border-radius:6px}
.settings-close:hover{background:var(--surface-soft)}
.settings-body{padding:16px 20px 20px;display:flex;flex-direction:column;gap:18px}
.s-section{}
.s-section-title{font-size:11px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--primary);margin-bottom:10px;padding-bottom:5px;border-bottom:2px solid var(--primary-pale,#e8f0fe)}
.s-row{display:flex;flex-direction:column;gap:3px;margin-bottom:10px}
.s-row:last-child{margin-bottom:0}
.s-label{font-size:12px;font-weight:700;color:var(--muted)}
.s-input{border:1.5px solid var(--line);border-radius:8px;padding:8px 10px;font-size:13px;font-family:inherit;outline:none;transition:border-color .15s;width:100%;box-sizing:border-box}
.s-input:focus{border-color:var(--primary)}
.s-row-inline{display:flex;gap:8px}
.s-row-inline .s-input{flex:1}
.s-pass-wrap{position:relative}
.s-pass-wrap .s-input{padding-right:70px}
.s-pass-toggle{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:11px;font-weight:700;color:var(--primary);cursor:pointer;padding:2px 4px}
.s-select{border:1.5px solid var(--line);border-radius:8px;padding:8px 10px;font-size:13px;font-family:inherit;outline:none;background:#fff;width:100%;box-sizing:border-box}
.s-toggle-row{display:flex;align-items:center;justify-content:space-between;padding:4px 0}
.s-toggle-label{font-size:13px;color:var(--text)}
.s-toggle{position:relative;width:42px;height:24px;flex-shrink:0}
.s-toggle input{opacity:0;width:0;height:0;position:absolute}
.s-toggle-track{position:absolute;inset:0;background:#ccc;border-radius:999px;transition:background .2s;cursor:pointer}
.s-toggle input:checked + .s-toggle-track{background:var(--primary)}
.s-toggle-track::after{content:'';position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.s-toggle input:checked + .s-toggle-track::after{transform:translateX(18px)}
.s-info-grid{display:grid;grid-template-columns:auto 1fr;gap:4px 12px;font-size:12px}
.s-info-key{color:var(--muted);font-weight:700}
.s-info-val{color:var(--text);font-family:monospace;word-break:break-all}
.settings-foot{padding:14px 20px;border-top:1px solid var(--line);display:flex;gap:8px;position:sticky;bottom:0;background:#fff}
.s-btn{flex:1;padding:10px;border-radius:10px;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;border:none;transition:opacity .15s}
.s-btn:active{opacity:.8}
.s-btn-cancel{background:var(--surface-soft);color:var(--muted)}
.s-btn-save{background:var(--primary);color:#fff}
.s-btn-save:disabled{opacity:.5;cursor:not-allowed}
.s-msg{font-size:12px;font-weight:700;padding:6px 10px;border-radius:8px;margin-top:6px;display:none}
.s-msg.ok{background:#dcfce7;color:#15803d;display:block}
.s-msg.err{background:#fee2e2;color:#b91c1c;display:block}
.logo-tap-hint{outline:2px solid var(--primary);outline-offset:3px;border-radius:8px;animation:logo-tap-flash .3s ease}
@keyframes logo-tap-flash{0%{opacity:.5}100%{opacity:1}}

/* ── BARCODE SCANNER ── */
.scan-cam-btn{appearance:none;border:1px solid rgba(255,255,255,.2);border-radius:12px;min-height:34px;padding:0 12px;font-size:12px;font-weight:700;cursor:pointer;background:rgba(255,255,255,.18);color:#fff;font-family:Tahoma,Arial,sans-serif;display:flex;align-items:center;gap:5px;white-space:nowrap;transition:all .15s}
.scan-cam-btn:active{background:rgba(255,255,255,.28)}
.scan-cam-btn.active{background:var(--secondary);border-color:var(--secondary)}
.scan-overlay{position:fixed;inset:0;z-index:800;background:#000;display:flex;align-items:center;justify-content:center}
.scan-overlay.hidden{display:none}
.scan-video{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
.scan-ov-head{position:absolute;top:0;left:0;right:0;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(180deg,rgba(0,0,0,.65),transparent);z-index:2}
.scan-ov-title{color:#fff;font-size:14px;font-weight:700}
.scan-ov-close{background:rgba(255,255,255,.15);border:none;color:#fff;font-size:18px;width:38px;height:38px;border-radius:999px;cursor:pointer}
.scan-frame{position:absolute;width:min(280px,72vw);height:min(200px,52vw);border-radius:16px;box-shadow:0 0 0 9999px rgba(0,0,0,.52);z-index:1}
.scan-corner{position:absolute;width:28px;height:28px}
.scan-corner.tl{top:-3px;left:-3px;border-top:4px solid var(--secondary);border-left:4px solid var(--secondary);border-radius:8px 0 0 0}
.scan-corner.tr{top:-3px;right:-3px;border-top:4px solid var(--secondary);border-right:4px solid var(--secondary);border-radius:0 8px 0 0}
.scan-corner.bl{bottom:-3px;left:-3px;border-bottom:4px solid var(--secondary);border-left:4px solid var(--secondary);border-radius:0 0 0 8px}
.scan-corner.br{bottom:-3px;right:-3px;border-bottom:4px solid var(--secondary);border-right:4px solid var(--secondary);border-radius:0 0 8px 0}
.scan-laser{position:absolute;width:min(280px,72vw);height:2px;background:linear-gradient(90deg,transparent,var(--secondary),transparent);box-shadow:0 0 8px var(--secondary);animation:laser 1.8s ease-in-out infinite;z-index:2}
@keyframes laser{0%,100%{transform:translateY(-min(100px,26vw))}50%{transform:translateY(min(100px,26vw))}}
.scan-hint-bar{position:absolute;bottom:70px;color:rgba(255,255,255,.9);font-size:12px;font-weight:700;padding:7px 18px;background:rgba(0,0,0,.45);border-radius:999px;z-index:2}
.scan-flash{position:absolute;inset:0;background:rgba(18,161,80,.3);opacity:0;transition:opacity .12s;z-index:3;pointer-events:none}
.scan-flash.show{opacity:1}
.scan-digit-bar{position:fixed;bottom:76px;left:50%;transform:translateX(-50%);background:rgba(10,30,60,.92);color:#fff;font-size:15px;font-weight:700;padding:9px 22px;border-radius:999px;z-index:9998;letter-spacing:.18em;box-shadow:var(--shadow);display:none;backdrop-filter:blur(8px);white-space:nowrap}
.scan-digit-bar.show{display:block}
</style>
</head>
<body>

<!-- LOGIN OVERLAY -->
<div class="login-overlay" id="loginOverlay">
  <div class="login-box">
    <div class="login-icon">🍽️</div>
    <div class="login-title">Serve Display</div>
    <div class="login-sub">กรอกรหัสพนักงานเพื่อเข้าใช้งาน</div>
    <input class="login-input" id="loginInput" type="text" placeholder="รหัสพนักงาน" autocomplete="off" maxlength="20">
    <button class="login-btn" id="loginBtn" onclick="doLogin()">เข้าสู่ระบบ</button>
    <div class="login-err" id="loginErr"></div>
  </div>
</div>

<!-- HEADER -->
<div class="hdr">
  <div class="hdr-l" id="hdrLogo" title="กดสามครั้งเพื่อตั้งค่า">
    <div class="hdr-icon">🍽️</div>
    <div>
      <div class="hdr-title">เสิร์ฟอาหาร</div>
      <div class="hdr-sub">Serve Display</div>
    </div>
  </div>
  <div class="hdr-r">
    <div class="live"></div>
    <div class="clock" id="clock">--:--</div>
    <div class="staff-info" id="staffInfo" style="display:none">
      <span class="staff-name" id="staffNameDisplay"></span>
      <button class="btn-logout" onclick="doLogout()">ออก</button>
    </div>
    <button class="scan-cam-btn" id="scanCamBtn" onclick="toggleCamera()" title="สแกนบาร์โค้ดด้วยกล้อง">📷 กล้อง</button>
    <button class="rfbtn" id="rfBtn" onclick="loadData()">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12a9 9 0 0 1-9 9 9 9 0 0 1-6.36-2.64L3 21V15h6l-2.73 2.73A7 7 0 0 0 19 12z"/>
        <path d="M3 12a9 9 0 0 1 9-9 9 9 0 0 1 6.36 2.64L21 3v6h-6l2.73-2.73A7 7 0 0 0 5 12z"/>
      </svg>
      รีเฟรช
    </button>
    <button type="button" class="btn-fullscreen" id="fsBtn" title="เต็มจอ">
      <svg class="fs-ico-enter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
      <svg class="fs-ico-exit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="display:none"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
      เต็มจอ
    </button>
  </div>
</div>


<!-- STICKY FILTER + SEARCH -->
<div class="sticky-bar">
  <div class="fbar">
    <button class="fbtn on" id="fb-wait"    onclick="setFilter('wait')">⏳ รอเสิร์ฟ <span class="cnt" id="fc-wait">-</span></button>
    <button class="fbtn"    id="fb-done"    onclick="setFilter('done')">✅ เสิร์ฟแล้ว <span class="cnt" id="fc-done">-</span></button>
    <button class="fbtn"    id="fb-kitchen" onclick="setFilter('kitchen')">🍳 อยู่ในครัว <span class="cnt" id="fc-kitchen">-</span></button>
  </div>
  <div class="sbar">
    <div class="sbar-wrap">
      <span class="sbar-ico">🔍</span>
      <input class="sbar-inp" id="searchInp" type="text" placeholder="ค้นหาโต๊ะ..." autocomplete="off" oninput="onSearch(this.value)">
      <button class="sbar-clr" id="searchClr" onclick="clearSearch()">✕</button>
    </div>
    <div class="tchips" id="tchips"></div>
  </div>
</div>

<div class="err-banner" id="errBanner"></div>
<div class="content" id="main">
  <div class="loading"><div class="spinner"></div><span>กำลังโหลด...</span></div>
</div>
<div class="toast" id="toast"></div>

<!-- CUSTOM CONFIRM MODAL -->
<!-- CAMERA OVERLAY -->
<div class="scan-overlay hidden" id="scanOverlay">
  <video class="scan-video" id="scanVideo" autoplay playsinline muted></video>
  <div class="scan-flash" id="scanFlash"></div>
  <div class="scan-ov-head">
    <span class="scan-ov-title">📷 สแกนบาร์โค้ด</span>
    <button class="scan-ov-close" onclick="stopCamera()">✕</button>
  </div>
  <div class="scan-frame">
    <div class="scan-corner tl"></div>
    <div class="scan-corner tr"></div>
    <div class="scan-corner bl"></div>
    <div class="scan-corner br"></div>
  </div>
  <div class="scan-laser"></div>
  <div class="scan-hint-bar" id="scanHintBar">จ่อกล้องไปที่บาร์โค้ด</div>
</div>
<div class="scan-digit-bar" id="scanDigitBar">🔍 <span id="scanDigitText"></span></div>

<!-- SETTINGS MODAL -->
<div class="settings-backdrop" id="settingsModal">
  <div class="settings-box">
    <div class="settings-head">
      <div class="settings-head-ico">⚙️</div>
      <div class="settings-head-title">ตั้งค่าโปรแกรม</div>
      <span class="settings-head-ver" id="sVerBadge">v1.1.0</span>
      <button class="settings-close" onclick="closeSettings()">✕</button>
    </div>
    <div class="settings-body">

      <div class="s-section">
        <div class="s-section-title">ฐานข้อมูล</div>
        <div class="s-row">
          <div class="s-label">Host / IP Address</div>
          <input class="s-input" id="s-db-host" type="text" placeholder="127.0.0.1" autocomplete="off">
        </div>
        <div class="s-row">
          <div class="s-label">ชื่อฐานข้อมูล</div>
          <input class="s-input" id="s-db-name" type="text" placeholder="database_name" autocomplete="off">
        </div>
        <input type="hidden" id="s-db-port">
        <input type="hidden" id="s-db-user">
        <input type="hidden" id="s-db-pass">
      </div>

      <div class="s-section">
        <div class="s-section-title">Computer</div>
        <div class="s-row-inline">
          <div class="s-row" style="flex:1">
            <div class="s-label">Computer ID</div>
            <input class="s-input" id="s-computer-id" type="number" placeholder="1" min="0">
          </div>
          <div class="s-row" style="flex:2">
            <div class="s-label">ชื่อ Computer</div>
            <input class="s-input" id="s-computer-name" type="text" placeholder="Serve 1" autocomplete="off">
          </div>
        </div>
      </div>

      <div class="s-section">
        <div class="s-section-title">การแสดงผล</div>
        <div class="s-row">
          <div class="s-label">ความถี่รีเฟรช</div>
          <select class="s-select" id="s-refresh">
            <option value="10">10 วินาที</option>
            <option value="15">15 วินาที</option>
            <option value="30" selected>30 วินาที</option>
            <option value="60">60 วินาที</option>
          </select>
        </div>
        <div class="s-toggle-row">
          <span class="s-toggle-label">🔔 เสียงแจ้งเตือนเมื่อมีออเดอร์ใหม่</span>
          <label class="s-toggle">
            <input type="checkbox" id="s-sound">
            <span class="s-toggle-track"></span>
          </label>
        </div>
        <div class="s-toggle-row">
          <span class="s-toggle-label">📷 ระบบสแกนบาร์โค้ด (กล้อง + เครื่องยิง)</span>
          <label class="s-toggle">
            <input type="checkbox" id="s-barcode">
            <span class="s-toggle-track"></span>
          </label>
        </div>
      </div>

      <div class="s-section">
        <div class="s-section-title">ข้อมูลโปรแกรม</div>
        <div class="s-info-grid" id="sInfoGrid"></div>
      </div>

      <div class="s-msg" id="sMsg"></div>
    </div>
    <div class="settings-foot">
      <button class="s-btn s-btn-cancel" onclick="closeSettings()">ยกเลิก</button>
      <button class="s-btn s-btn-save" id="sSaveBtn" onclick="saveSettings()">💾 บันทึก</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="confirmModal">
  <div class="modal-box">
    <div class="modal-ico" id="modalIco">↩️</div>
    <div class="modal-title" id="modalTitle"></div>
    <div class="modal-msg"  id="modalMsg"></div>
    <div class="modal-btns">
      <button class="modal-btn cancel"  id="modalCancel">ยกเลิก</button>
      <button class="modal-btn confirm" id="modalConfirm">ยืนยัน</button>
    </div>
  </div>
</div>

<script>
/* ============================================================
   CONFIG
   StaffID ควร inject จาก session PHP จริง
   เช่น: const STAFF_ID = <?= $_SESSION['staff_id'] ?? 0 ?>;
============================================================ */
const API = 'api_waiter.php';
const BARCODE_MIN_LENGTH    = 1;  // ความยาวขั้นต่ำของรหัสที่รับได้
const BARCODE_DIGITS_DISPLAY = 6;  // zero-pad ให้ครบกี่หลัก
let refreshSec  = parseInt(localStorage.getItem('waiter_refresh_sec') || '30', 10);
let soundEnabled = localStorage.getItem('waiter_sound') === '1';
let   STAFF_ID    = 0;
let   STAFF_NAME  = '';

/* ── state ── */
let tables         = [];
let rawRows        = [];
let cooking        = {}; // {TableID: stillCookingCount}
let cookingRows    = []; // full rows for kitchen tab
let filter         = 'wait';
let search         = '';
let timer          = null;
let allowedPrinters = new Set(); // PrinterID ที่ station นี้ดูแล; ว่าง = ไม่กรอง
let saleModes = {}; // {SaleModeID: SaleModeName}
const pending = new Set(); // rowKey ที่กำลัง POST อยู่

/* ============================================================
   NONKDS — รายการของ station อื่น → virtual ServeStatus=1
============================================================ */
function applyNonKds(rows) {
  if (!allowedPrinters.size) return; // ไม่ได้ config → ไม่กรอง
  // mark badge "ครัวอื่น" เท่านั้น — ไม่เปลี่ยน ServeStatus, ยังกดติ๊กได้ปกติ
  rows.forEach(r => {
    const pid = parseInt(r.PrinterID, 10);
    if (isNaN(pid) || !allowedPrinters.has(pid)) {
      r._nonKds = true;
    }
  });
}

/* ============================================================
   LOAD จาก api_waiter.php
============================================================ */
async function loadData() {
  const btn = document.getElementById('rfBtn');
  btn.classList.add('spin');
  clearTimeout(timer);

  try {
    const res  = await fetch(`${API}?action=list_pending`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();

    if (!json.success) throw new Error(json.message || 'API error');

    allowedPrinters = new Set((json.allowed_printer_ids || []).map(Number));
    saleModes    = json.sale_modes   || {};
    rawRows      = json.rows;
    applyNonKds(rawRows);
    cooking      = json.cooking      || {};
    cookingRows  = json.cooking_rows || [];
    tables       = groupByTable(rawRows);

    console.log(
      `[serve] rows=${rawRows.length}`,
      `printers=[${[...allowedPrinters].join(',')||'ไม่ได้ config'}]`,
      `nonKds=${rawRows.filter(r=>r._nonKds).length}`,
      `wait=${rawRows.filter(r=>r.ServeStatus==0).length}`,
      `done=${rawRows.filter(r=>r.ServeStatus==1).length}`
    );
    hideError();

  } catch (e) {
    showError('โหลดข้อมูลไม่ได้: ' + e.message);
  } finally {
    btn.classList.remove('spin');
    render();
    timer = setTimeout(loadData, refreshSec * 1000);
  }
}

/* ── group rows by TableID ── */
function groupByTable(rows) {
  const map = {};
  rows.forEach(r => {
    const smId = parseInt(r.SaleModeID, 10) || 0;
    const tblKey = parseInt(r.TableID, 10) || 0;
    const isDelivery = tblKey === 0;
    // delivery orders (TableID=0) ใช้ DisplayTableName + SaleModeID แยกบิล
    // TransactionID ใน production อาจ collide → ไม่น่าเชื่อถือ
    const k = !isDelivery ? `${tblKey}_${smId}` : `d_${r.DisplayTableName || ''}_${smId}`;
    if (!map[k]) map[k] = {
      tableId:    r.TableID,
      tableName:  r.DisplayTableName || String(r.TableID),
      queueName:  r.QueueName || '',
      saleModeId: smId,
      isDelivery: isDelivery,
      earliest:   r.FinishDateTime || r.SubmitOrderDateTime || '',
      rows: []
    };
    // ใช้ชื่อโต๊ะล่าสุด (รองรับโต๊ะโอน A3->A5)
    if (r.FinishDateTime && r.FinishDateTime > map[k].earliest) {
      map[k].tableName = r.DisplayTableName || String(r.TableID);
      map[k].earliest  = r.FinishDateTime;
    }
    if (!map[k].queueName && r.QueueName) map[k].queueName = r.QueueName;
    if (!map[k].transactionId && r.TransactionID) map[k].transactionId = parseInt(r.TransactionID, 10) || 0;
    map[k].rows.push(r);
  });
  return Object.values(map).sort((a,b) => {
    // pending ก่อน → sort ตามเวลาออกจากครัว
    const aDone = a.rows.every(r => r.ServeStatus == 1) ? 1 : 0;
    const bDone = b.rows.every(r => r.ServeStatus == 1) ? 1 : 0;
    return aDone - bDone || a.earliest.localeCompare(b.earliest);
  });
}

/* ============================================================
   RENDER
============================================================ */
function render() {
  const nW = tables.filter(t => t.rows.some(r => r.ServeStatus == 0)).length;
  const nD = tables.filter(t => t.rows.some(r => r.ServeStatus == 1)).length;
  const nK = Object.keys(cooking).length;
  document.getElementById('fc-wait').textContent    = nW;
  document.getElementById('fc-done').textContent    = nD;
  document.getElementById('fc-kitchen').textContent = nK;

  // update table chips
  renderChips();

  const el = document.getElementById('main');

  // kitchen tab — read-only
  if (filter === 'kitchen') {
    const filteredCookRows = allowedPrinters.size
      ? cookingRows.filter(r => { const pid = parseInt(r.PrinterID, 10); return pid === -1 || allowedPrinters.has(pid); })
      : cookingRows;
    let kTables = groupByTable(filteredCookRows);
    if (search) {
      const q = search.toLowerCase();
      kTables = kTables.filter(t => String(t.tableName).toLowerCase().includes(q) || String(t.tableId).includes(q));
    }
    if (!kTables.length) {
      el.innerHTML = `<div class="empty"><div class="ico">🍳</div><h3>ไม่มีรายการในครัว</h3><p></p></div>`;
      return;
    }
    el.innerHTML = kTables.map(t => buildKitchenCard(t)).join('');
    return;
  }

  // wait / done tabs
  let shown = tables.filter(t => t.rows.some(r => r.ServeStatus == 0));
  if (filter === 'done') shown = tables.filter(t => t.rows.some(r => r.ServeStatus == 1));
  if (search) {
    const q = search.toLowerCase();
    shown = shown.filter(t => String(t.tableName).toLowerCase().includes(q) || String(t.tableId).includes(q));
  }

  if (!shown.length) {
    const emptyMsg = filter === 'done'
      ? { ico: '📋', h: 'ยังไม่มีรายการที่เสิร์ฟแล้ว', p: '' }
      : { ico: '🎉', h: 'เสิร์ฟครบทุกโต๊ะแล้ว!', p: 'ไม่มีรายการค้าง 👍' };
    el.innerHTML = `<div class="empty"><div class="ico">${emptyMsg.ico}</div><h3>${emptyMsg.h}</h3><p>${emptyMsg.p}</p></div>`;
    return;
  }

  // จัดกลุ่มตาม SaleModeID → แสดง section header เมื่อมีมากกว่า 1 mode
  const modeGroups = {};
  shown.forEach(t => {
    const smId = t.saleModeId || 0;
    if (!modeGroups[smId]) modeGroups[smId] = [];
    modeGroups[smId].push(t);
  });
  const modeIds = Object.keys(modeGroups).map(Number).sort((a, b) => a - b);
  const multiMode = modeIds.length > 1;

  const parts = [];
  modeIds.forEach(smId => {
    if (multiMode) {
      const name = saleModes[smId] || (smId === 0 ? 'ทั่วไป' : `Mode ${smId}`);
      const icon = saleModeIcon(smId, name);
      parts.push(`<div class="mode-sep"><span class="mode-sep-icon">${icon}</span>${escHtml(name)}</div>`);
    }
    modeGroups[smId].forEach(t => parts.push(buildCard(t, filter === 'done')));
  });
  el.innerHTML = parts.join('');
}

/* ── build card HTML ── */
function buildCard(t, allowUnserve = false) {
  const total     = t.rows.length;
  const served    = t.rows.filter(r => r.ServeStatus == 1).length;
  const allDone   = served === total;
  const pct       = total ? Math.round(served / total * 100) : 0;
  const stillCook = cooking[t.tableId] || 0;

  // ต้อง declare cardTitle/modeLabel ก่อนใช้ใน btn (แก้ ReferenceError temporal dead zone)
  const modeLabel = t.isDelivery
    ? (saleModes[t.saleModeId] || `Mode ${t.saleModeId}`)
    : 'โต๊ะ';
  const cardTitle = t.isDelivery
    ? (t.queueName || t.tableName)
    : t.tableName;

  const cls  = allDone ? 'c-done' : served > 0 ? 'c-part' : 'c-rdy';
  const pill = allDone
    ? `<span class="pill p-done">✅ เสิร์ฟครบ</span>`
    : served > 0
      ? `<span class="pill p-part">⏳ ${served}/${total}</span>`
      : `<span class="pill p-rdy">🟢 รอเสิร์ฟ</span>`;

  const t0  = t.earliest ? new Date(t.earliest.replace(' ', 'T')) : null;
  const ts  = t0 ? `${pad(t0.getHours())}:${pad(t0.getMinutes())}` : '--:--';

  // wait tab: แสดงเฉพาะที่ยังไม่เสิร์ฟ / done tab: แสดงเฉพาะที่เสิร์ฟแล้ว
  // set header แสดงเมื่อมี sub-item ในฝั่งนั้นๆ อยู่
  const visibleRows = sortItemsBySet(t.rows).filter(r => {
    if (r.ProductSetType == 7) {
      const mySubs = t.rows.filter(s => parseInt(s.ProductSetType) < 0 && s.ParentProcessID == r.ProcessID);
      if (!mySubs.length) return false; // sub-items ยังอยู่ในครัวหรือไม่มีเลย → ซ่อนหัวโล่ง
      if (allowUnserve) {
        return mySubs.some(s => s.ServeStatus == 1) || r.ServeStatus == 1;
      }
      return mySubs.some(s => s.ServeStatus == 0) || r.ServeStatus == 0;
    }
    return allowUnserve ? r.ServeStatus == 1 : r.ServeStatus == 0;
  });

  const items = visibleRows.map(r => {
    const srv = r.ServeStatus == 1;
    const key = rowKey(r);

    // หัวเซ็ต → แสดงเป็น divider
    if (r.ProductSetType == 7) {
      const ft = r.FinishDateTime ? (() => {
        const d = new Date(r.FinishDateTime.replace(' ', 'T'));
        return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
      })() : '';
      const onclick = srv ? `onclick="confirmUnserve('${key}')"` : `onclick="tapItem('${key}')"`;
      return `<div class="set-divider${srv ? ' served' : ''}${r._nonKds ? ' nonkds' : ''}" data-key="${key}" ${onclick}>
        <div class="set-label">📦 ${esc(r.ProductName)}</div>
        <div class="set-pid">#${String(r.ProcessID).padStart(6,'0')}</div>
        ${ft ? `<div class="i-time" style="margin-right:6px">🕐 ${ft}</div>` : ''}
        ${r._nonKds ? `<span class="nonkds-badge">ครัวอื่น</span>` : `<div class="set-qty">×${parseFloat(r.ProductAmount)}</div>`}
      </div>`;
    }

    let tag = '';
    if      (parseInt(r.ProductSetType) < 0) tag = `<span class="tag sub">↳ ในเซต</span>`;
    else if (r.ProductSetType == 15)          tag = `<span class="tag add">➕ Add-on</span>`;

    const ft = r.FinishDateTime ? (() => {
      const d = new Date(r.FinishDateTime.replace(' ', 'T'));
      return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
    })() : '';
    const staffLabel = (srv && r.ServingStaffName) ? `<div class="i-staff">👤 ${esc(r.ServingStaffName)}</div>` : '';
    const onclick = srv ? `onclick="confirmUnserve('${key}')"` : `onclick="tapItem('${key}')"`;
    return `<div class="irow${srv ? ' served' : ''}${r._nonKds ? ' nonkds' : ''}" data-key="${key}" ${onclick}>
      <div class="chk">
        <svg class="chk-ico" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="i-info">
        <div class="i-name">${esc(r.ProductName)}</div>
        ${r.ItemComment ? `<div class="i-comment">💬 ${esc(r.ItemComment)}</div>` : ''}
        ${tag ? `<div class="i-tags">${tag}</div>` : ''}
      </div>
      ${staffLabel}
      ${ft ? `<div class="i-time">🕐 ${ft}</div>` : ''}
      <div class="i-qty">×${parseFloat(r.ProductAmount)}</div>
    </div>`;
  }).join('');

  const txId = t.transactionId || 0;
  let btn;
  if (allDone) {
    btn = allowUnserve
      ? `<button class="srv-btn unserve-btn" onclick="tapUnserveAll(${t.tableId},${txId},'${esc(cardTitle)}')">↩️ ยกเลิกเสิร์ฟ ${esc(cardTitle)}</button>`
      : `<button class="srv-btn done-btn" disabled>✅ เสิร์ฟครบแล้ว</button>`;
  } else {
    btn = `<button class="srv-btn" onclick="tapServeAll(${t.tableId},${txId})">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        เสิร์ฟครบ ${esc(cardTitle)}
      </button>`;
  }


  const cardKey = t.isDelivery ? 'd_' + t.tableName : String(t.tableId);
  return `<div class="card ${cls}" data-table="${esc(cardKey)}">
    <div class="c-hdr">
      <div class="tbl-badge">
        <div class="tbl-num">${esc(cardTitle)}</div>
        <div class="tbl-name">${esc(modeLabel)} ${t.isDelivery ? '' : esc(t.tableName)}</div>
        ${t.isDelivery && t.tableName !== cardTitle ? `<div class="queue-badge">🎫 ${esc(t.tableName)}</div>` : ''}
        ${!t.isDelivery && t.queueName ? `<div class="queue-badge">🎫 ${esc(t.queueName)}</div>` : ''}
      </div>
      ${pill}
    </div>
    <div class="c-meta">
      <div class="m-item">🕐 <b>${ts}</b></div>
      <div class="m-item">🍽️ <b>${total}</b> รายการ</div>
      <div class="m-item" style="color:var(--grn)">✅ <b>${served}/${total}</b></div>
      ${stillCook > 0 ? `<div class="m-item"><span class="cook-badge">🍳 ยังทำอยู่ ${stillCook}</span></div>` : ''}
    </div>
    <div class="items">${items}</div>
    <div class="prog-wrap">
      <div class="prog-track"><div class="prog-fill" style="width:${pct}%"></div></div>
      <div class="prog-lbl"><span>ความคืบหน้า</span><span class="pc">${served}/${total}</span></div>
    </div>
    ${btn}
  </div>`;
}

/* ============================================================
   ACTIONS — POST ไปที่ api_waiter.php
============================================================ */
async function postServeRow(action, row) {
  const fd = new FormData();
  fd.append('action',         action);
  fd.append('ProductLevelID', row.ProductLevelID);
  fd.append('ProcessID',      row.ProcessID);
  fd.append('SubProcessID',   row.SubProcessID);
  fd.append('PrinterID',      row.PrinterID);
  fd.append('TableID',        row.TableID);
  fd.append('StaffID',        STAFF_ID);
  const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const json = await res.json();
  if (!json.success) throw new Error(json.message);
}

async function tapItem(key) {
  if (!STAFF_ID) { toast('⚠️ กรุณาล็อกอินก่อน', true); return; }
  if (pending.has(key)) return;

  const r = rawRows.find(r => rowKey(r) === key);
  if (!r) return;
  const wasServed = r.ServeStatus == 1;
  const newStatus = wasServed ? 0 : 1;
  const action    = wasServed ? 'unserve_item' : 'serve_item';
  const isHeader  = r.ProductSetType == 7;

  // ถ้าเป็นหัวเซ็ต รวม sub-item ที่ยังไม่อยู่ใน target state ด้วย
  const targets = [r];
  if (isHeader) {
    rawRows
      .filter(s => parseInt(s.ProductSetType) < 0 && s.ParentProcessID == r.ProcessID && s.ServeStatus != newStatus)
      .forEach(s => targets.push(s));
  }

  // lock ทุก target
  targets.forEach(t => pending.add(rowKey(t)));

  // บันทึกสถานะเดิมไว้ rollback
  const saved = targets.map(t => ({ t, status: t.ServeStatus, name: t.ServingStaffName }));

  // Optimistic update
  targets.forEach(t => {
    t.ServeStatus      = newStatus;
    t.ServingStaffName = wasServed ? '' : STAFF_NAME;
  });
  tables = groupByTable(rawRows);
  render();
  toast(wasServed ? '↩️ ยกเลิกติ๊ก' : '✅ ติ๊กเสิร์ฟแล้ว');

  try {
    await Promise.all(targets.map(t => postServeRow(action, t)));

    // Part B: sub-item ครบ → auto-serve header
    if (!isHeader && !wasServed && r.ParentProcessID && r.ParentProcessID != '0') {
      const hdr = rawRows.find(h => h.ProcessID == r.ParentProcessID && h.ProductSetType == 7);
      if (hdr && hdr.ServeStatus != 1) {
        const siblings = rawRows.filter(s => parseInt(s.ProductSetType) < 0 && s.ParentProcessID == hdr.ProcessID);
        const siblingsInKitchen = cookingRows.filter(c => parseInt(c.ParentProcessID) == parseInt(hdr.ProcessID));
        if (siblings.every(s => s.ServeStatus == 1) && siblingsInKitchen.length === 0) {
          hdr.ServeStatus      = 1;
          hdr.ServingStaffName = STAFF_NAME;
          tables = groupByTable(rawRows);
          render();
          await postServeRow('serve_item', hdr);
        }
      }
    }
  } catch (e) {
    saved.forEach(({ t, status, name }) => { t.ServeStatus = status; t.ServingStaffName = name; });
    tables = groupByTable(rawRows);
    render();
    toast('⚠️ บันทึกไม่สำเร็จ', true);
  } finally {
    targets.forEach(t => pending.delete(rowKey(t)));
  }
}

async function tapServeAll(tableId, txId = 0) {
  if (!STAFF_ID) { toast('⚠️ กรุณาล็อกอินก่อน', true); return; }
  const lockKey = `table_${tableId}_${txId}`;
  if (pending.has(lockKey)) return;
  pending.add(lockKey);
  const t = tables.find(t => t.tableId == tableId && (txId === 0 || t.transactionId == txId));
  if (!t) { pending.delete(lockKey); return; }

  // Optimistic
  t.rows.forEach(r => r.ServeStatus = 1);
  tables = groupByTable(rawRows);
  render();
  toast(`✅ เสิร์ฟครบ ${t.queueName || t.tableName} แล้ว!`);

  try {
    const fd = new FormData();
    fd.append('action',  'serve_table');
    fd.append('TableID', tableId);
    fd.append('StaffID', STAFF_ID);
    if (txId > 0) fd.append('TransactionID', txId);
    const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
  } catch (e) {
    await loadData();
    toast('⚠️ บันทึกไม่สำเร็จ กำลังโหลดใหม่', true);
  } finally {
    pending.delete(lockKey);
  }
}

/* ── kitchen card (read-only) ── */
function buildKitchenCard(t) {
  const total = t.rows.length;
  const inProcess = t.rows.filter(r => r.ProcessStatus == 2).length;
  const waiting   = total - inProcess;
  const t0  = t.earliest ? new Date(t.earliest.replace(' ', 'T')) : null;
  const ts  = t0 ? `${pad(t0.getHours())}:${pad(t0.getMinutes())}` : '--:--';

  const pill = inProcess > 0
    ? `<span class="pill p-part">🍳 กำลังทำ ${inProcess}/${total}</span>`
    : `<span class="pill" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa">⏳ รอทำ ${waiting}</span>`;

  const items = sortItemsBySet(t.rows).map(r => {
    const isCooking = r.ProcessStatus == 2;
    if (r.ProductSetType == 7) {
      return `<div class="set-divider" style="cursor:default">
        <div class="set-label">📦 ${esc(r.ProductName)}</div>
        <div class="set-qty">×${parseFloat(r.ProductAmount)}</div>
      </div>`;
    }
    let tag = '';
    if      (parseInt(r.ProductSetType) < 0) tag = `<span class="tag sub">↳ ในเซต</span>`;
    else if (r.ProductSetType == 15)          tag = `<span class="tag add">➕ Add-on</span>`;
    return `<div class="irow" style="cursor:default">
      <div style="font-size:18px;flex-shrink:0">${isCooking ? '🍳' : '⏳'}</div>
      <div class="i-info">
        <div class="i-name">${esc(r.ProductName)}</div>
        ${tag ? `<div class="i-tags">${tag}</div>` : ''}
      </div>
      <div class="i-qty">×${parseFloat(r.ProductAmount)}</div>
    </div>`;
  }).join('');

  const ckKey = t.isDelivery ? 'd_' + t.tableName : String(t.tableId);
  return `<div class="card" style="border-color:#fed7aa;box-shadow:0 0 0 2px rgba(194,65,12,.08),var(--shadow)" data-table="${esc(ckKey)}">
    <div class="c-hdr">
      <div class="tbl-badge">
        <div class="tbl-num">${esc(t.tableName)}</div>
        <div class="tbl-name">โต๊ะ ${esc(t.tableName)}</div>
      </div>
      ${pill}
    </div>
    <div class="c-meta">
      <div class="m-item">🕐 <b>${ts}</b></div>
      <div class="m-item">🍽️ <b>${total}</b> รายการ</div>
    </div>
    <div class="items">${items}</div>
  </div>`;
}

/* ── filter ── */
function setFilter(f) {
  filter = f;
  ['wait', 'done', 'kitchen'].forEach(x =>
    document.getElementById('fb-' + x).classList.toggle('on', x === f)
  );
  document.getElementById('main').classList.toggle('view-done', f === 'done');
  render();
}

/* ── search ── */
function onSearch(val) {
  search = val.trim();
  document.getElementById('searchClr').classList.toggle('show', search.length > 0);
  render();
}
function clearSearch() {
  search = '';
  document.getElementById('searchInp').value = '';
  document.getElementById('searchClr').classList.remove('show');
  render();
}

/* ── table chips ── */
function renderChips() {
  const el = document.getElementById('tchips');
  if (!el) return;

  let src;
  if (filter === 'kitchen') {
    const filteredCook = allowedPrinters.size
      ? cookingRows.filter(r => { const pid = parseInt(r.PrinterID, 10); return pid === -1 || allowedPrinters.has(pid); })
      : cookingRows;
    src = groupByTable(filteredCook).map(t => ({ ...t, _kitchen: true }));
  } else if (filter === 'done') {
    src = tables.filter(t => t.rows.some(r => r.ServeStatus == 1));
  } else {
    src = tables.filter(t => t.rows.some(r => r.ServeStatus == 0));
  }

  el.innerHTML = src.map(t => {
    const cls = t._kitchen ? 'tchip' : (
      t.rows.every(r => r.ServeStatus == 1) ? 't-done' :
      t.rows.some(r  => r.ServeStatus == 1) ? 't-part' : 't-wait'
    );
    const ck = t.isDelivery ? 'd_' + t.tableName : String(t.tableId);
    return `<button class="tchip ${cls}" onclick="jumpToCard('${esc(ck)}')">${esc(t.tableName)}</button>`;
  }).join('');
}
function jumpToCard(ck) {
  if (search) clearSearch();
  setTimeout(() => {
    const card = document.querySelector(`#main .card[data-table="${ck}"]`);
    if (!card) return;
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    card.style.outline = '2.5px solid var(--primary)';
    card.style.outlineOffset = '2px';
    setTimeout(() => { card.style.outline = ''; card.style.outlineOffset = ''; }, 1200);
  }, 50);
}

/* ── confirm before unserve ── */
async function confirmUnserve(key) {
  if (!STAFF_ID) { toast('⚠️ กรุณาล็อกอินก่อน', true); return; }
  const ok = await showConfirm({ ico: '↩️', title: 'ยกเลิกการเสิร์ฟ?', msg: 'ต้องการยกเลิกรายการนี้ใช่ไหม', confirmLabel: 'ยกเลิกเสิร์ฟ' });
  if (!ok) return;
  await tapItem(key);
}

async function tapUnserveAll(tableId, txId, displayName) {
  if (!STAFF_ID) { toast('⚠️ กรุณาล็อกอินก่อน', true); return; }
  const ok = await showConfirm({ ico: '↩️', title: `ยกเลิกเสิร์ฟ ${displayName}?`, msg: 'รายการที่เสิร์ฟแล้วทั้งหมดจะถูกยกเลิก', confirmLabel: 'ยกเลิกเสิร์ฟ' });
  if (!ok) return;
  const lockKey = `untable_${tableId}_${txId}`;
  if (pending.has(lockKey)) return;
  pending.add(lockKey);
  const t = tables.find(t => t.tableId == tableId && (txId === 0 || t.transactionId == txId));
  if (!t) { pending.delete(lockKey); return; }

  t.rows.forEach(r => { r.ServeStatus = 0; });
  tables = groupByTable(rawRows);
  render();

  try {
    const fd = new FormData();
    fd.append('action',  'unserve_table');
    fd.append('TableID', tableId);
    fd.append('StaffID', STAFF_ID);
    if (txId > 0) fd.append('TransactionID', txId);
    const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    toast(`↩️ ยกเลิกเสิร์ฟ ${displayName} แล้ว`);
  } catch (e) {
    await loadData();
    toast('⚠️ บันทึกไม่สำเร็จ', true);
  } finally {
    pending.delete(lockKey);
  }
}

/* ============================================================
   HELPERS
============================================================ */
/* จัดกลุ่มรายการ: standalone → แต่ละ set header + ลูก */
function sortItemsBySet(rows) {
  const setHeaders  = rows.filter(r => r.ProductSetType == 7);
  if (!setHeaders.length) return rows;

  const subItems    = rows.filter(r => parseInt(r.ProductSetType) < 0);
  const standalones = rows.filter(r => r.ProductSetType != 7 && parseInt(r.ProductSetType) >= 0);

  const result = [...standalones];
  const placed  = new Set(standalones.map(r => r.ProcessID));

  setHeaders.forEach(hdr => {
    result.push(hdr);
    placed.add(hdr.ProcessID);
    subItems
      .filter(s => s.ParentProcessID == hdr.ProcessID && !placed.has(s.ProcessID))
      .forEach(s => {
        if (!s.FinishDateTime && hdr.FinishDateTime) s.FinishDateTime = hdr.FinishDateTime;
        result.push(s);
        placed.add(s.ProcessID);
      });
  });
  // orphan sub-items ที่หาหัวไม่เจอ
  subItems.filter(s => !placed.has(s.ProcessID)).forEach(s => result.push(s));
  return result;
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function saleModeIcon(id, name) {
  const n = (name || '').toLowerCase();
  if (n.includes('grab'))     return '🟢';
  if (n.includes('line'))     return '💚';
  if (n.includes('food'))     return '🛵';
  if (n.includes('delivery')) return '📦';
  if (n.includes('takeaway') || n.includes('take away') || n.includes('take-away') || n.includes('ซื้อกลับ') || n.includes('กลับบ้าน')) return '🥡';
  if (n.includes('dine')     || n.includes('โต๊ะ')    || n.includes('นั่งทาน'))  return '🍽️';
  return '🏷️';
}

function rowKey(r) {
  return `${r.ProductLevelID}_${r.ProcessID}_${r.SubProcessID}_${r.PrinterID}_${r.TableID}`;
}
function pad(n)  { return String(n).padStart(2, '0'); }
function esc(s)  { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

/* CUSTOM CONFIRM */
function showConfirm({ ico = '❓', title = '', msg = '', confirmLabel = 'ยืนยัน' } = {}) {
  return new Promise(resolve => {
    const backdrop  = document.getElementById('confirmModal');
    const btnOk     = document.getElementById('modalConfirm');
    const btnCancel = document.getElementById('modalCancel');
    document.getElementById('modalIco').textContent   = ico;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMsg').textContent   = msg;
    btnOk.textContent = confirmLabel;
    backdrop.classList.add('show');
    const close = ok => {
      backdrop.classList.remove('show');
      btnOk.removeEventListener('click', onOk);
      btnCancel.removeEventListener('click', onCancel);
      backdrop.removeEventListener('click', onBackdrop);
      resolve(ok);
    };
    const onOk       = () => close(true);
    const onCancel   = () => close(false);
    const onBackdrop = e => { if (e.target === backdrop) close(false); };
    btnOk.addEventListener('click', onOk);
    btnCancel.addEventListener('click', onCancel);
    backdrop.addEventListener('click', onBackdrop);
  });
}

/* TOAST */
let _tt;
function toast(msg, err = false) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className   = `toast show ${err ? 't-err' : 't-ok'}`;
  clearTimeout(_tt);
  _tt = setTimeout(() => el.classList.remove('show'), 2200);
}

/* ERROR BANNER */
function showError(msg) {
  const el = document.getElementById('errBanner');
  el.textContent = '⚠️ ' + msg;
  el.classList.add('show');
}
function hideError() {
  document.getElementById('errBanner').classList.remove('show');
}

/* CLOCK */
function tick() {
  const n = new Date();
  document.getElementById('clock').textContent =
    `${pad(n.getHours())}:${pad(n.getMinutes())}:${pad(n.getSeconds())}`;
}
setInterval(tick, 1000);
tick();

/* ============================================================
   LOGIN / LOGOUT
============================================================ */
function initAuth() {
  const saved = localStorage.getItem('waiter_staff');
  if (saved) {
    try {
      const s = JSON.parse(saved);
      if (s.staff_id && s.staff_name) {
        setStaff(s.staff_id, s.staff_name);
        loadData();
        return;
      }
    } catch(e) {}
  }
  showLoginOverlay();
}

function showLoginOverlay() {
  document.getElementById('loginOverlay').classList.remove('hidden');
  document.getElementById('staffInfo').style.display = 'none';
  setTimeout(() => document.getElementById('loginInput').focus(), 100);
}

function setStaff(id, name) {
  STAFF_ID   = id;
  STAFF_NAME = name;
  document.getElementById('staffNameDisplay').textContent = name;
  document.getElementById('staffInfo').style.display = 'flex';
  document.getElementById('loginOverlay').classList.add('hidden');
}

async function doLogin() {
  const code = document.getElementById('loginInput').value.trim();
  const err  = document.getElementById('loginErr');
  const btn  = document.getElementById('loginBtn');
  if (!code) { err.textContent = 'กรุณากรอกรหัสพนักงาน'; return; }

  btn.disabled = true;
  err.textContent = '';
  try {
    const fd = new FormData();
    fd.append('action', 'lookup_staff');
    fd.append('staff_code', code);
    const res  = await fetch(API, { method: 'POST', body: fd });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    localStorage.setItem('waiter_staff', JSON.stringify({ staff_id: json.staff_id, staff_name: json.staff_name }));
    setStaff(json.staff_id, json.staff_name);
    document.getElementById('loginInput').value = '';
    loadData();
  } catch(e) {
    err.textContent = e.message || 'เกิดข้อผิดพลาด';
  } finally {
    btn.disabled = false;
  }
}

function doLogout() {
  localStorage.removeItem('waiter_staff');
  STAFF_ID = 0;
  clearTimeout(timer);
  showLoginOverlay();
}

/* ============================================================
   BARCODE SCANNER — keyboard gun + camera
============================================================ */
let barcodeBuffer  = '';
let barcodeTimer   = null;
let lastScanPid    = 0;
let lastScanTime   = 0;
let cameraStream   = null;
let cameraAnim     = null;
let camCooldown    = false;
let isDetecting    = false;        // Bug fix: กัน async race ใน scanFrame
let jsQRLoaded     = false;
let barcodeEnabled = localStorage.getItem('waiter_barcode') !== '0'; // default เปิด

/* ── ซ่อน/แสดงปุ่มกล้องตาม barcodeEnabled ── */
function applyBarcodeEnabled() {
  const btn = document.getElementById('scanCamBtn');
  btn.style.display = barcodeEnabled ? '' : 'none';
  if (!barcodeEnabled && cameraStream) stopCamera();
}

/* ── keyboard / scanner gun ── */
document.addEventListener('keydown', e => {
  if (!barcodeEnabled) return;
  if (document.getElementById('settingsModal').classList.contains('show')) return;
  if (document.getElementById('confirmModal').classList.contains('show')) return;
  const tag = document.activeElement?.tagName;
  if (['INPUT','TEXTAREA','SELECT'].includes(tag)) return;

  if (e.key === 'Escape') { if (cameraStream) stopCamera(); return; }

  if (e.key === 'Enter') {
    if (barcodeBuffer.length >= BARCODE_MIN_LENGTH) {
      const buf = barcodeBuffer;
      barcodeBuffer = '';
      hideScanDigitBar();
      serveBarcodeCode(buf);
    }
    return;
  }

  if (/^\d$/.test(e.key)) {
    barcodeBuffer += e.key;
    showScanDigitBar(barcodeBuffer);
    clearTimeout(barcodeTimer);
    barcodeTimer = setTimeout(() => {
      barcodeBuffer = '';
      hideScanDigitBar();
    }, 1200);
  }
});

function showScanDigitBar(buf) {
  document.getElementById('scanDigitText').textContent = buf.padStart(BARCODE_DIGITS_DISPLAY, '0');
  document.getElementById('scanDigitBar').classList.add('show');
}
function hideScanDigitBar() {
  document.getElementById('scanDigitBar').classList.remove('show');
}

/* ── ประมวลผลรหัสที่ได้รับ ── */
function serveBarcodeCode(raw) {
  const digits = raw.replace(/\D/g, '');
  if (!digits) return;
  const pid = parseInt(digits, 10);
  if (!pid) return;

  const now = Date.now();
  if (pid === lastScanPid && now - lastScanTime < 1500) return;
  lastScanPid  = pid;
  lastScanTime = now;

  if (!STAFF_ID) { toast('⚠️ กรุณาล็อกอินก่อน', true); return; }

  // หาแถวที่ยังไม่ได้เสิร์ฟ — priority: set header → standalone/sub-item
  let match = rawRows.find(r => parseInt(r.ProcessID) === pid && r.ServeStatus == 0 && r.ProductSetType == 7);
  if (!match) match = rawRows.find(r => parseInt(r.ProcessID) === pid && r.ServeStatus == 0);

  if (match) {
    tapItem(rowKey(match));
    if (cameraStream) {
      document.getElementById('scanHintBar').textContent = '✅ #' + String(pid).padStart(6, '0');
      setTimeout(() => { document.getElementById('scanHintBar').textContent = 'จ่อกล้องไปที่บาร์โค้ด'; }, 1800);
    }
    return;
  }

  const already = rawRows.find(r => parseInt(r.ProcessID) === pid);
  toast(already ? 'ℹ️ #' + String(pid).padStart(BARCODE_DIGITS_DISPLAY,'0') + ' เสิร์ฟแล้ว' : '❌ ไม่พบ #' + String(pid).padStart(BARCODE_DIGITS_DISPLAY,'0'), !already);
}

/* ── camera ── */
async function toggleCamera() {
  if (cameraStream) { stopCamera(); return; }
  await startCamera();
}

async function startCamera() {
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
    const video  = document.getElementById('scanVideo');
    video.srcObject = cameraStream;
    document.getElementById('scanOverlay').classList.remove('hidden');
    const btn = document.getElementById('scanCamBtn');
    btn.classList.add('active');
    btn.textContent = '⏹ ปิดกล้อง';
    await loadJsQR();
    cameraAnim = requestAnimationFrame(scanFrame); // Bug fix: เก็บ ID frame แรก
  } catch(e) {
    toast('❌ เปิดกล้องไม่ได้: ' + e.message, true);
    cameraStream = null;
  }
}

function stopCamera() {
  if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; }
  if (cameraAnim)   { cancelAnimationFrame(cameraAnim); cameraAnim = null; }
  isDetecting = false;
  document.getElementById('scanOverlay').classList.add('hidden');
  const btn = document.getElementById('scanCamBtn');
  btn.classList.remove('active');
  btn.textContent = '📷 กล้อง';
}

async function scanFrame() {
  if (!cameraStream) return;
  const video = document.getElementById('scanVideo');

  if (video.readyState >= 2 && !camCooldown && !isDetecting) { // Bug fix: guard isDetecting
    isDetecting = true;
    let code = null;

    if ('BarcodeDetector' in window) {
      try {
        const bd = new BarcodeDetector({ formats: ['code_128','code_39','ean_13','ean_8','qr_code','upc_a','itf'] });
        const results = await bd.detect(video);
        if (results.length) code = results[0].rawValue;
      } catch(_) {}
    }

    if (!code && window.jsQR) {
      try {
        const c = document.createElement('canvas');
        c.width = video.videoWidth || 640; c.height = video.videoHeight || 480;
        c.getContext('2d').drawImage(video, 0, 0, c.width, c.height);
        const d = c.getContext('2d').getImageData(0, 0, c.width, c.height);
        const r = jsQR(d.data, d.width, d.height);
        if (r) code = r.data;
      } catch(_) {}
    }

    if (code) {
      camCooldown = true;
      const flash = document.getElementById('scanFlash');
      flash.classList.add('show');
      setTimeout(() => flash.classList.remove('show'), 180);
      serveBarcodeCode(code);
      setTimeout(() => { camCooldown = false; }, 1500);
    }
    isDetecting = false; // Bug fix: reset ทุกครั้งหลัง detect เสร็จ
  }

  if (cameraStream) cameraAnim = requestAnimationFrame(scanFrame); // Bug fix: schedule ต่อเฉพาะยังเปิดอยู่
}

async function loadJsQR() {
  if (jsQRLoaded || window.jsQR) return;
  return new Promise(resolve => {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
    s.onload  = () => { jsQRLoaded = true; resolve(); };
    s.onerror = () => resolve();
    document.head.appendChild(s);
  });
}

/* ============================================================
   SETTINGS MODAL
============================================================ */
let _logoTaps = 0, _logoTimer = null;
document.getElementById('hdrLogo').addEventListener('click', () => {
  _logoTaps++;
  clearTimeout(_logoTimer);
  if (_logoTaps >= 3) { _logoTaps = 0; openSettings(); return; }
  _logoTimer = setTimeout(() => { _logoTaps = 0; }, 700);
});

async function openSettings() {
  const modal = document.getElementById('settingsModal');
  const msg   = document.getElementById('sMsg');
  msg.className = 's-msg';
  msg.textContent = '';
  document.getElementById('sSaveBtn').disabled = false;
  modal.classList.add('show');

  try {
    const res  = await fetch(`${API}?action=get_settings`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);

    document.getElementById('s-db-host').value      = json.db_host  || '';
    document.getElementById('s-db-port').value      = json.db_port  || 3306;
    document.getElementById('s-db-name').value      = json.db_name  || '';
    document.getElementById('s-db-user').value      = json.db_user  || '';
    document.getElementById('s-db-pass').value      = '';
    document.getElementById('s-computer-id').value  = json.current_computer_id   || '';
    document.getElementById('s-computer-name').value= json.current_computer_name || '';

    const sel = document.getElementById('s-refresh');
    sel.value = String(refreshSec);
    if (!sel.value) sel.value = '30';

    document.getElementById('s-sound').checked   = soundEnabled;
    document.getElementById('s-barcode').checked = barcodeEnabled;
    document.getElementById('sVerBadge').textContent = json.version || 'v1.1.0';

    const grid = document.getElementById('sInfoGrid');
    grid.innerHTML = [
      ['เวอร์ชั่น', json.version || '—'],
      ['Computer ID (active)', json.current_computer_id || '—'],
      ['ชื่อสถานี (active)', json.current_computer_name || '—'],
      ['พนักงานที่ login', STAFF_NAME || '—'],
    ].map(([k,v]) => `<span class="s-info-key">${esc(k)}</span><span class="s-info-val">${esc(String(v))}</span>`).join('');

  } catch(e) {
    msg.textContent = '⚠️ โหลดการตั้งค่าไม่ได้: ' + e.message;
    msg.className = 's-msg err';
  }
}

function closeSettings() {
  document.getElementById('settingsModal').classList.remove('show');
}


async function saveSettings() {
  const btn = document.getElementById('sSaveBtn');
  const msg = document.getElementById('sMsg');
  btn.disabled = true;
  msg.className = 's-msg';

  // บันทึก localStorage ทันที (refresh + sound)
  const newRefresh = parseInt(document.getElementById('s-refresh').value, 10) || 30;
  const newSound   = document.getElementById('s-sound').checked;
  const newBarcode = document.getElementById('s-barcode').checked;
  localStorage.setItem('waiter_refresh_sec', String(newRefresh));
  localStorage.setItem('waiter_sound',   newSound   ? '1' : '0');
  localStorage.setItem('waiter_barcode', newBarcode ? '1' : '0');
  refreshSec     = newRefresh;
  soundEnabled   = newSound;
  barcodeEnabled = newBarcode;
  applyBarcodeEnabled();

  // บันทึก server settings
  try {
    const fd = new FormData();
    fd.append('action',               'save_settings');
    fd.append('db_host',              document.getElementById('s-db-host').value.trim());
    fd.append('db_port',              document.getElementById('s-db-port').value.trim());
    fd.append('db_name',              document.getElementById('s-db-name').value.trim());
    fd.append('db_user',              document.getElementById('s-db-user').value.trim());
    fd.append('db_pass',              document.getElementById('s-db-pass').value);
    fd.append('current_computer_id',  document.getElementById('s-computer-id').value.trim());
    fd.append('current_computer_name',document.getElementById('s-computer-name').value.trim());
    fd.append('sound_enabled',        newSound ? '1' : '0');
    const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    msg.textContent = '✅ บันทึกสำเร็จ — การตั้งค่าจะมีผลเมื่อรีโหลดหน้า';
    msg.className = 's-msg ok';
  } catch(e) {
    msg.textContent = '⚠️ บันทึกไม่สำเร็จ: ' + e.message;
    msg.className = 's-msg err';
  } finally {
    btn.disabled = false;
  }
}

// ปิด settings เมื่อคลิก backdrop
document.getElementById('settingsModal').addEventListener('click', e => {
  if (e.target === document.getElementById('settingsModal')) closeSettings();
});

/* START */
applyBarcodeEnabled();
document.getElementById('loginInput').addEventListener('keydown', e => {
  if (e.key === 'Enter') doLogin();
});
initAuth();
</script>

    <script>
    (function(){
        var btn = document.getElementById("fsBtn");
        if(!btn) return;
        function updateIcon(){
            var full = !!document.fullscreenElement;
            btn.querySelector(".fs-ico-enter").style.display = full ? "none" : "inline";
            btn.querySelector(".fs-ico-exit").style.display  = full ? "inline" : "none";
            btn.title = full ? "ออกจากเต็มจอ" : "เต็มจอ";
        }
        btn.addEventListener("click", function(){
            if(!document.fullscreenElement){
                document.documentElement.requestFullscreen().catch(function(){});
            } else {
                document.exitFullscreen().catch(function(){});
            }
        });
        document.addEventListener("fullscreenchange", updateIcon);
        updateIcon();
    })();
    </script>
</body>
</html>

<?php require_once __DIR__ . '/config.php'; require_once __DIR__ . '/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>หน้าเสิร์ฟ — Waiter Display</title>
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
.sum-bar{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:8px 12px;max-width:1920px;margin:0 auto}
.sum-item{
    background:rgba(255,255,255,.88);border:1px solid rgba(255,255,255,.72);
    border-radius:14px;padding:8px 12px;text-align:center;box-shadow:var(--shadow);
}
.sum-n{font-size:22px;font-weight:700;line-height:1}
.sum-l{font-size:10px;color:var(--muted);margin-top:3px;font-weight:700}
.s-wait .sum-n{color:var(--warning)}
.s-rdy .sum-n{color:var(--primary)}
.s-done .sum-n{color:var(--success)}

/* FILTER BAR */
.fbar{display:flex;gap:6px;padding:6px 12px 8px;overflow-x:auto;scrollbar-width:none;max-width:1920px;margin:0 auto}
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
.content{padding:8px 12px 16px;max-width:1920px;margin:0 auto;display:flex;flex-direction:column;gap:8px}

.sec-lbl{
    font-size:10px;font-weight:700;color:var(--muted);
    letter-spacing:.08em;text-transform:uppercase;
    padding:2px 0 6px;display:flex;align-items:center;gap:8px;
}
.sec-lbl::after{content:'';flex:1;height:1px;background:var(--line)}

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

/* PROGRESS */
.prog-wrap{padding:8px 14px 10px}
.prog-track{height:4px;background:var(--line);border-radius:2px;overflow:hidden}
.prog-fill{height:100%;background:var(--success);border-radius:2px;transition:width .3s ease}
.prog-lbl{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-top:4px;font-weight:700}
.prog-lbl .pc{color:var(--success)}

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
.srv-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}

/* EMPTY */
.empty{text-align:center;padding:64px 20px;color:var(--muted)}
.empty .ico{font-size:48px;margin-bottom:16px}
.empty h3{font-size:16px;color:var(--text);font-weight:700}
.empty p{font-size:12px;margin-top:6px;line-height:1.7}

/* LOADING */
.loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;gap:14px;color:var(--muted)}
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
</style>
</head>
<body>

<!-- LOGIN OVERLAY -->
<div class="login-overlay" id="loginOverlay">
  <div class="login-box">
    <div class="login-icon">🍽️</div>
    <div class="login-title">Waiter Display</div>
    <div class="login-sub">กรอกรหัสพนักงานเพื่อเข้าใช้งาน</div>
    <input class="login-input" id="loginInput" type="text" placeholder="รหัสพนักงาน" autocomplete="off" maxlength="20">
    <button class="login-btn" id="loginBtn" onclick="doLogin()">เข้าสู่ระบบ</button>
    <div class="login-err" id="loginErr"></div>
  </div>
</div>

<!-- HEADER -->
<div class="hdr">
  <div class="hdr-l">
    <div class="hdr-icon">🍽️</div>
    <div>
      <div class="hdr-title">เสิร์ฟอาหาร</div>
      <div class="hdr-sub">Waiter Display</div>
    </div>
  </div>
  <div class="hdr-r">
    <div class="live"></div>
    <div class="clock" id="clock">--:--</div>
    <div class="staff-info" id="staffInfo" style="display:none">
      <span class="staff-name" id="staffNameDisplay"></span>
      <button class="btn-logout" onclick="doLogout()">ออก</button>
    </div>
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

<!-- SUMMARY -->
<div class="sum-bar">
  <div class="sum-item s-wait"><div class="sum-n" id="sn-wait">-</div><div class="sum-l">โต๊ะรอเสิร์ฟ</div></div>
  <div class="sum-item s-rdy"><div class="sum-n" id="sn-items">-</div><div class="sum-l">รายการค้าง</div></div>
  <div class="sum-item s-done"><div class="sum-n" id="sn-done">-</div><div class="sum-l">เสิร์ฟแล้ว</div></div>
</div>

<!-- FILTER -->
<div class="fbar">
  <button class="fbtn on" id="fb-wait" onclick="setFilter('wait')">⏳ รอเสิร์ฟ <span class="cnt" id="fc-wait">-</span></button>
  <button class="fbtn"    id="fb-done" onclick="setFilter('done')">✅ เสิร์ฟแล้ว <span class="cnt" id="fc-done">-</span></button>
</div>

<div class="err-banner" id="errBanner"></div>
<div class="content" id="main">
  <div class="loading"><div class="spinner"></div><span>กำลังโหลด...</span></div>
</div>
<div class="toast" id="toast"></div>

<script>
/* ============================================================
   CONFIG
   StaffID ควร inject จาก session PHP จริง
   เช่น: const STAFF_ID = <?= $_SESSION['staff_id'] ?? 0 ?>;
============================================================ */
const API        = 'api_waiter.php';
const REFRESH_SEC = 30;
let   STAFF_ID    = 0;

/* ── state ── */
let tables   = [];
let rawRows  = [];
let filter   = 'wait';
let timer    = null;
const pending = new Set(); // rowKey ที่กำลัง POST อยู่

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

    rawRows = json.rows;
    tables  = groupByTable(rawRows);
    hideError();

  } catch (e) {
    showError('โหลดข้อมูลไม่ได้: ' + e.message);
  } finally {
    btn.classList.remove('spin');
    render();
    timer = setTimeout(loadData, REFRESH_SEC * 1000);
  }
}

/* ── group rows by TableID ── */
function groupByTable(rows) {
  const map = {};
  rows.forEach(r => {
    const k = r.TableID;
    if (!map[k]) map[k] = {
      tableId:   r.TableID,
      tableName: r.DisplayTableName || String(r.TableID),
      earliest:  r.FinishDateTime || r.SubmitOrderDateTime,
      rows: []
    };
    // ใช้ชื่อโต๊ะล่าสุด (รองรับโต๊ะโอน A3->A5)
    if (r.FinishDateTime && r.FinishDateTime > map[k].earliest) {
      map[k].tableName = r.DisplayTableName || String(r.TableID);
      map[k].earliest  = r.FinishDateTime;
    }
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
  // summary counts
  let nWait = 0, nItems = 0, nDone = 0;
  tables.forEach(t => {
    const d = t.rows.filter(r => r.ServeStatus == 1).length;
    if (d < t.rows.length) nWait++;
    nItems += (t.rows.length - d);
    nDone  += d;
  });
  document.getElementById('sn-wait').textContent  = nWait;
  document.getElementById('sn-items').textContent = nItems;
  document.getElementById('sn-done').textContent  = nDone;

  const nW = tables.filter(t => t.rows.some(r  => r.ServeStatus == 0)).length;
  const nD = tables.filter(t => t.rows.every(r => r.ServeStatus == 1)).length;
  document.getElementById('fc-wait').textContent = nW;
  document.getElementById('fc-done').textContent = nD;

  // filter
  let shown = tables.filter(t => t.rows.some(r => r.ServeStatus == 0));
  if (filter === 'done') shown = tables.filter(t => t.rows.every(r => r.ServeStatus == 1));

  const el = document.getElementById('main');
  if (!shown.length) {
    const emptyMsg = filter === 'done'
      ? { ico: '📋', h: 'ยังไม่มีโต๊ะที่เสิร์ฟครบ', p: '' }
      : { ico: '🎉', h: 'เสิร์ฟครบทุกโต๊ะแล้ว!', p: 'ไม่มีรายการค้าง 👍' };
    el.innerHTML = `<div class="empty"><div class="ico">${emptyMsg.ico}</div><h3>${emptyMsg.h}</h3><p>${emptyMsg.p}</p></div>`;
    return;
  }
  let html = '';
  shown.forEach(t => html += buildCard(t, filter === 'done'));
  el.innerHTML = html;
}

/* ── build card HTML ── */
function buildCard(t, allowUnserve = false) {
  const total   = t.rows.length;
  const served  = t.rows.filter(r => r.ServeStatus == 1).length;
  const allDone = served === total;
  const pct     = total ? Math.round(served / total * 100) : 0;

  const cls  = allDone ? 'c-done' : served > 0 ? 'c-part' : 'c-rdy';
  const pill = allDone
    ? `<span class="pill p-done">✅ เสิร์ฟครบ</span>`
    : served > 0
      ? `<span class="pill p-part">⏳ ${served}/${total}</span>`
      : `<span class="pill p-rdy">🟢 รอเสิร์ฟ</span>`;

  const t0  = new Date(t.earliest.replace(' ', 'T'));
  const ts  = `${pad(t0.getHours())}:${pad(t0.getMinutes())}`;

  const items = t.rows.map(r => {
    const srv = r.ServeStatus == 1;
    const key = rowKey(r);
    let tag = '';
    if      (r.ProductSetType ==  7) tag = `<span class="tag set">📦 เซต</span>`;
    else if (r.ProductSetType <   0) tag = `<span class="tag sub">↳ ในเซต</span>`;
    else if (r.ProductSetType == 15) tag = `<span class="tag add">➕ Add-on</span>`;

    const tappable = !srv || allowUnserve;
    const onclick  = tappable
      ? (srv ? `onclick="confirmUnserve('${key}')"` : `onclick="tapItem('${key}')"`)
      : '';
    return `<div class="irow${srv ? ' served' : ''}${!tappable ? ' locked' : ''}" data-key="${key}" ${onclick}>
      <div class="chk">
        <svg class="chk-ico" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div class="i-info">
        <div class="i-name">${esc(r.ProductName)}</div>
        ${tag ? `<div class="i-tags">${tag}</div>` : ''}
      </div>
      <div class="i-qty">×${parseFloat(r.ProductAmount)}</div>
    </div>`;
  }).join('');

  const btn = allDone
    ? `<button class="srv-btn done-btn" disabled>✅ เสิร์ฟครบแล้ว</button>`
    : `<button class="srv-btn" onclick="tapServeAll(${t.tableId})">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        เสิร์ฟครบโต๊ะ ${t.tableName}
      </button>`;

  return `<div class="card ${cls}">
    <div class="c-hdr">
      <div class="tbl-badge">
        <div class="tbl-num">T${esc(t.tableName)}</div>
        <div class="tbl-name">โต๊ะ ${esc(t.tableName)}</div>
      </div>
      ${pill}
    </div>
    <div class="c-meta">
      <div class="m-item">🕐 <b>${ts}</b></div>
      <div class="m-item">🍽️ <b>${total}</b> รายการ</div>
      <div class="m-item" style="color:var(--grn)">✅ <b>${served}/${total}</b></div>
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
async function tapItem(key) {
  if (pending.has(key)) return;
  const r = rawRows.find(r => rowKey(r) === key);
  if (!r) return;

  const wasServed = r.ServeStatus == 1;
  const action    = wasServed ? 'unserve_item' : 'serve_item';
  pending.add(key);

  // Optimistic UI update
  r.ServeStatus = wasServed ? 0 : 1;
  if (!wasServed) {
    // ถ้า toggle parent set → toggle ลูกในเซตด้วย
    if (r.ProductSetType == 7) {
      rawRows.filter(c => c.ParentProcessID == r.ProcessID && c.TableID == r.TableID)
             .forEach(c => c.ServeStatus = 1);
    }
  }
  tables = groupByTable(rawRows);
  render();
  toast(wasServed ? '↩️ ยกเลิกติ๊ก' : '✅ ติ๊กเสิร์ฟแล้ว');

  // POST to API
  try {
    const fd = new FormData();
    fd.append('action',         action);
    fd.append('ProductLevelID', r.ProductLevelID);
    fd.append('ProcessID',      r.ProcessID);
    fd.append('SubProcessID',   r.SubProcessID);
    fd.append('PrinterID',      r.PrinterID);
    fd.append('StaffID',        STAFF_ID);
    const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
  } catch (e) {
    // Rollback
    r.ServeStatus = wasServed ? 1 : 0;
    tables = groupByTable(rawRows);
    render();
    toast('⚠️ บันทึกไม่สำเร็จ', true);
  } finally {
    pending.delete(key);
  }
}

async function tapServeAll(tableId) {
  const t = tables.find(t => t.tableId == tableId);
  if (!t) return;

  // Optimistic
  t.rows.forEach(r => r.ServeStatus = 1);
  tables = groupByTable(rawRows);
  render();
  toast(`✅ เสิร์ฟครบโต๊ะ ${t.tableName} แล้ว!`);

  try {
    const fd = new FormData();
    fd.append('action',  'serve_table');
    fd.append('TableID', tableId);
    fd.append('StaffID', STAFF_ID);
    const res  = await fetch(API, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
  } catch (e) {
    // Reload from server on failure
    await loadData();
    toast('⚠️ บันทึกไม่สำเร็จ กำลังโหลดใหม่', true);
  }
}

/* ── filter ── */
function setFilter(f) {
  filter = f;
  ['wait', 'done'].forEach(x =>
    document.getElementById('fb-' + x).classList.toggle('on', x === f)
  );
  render();
}

/* ── confirm before unserve ── */
async function confirmUnserve(key) {
  if (!confirm('ยกเลิกการเสิร์ฟรายการนี้?')) return;
  await tapItem(key);
}

/* ============================================================
   HELPERS
============================================================ */
function rowKey(r) {
  return `${r.ProductLevelID}_${r.ProcessID}_${r.SubProcessID}_${r.PrinterID}`;
}
function pad(n)  { return String(n).padStart(2, '0'); }
function esc(s)  { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

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
  STAFF_ID = id;
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
    const res  = await fetch(`${API}?action=lookup_staff&staff_code=${encodeURIComponent(code)}`);
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

/* START */
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

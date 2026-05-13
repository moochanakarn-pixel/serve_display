<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>คู่มือการใช้งาน — Serve Display</title>
<style>
:root{
    --primary:#1683ff;--primary-deep:#0a3a70;
    --success:#12a150;--warning:#d97706;--danger:#e44c3a;
    --bg:#f0f6ff;--surface:#fff;--text:#122033;--muted:#6b7a90;
    --line:#dbe8f7;--radius:16px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6}
a{color:var(--primary);text-decoration:none}

/* HEADER */
.hdr{background:linear-gradient(135deg,#0a3a70,#1683ff,#ff8a1f);color:#fff;padding:20px 24px}
.hdr h1{font-size:22px;font-weight:800}
.hdr p{font-size:13px;opacity:.85;margin-top:4px}
.version{display:inline-block;background:rgba(255,255,255,.2);border-radius:999px;padding:2px 10px;font-size:11px;font-weight:700;margin-top:8px}

/* LAYOUT */
.wrap{max-width:900px;margin:0 auto;padding:20px 16px 60px}

/* NAV */
.nav{background:var(--surface);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;border:1px solid var(--line)}
.nav h3{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px}
.nav-list{display:flex;flex-wrap:wrap;gap:8px}
.nav-list a{background:var(--bg);border:1px solid var(--line);border-radius:999px;padding:5px 14px;font-size:12px;font-weight:700;color:var(--primary)}
.nav-list a:hover{background:var(--primary);color:#fff;border-color:var(--primary)}

/* SECTION */
.sec{background:var(--surface);border-radius:var(--radius);padding:24px;margin-bottom:16px;border:1px solid var(--line)}
.sec-title{font-size:17px;font-weight:800;color:var(--primary-deep);margin-bottom:16px;display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:2px solid var(--line)}
.sec-title .ico{font-size:20px}
h3{font-size:14px;font-weight:800;color:var(--text);margin:18px 0 8px}
h3:first-child{margin-top:0}
p{font-size:13px;color:var(--muted);margin-bottom:10px}
p:last-child{margin-bottom:0}

/* TABLE */
table{width:100%;border-collapse:collapse;font-size:13px;margin:10px 0}
th{background:var(--bg);padding:9px 12px;text-align:left;font-weight:700;color:var(--primary-deep);border-bottom:2px solid var(--line)}
td{padding:9px 12px;border-bottom:1px solid var(--line);color:var(--text)}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f8fbff}

/* CODE */
pre{background:#1e293b;color:#e2e8f0;border-radius:10px;padding:16px;font-size:12px;overflow-x:auto;margin:10px 0;line-height:1.6}
code{font-family:monospace}
.inline-code{background:#eef3ff;color:#1758a5;border-radius:4px;padding:1px 6px;font-size:12px;font-family:monospace}

/* STEP */
.steps{display:flex;flex-direction:column;gap:12px;margin:10px 0}
.step{display:flex;gap:12px;align-items:flex-start}
.step-num{width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px}
.step-body{flex:1}
.step-title{font-size:13px;font-weight:700;color:var(--text)}
.step-desc{font-size:12px;color:var(--muted);margin-top:3px}

/* BADGE */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700}
.b-wait{background:#fffbeb;color:#d97706;border:1px solid #fcd34d}
.b-done{background:#e6f8ee;color:#12a150;border:1px solid #bfeacc}
.b-cook{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa}
.b-info{background:#eef6ff;color:#1758a5;border:1px solid #d5e7ff}

/* ALERT */
.alert{border-radius:10px;padding:12px 16px;font-size:13px;margin:10px 0;display:flex;gap:10px}
.alert-warn{background:#fffbeb;border:1px solid #fcd34d;color:#92400e}
.alert-info{background:#eef6ff;border:1px solid #d5e7ff;color:#1e40af}
.alert-ico{font-size:16px;flex-shrink:0;margin-top:1px}

/* UI PREVIEW */
.ui-card{border:1.5px solid var(--line);border-radius:12px;overflow:hidden;margin:12px 0;font-size:12px}
.ui-hdr{background:linear-gradient(180deg,#fff,#f8fbff);border-bottom:1px solid var(--line);padding:10px 14px;display:flex;align-items:center;justify-content:space-between}
.ui-tbl{background:linear-gradient(135deg,#0a3a70,#1683ff);color:#fff;font-weight:800;border-radius:6px;padding:3px 8px;font-size:12px}
.ui-row{padding:10px 14px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px;font-size:12px}
.ui-row:last-child{border-bottom:none}
.ui-chk{width:22px;height:22px;border-radius:6px;border:2px solid #dbe8f7;flex-shrink:0}
.ui-chk.done{background:#12a150;border-color:#12a150;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px}
.ui-name{flex:1;font-weight:700}
.ui-name.done{text-decoration:line-through;color:#6b7a90}
.ui-qty{background:#fff1e4;border:1px solid #ffd8b0;border-radius:6px;padding:2px 8px;font-weight:700;color:#9a5200;font-size:11px}

@media(max-width:600px){
    .hdr{padding:16px}
    .wrap{padding:12px 10px 40px}
    .sec{padding:16px}
}
</style>
</head>
<body>

<div class="hdr">
  <h1>📋 คู่มือการใช้งาน</h1>
  <p>Serve Display — หน้าจอช่วยเสิร์ฟอาหาร</p>
  <span class="version">v1.0.0-beta</span>
</div>

<div class="wrap">

  <!-- NAV -->
  <div class="nav">
    <h3>เนื้อหา</h3>
    <div class="nav-list">
      <a href="#login">การ Login</a>
      <a href="#tabs">หน้าจอหลัก</a>
      <a href="#serve">การเสิร์ฟ</a>
      <a href="#search">ค้นหาโต๊ะ</a>
    </div>
  </div>

  <!-- LOGIN -->
  <div class="sec" id="login">
    <div class="sec-title"><span class="ico">🔐</span> การ Login</div>

    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <div class="step-title">กรอกรหัสพนักงาน (StaffCode) ในช่องที่ปรากฏ</div>
          <div class="step-desc">รหัสที่ใช้ต้องตรงกับในตาราง staffs และต้องเป็น Activated=1, Deleted=0</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-body">
          <div class="step-title">กด "เข้าสู่ระบบ" หรือ Enter</div>
          <div class="step-desc">ระบบจะจำ login ไว้ในเครื่องนี้ ไม่ต้อง login ซ้ำเมื่อเปิดหน้าใหม่</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-body">
          <div class="step-title">กดปุ่ม "ออก" ที่มุมบนขวาเพื่อเปลี่ยนพนักงาน</div>
        </div>
      </div>
    </div>

    <div class="alert alert-warn">
      <span class="alert-ico">⚠️</span>
      <div>ถ้า login หายกะทันหัน อาจเกิดจาก browser ล้าง cache — ให้ login ใหม่ได้เลย</div>
    </div>
  </div>

  <!-- TABS -->
  <div class="sec" id="tabs">
    <div class="sec-title"><span class="ico">📱</span> หน้าจอหลัก</div>

    <h3>แถบกรอง (Filter Tabs)</h3>

    <div class="sec" style="background:#fffbeb;border-color:#fcd34d;margin-bottom:0;border-radius:12px 12px 0 0;padding:16px 20px">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span class="badge b-wait" style="font-size:13px">⏳ รอเสิร์ฟ</span>
        <span style="font-size:11px;font-weight:700;color:#92400e;background:#fef3c7;border-radius:999px;padding:2px 10px">หน้าหลัก</span>
      </div>
      <p style="margin:0;color:#78350f;font-size:13px">แสดงโต๊ะที่มีรายการ <strong>ออกจากครัวแล้ว แต่ยังไม่ได้เสิร์ฟครบ</strong> — พนักงานกดติ๊กรายการที่เสิร์ฟไปแล้วได้ที่นี่ โต๊ะที่เสิร์ฟครบทุกรายการจะหายออกไปเองและย้ายไปแถบ "เสิร์ฟแล้ว" อัตโนมัติ</p>
    </div>
    <div class="sec" style="background:#e6f8ee;border-color:#bfeacc;margin-bottom:0;border-radius:0;padding:16px 20px;border-top:none">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span class="badge b-done" style="font-size:13px">✅ เสิร์ฟแล้ว</span>
      </div>
      <p style="margin:0;color:#14532d;font-size:13px">แสดงโต๊ะที่ <strong>เสิร์ฟครบทุกรายการ</strong> แล้ว — ใช้ตรวจสอบย้อนหลังว่าใครเสิร์ฟรายการไหน เมื่อไหร่ ถ้าติ๊กผิดสามารถกดยกเลิกเสิร์ฟได้จากหน้านี้ทั้งทีละรายการหรือทั้งโต๊ะ</p>
    </div>
    <div class="sec" style="background:#fff7ed;border-color:#fed7aa;margin-bottom:16px;border-radius:0 0 12px 12px;padding:16px 20px;border-top:none">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span class="badge b-cook" style="font-size:13px">🍳 อยู่ในครัว</span>
        <span style="font-size:11px;font-weight:700;color:#9a3412;background:#ffedd5;border-radius:999px;padding:2px 10px">ดูได้อย่างเดียว</span>
      </div>
      <p style="margin:0;color:#7c2d12;font-size:13px">แสดงรายการที่ <strong>ยังอยู่ในครัว ยังไม่ออกมา</strong> — แก้ไขอะไรไม่ได้ ใช้เช็คว่าโต๊ะไหนยังรอของจากครัวอยู่ ไม่ต้องวิ่งไปถามพ่อครัว</p>
    </div>

    <div style="background:#f0f6ff;border:1px solid var(--line);border-radius:10px;padding:12px 16px;font-size:12px;color:var(--muted);margin-bottom:16px">
      <strong style="color:var(--text)">ลำดับการไหลของออเดอร์</strong><br>
      <span style="font-size:13px">🍳 อยู่ในครัว &nbsp;→&nbsp; ⏳ รอเสิร์ฟ &nbsp;→&nbsp; ✅ เสิร์ฟแล้ว</span>
    </div>

    <h3>การ์ดโต๊ะ</h3>
    <div class="ui-card">
      <div class="ui-hdr">
        <div style="display:flex;align-items:center;gap:8px">
          <span class="ui-tbl">A5</span>
          <span style="font-weight:700;font-size:13px;color:#0a3a70">โต๊ะ A5</span>
        </div>
        <span class="badge b-wait">⏳ 1/3</span>
      </div>
      <div style="padding:6px 14px;background:#f8fbff;border-bottom:1px solid #dbe8f7;font-size:11px;color:#6b7a90;display:flex;gap:12px">
        <span>🕐 <b style="color:#122033">12:35</b></span>
        <span>🍽️ <b style="color:#122033">3</b> รายการ</span>
        <span style="color:#12a150">✅ <b>1/3</b></span>
        <span style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;border-radius:999px;padding:1px 8px;font-size:10px;font-weight:700">🍳 ยังทำอยู่ 2</span>
      </div>
      <div class="ui-row">
        <div class="ui-chk done">✓</div>
        <div class="ui-name done">หมูกระทะชุด A</div>
        <span style="background:#e6f8ee;color:#12a150;border:1px solid #bfeacc;border-radius:6px;padding:2px 7px;font-size:10px;font-weight:700">👤 สมชาย</span>
        <span style="font-size:10px;color:#6b7a90;background:#f8fbff;border:1px solid #dbe8f7;border-radius:6px;padding:2px 6px">🕐 12:33</span>
        <span class="ui-qty">×1</span>
      </div>
      <div class="ui-row">
        <div class="ui-chk"></div>
        <div class="ui-name">ข้าวสวย</div>
        <span class="ui-qty">×2</span>
      </div>
      <div class="ui-row">
        <div class="ui-chk"></div>
        <div class="ui-name">น้ำจิ้ม</div>
        <span class="ui-qty">×1</span>
      </div>
    </div>

    <table style="margin-top:8px">
      <tr><th>สัญลักษณ์</th><th>ความหมาย</th></tr>
      <tr><td>🕐 เวลาบนการ์ด</td><td>เวลาที่ครัว checkout ออเดอร์นี้เร็วสุด</td></tr>
      <tr><td>🕐 เวลาข้างรายการ</td><td>เวลาที่ครัว checkout รายการนั้นโดยเฉพาะ</td></tr>
      <tr><td>👤 ชื่อสีเขียว</td><td>พนักงานที่กดเสิร์ฟรายการนั้น</td></tr>
      <tr><td>🍳 ยังทำอยู่ N</td><td>มีรายการของโต๊ะนี้ที่ยังค้างในครัว</td></tr>
      <tr><td>📦 หัวเซ็ต (พื้นสีน้ำเงิน)</td><td>คั่นแต่ละรอบการสั่ง กดติ๊กได้</td></tr>
    </table>
  </div>

  <!-- SERVE -->
  <div class="sec" id="serve">
    <div class="sec-title"><span class="ico">✅</span> การเสิร์ฟและยกเลิก</div>

    <h3>เสิร์ฟทีละรายการ</h3>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <div class="step-title">กดที่แถวรายการที่ต้องการ</div>
          <div class="step-desc">รายการจะเปลี่ยนเป็นสีจาง มีเครื่องหมายถูก และบันทึกชื่อผู้เสิร์ฟทันที</div>
        </div>
      </div>
    </div>

    <h3>เสิร์ฟครบโต๊ะในปุ่มเดียว</h3>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <div class="step-title">กดปุ่มสีเขียว "เสิร์ฟครบโต๊ะ X" ด้านล่างการ์ด</div>
          <div class="step-desc">ทุกรายการที่ยังไม่เสิร์ฟจะถูกติ๊กพร้อมกัน</div>
        </div>
      </div>
    </div>

    <h3>ยกเลิกการเสิร์ฟ</h3>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <div class="step-title">กดที่รายการที่เสิร์ฟแล้ว (สีจาง) เพื่อยกเลิกทีละรายการ</div>
          <div class="step-desc">จะมี popup ยืนยันก่อน</div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-body">
          <div class="step-title">หรือไปที่แถบ "เสิร์ฟแล้ว" แล้วกดปุ่ม "ยกเลิกเสิร์ฟทั้งโต๊ะ"</div>
        </div>
      </div>
    </div>

    <div class="alert alert-info">
      <span class="alert-ico">ℹ️</span>
      <div>ข้อมูลรีเฟรชอัตโนมัติทุก <strong>30 วินาที</strong> หรือกดปุ่ม <strong>รีเฟรช</strong> ที่มุมบนขวาได้ทุกเมื่อ</div>
    </div>
  </div>

  <!-- SEARCH -->
  <div class="sec" id="search">
    <div class="sec-title"><span class="ico">🔍</span> ค้นหาและนำทางโต๊ะ</div>

    <h3>ช่องค้นหา</h3>
    <p>พิมพ์ชื่อหรือเลขโต๊ะในช่อง "ค้นหาโต๊ะ..." — การ์ดจะกรองแบบ real-time ทันที</p>

    <h3>Chip โต๊ะ</h3>
    <p>แถวปุ่มเล็ก ๆ ด้านขวาของช่องค้นหา — กดชื่อโต๊ะเพื่อ scroll ไปหาการ์ดนั้นทันที พร้อมกรอบไฮไลต์สีน้ำเงิน</p>
    <table>
      <tr><th>สีของ chip</th><th>ความหมาย</th></tr>
      <tr><td style="color:#12a150;font-weight:700">สีเขียว</td><td>รอเสิร์ฟทุกรายการ</td></tr>
      <tr><td style="color:#d97706;font-weight:700">สีเหลือง</td><td>เสิร์ฟบางส่วนแล้ว</td></tr>
      <tr><td style="color:#6b7a90;font-weight:700">สีเทา</td><td>เสิร์ฟครบแล้ว</td></tr>
    </table>
  </div>

  <div style="text-align:center;font-size:11px;color:var(--muted);margin-top:8px">
    Serve Display v1.0.0-beta &nbsp;|&nbsp; <a href="waiter_display.php">→ เปิดหน้าเสิร์ฟ</a>
  </div>

</div>
</body>
</html>

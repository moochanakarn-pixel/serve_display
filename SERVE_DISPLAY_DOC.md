# Serve Display — คู่มือการใช้งาน

> เอกสารนี้อธิบายการทำงาน วิธีใช้ และหลักการของหน้า **Serve Display (waiter_display.php)**  
> เวอร์ชัน: **1.0.0** · อัปเดต: 2026-05-10

---

## Changelog

| เวอร์ชัน | วันที่ | ไฟล์ที่แก้ไข | รายละเอียด |
|---------|--------|-------------|-----------|
| 1.0.0 | 2026-05-10 | `api_waiter.php`, `waiter_display.php` | Release แรก — UI ครบ, ServeStatus บันทึก DB จริงผ่าน `ServingStaffID`/`ServingDateTime` |
| 1.0.0 | 2026-05-10 | `api_waiter.php` | เปลี่ยนจาก `kds_serve_log` มาใช้ column ใน `orderprocessdetailfront` โดยตรง |
| 1.0.0 | 2026-05-10 | `api_waiter.php` | อัปเดต `ServingDateTime` จาก `UNIX_TIMESTAMP()` เป็น `NOW()` หลัง column เปลี่ยนเป็น `DATETIME` |
| 1.0.0 | 2026-05-10 | `api_waiter.php` | เพิ่ม filter `DATE(FinishDateTime) = CURDATE()` แสดงเฉพาะวันนี้ |

---

## คำสั่ง Git

### ดึงโค้ดล่าสุดมาใช้

```bash
git pull origin claude/review-service-display-FBPB9
```

### ครั้งแรกที่ clone โปรเจกต์

```bash
git clone https://github.com/moochanakarn-pixel/serve_display.git
cd serve_display
git checkout claude/review-service-display-FBPB9
```

---

## Serve Display คืออะไร

**Serve Display** คือหน้าจอสำหรับ **พนักงานเสิร์ฟ** ใช้ติดตามว่าอาหารโต๊ะไหนออกจากครัวมาแล้ว และติ๊กยืนยันว่าเสิร์ฟให้ลูกค้าเรียบร้อยแล้ว

```
POS สั่งออเดอร์
      │
      ▼
Checker KDS (ครัว)  ← พ่อครัวทำอาหาร → กด Checkout เมื่อเสร็จ
      │
      ▼
Serve Display (หน้านี้)  ← พนักงานเสิร์ฟ → ติ๊กว่าเสิร์ฟแล้ว
      │
      ▼
ลูกค้าได้รับอาหาร ✅
```

---

## ไฟล์ในโปรเจกต์

| ไฟล์ | หน้าที่ |
|------|---------|
| `waiter_display.php` | หน้า UI หลักที่พนักงานเปิดใช้งาน |
| `api_waiter.php` | API backend รับ-ส่งข้อมูลกับ DB |
| `config.php` | ค่าคงที่และ DB connection |
| `auth_check.php` | ตรวจสอบ session |
| `settings.local.php` | ค่าเฉพาะเครื่อง (DB host, port ฯลฯ) |

---

## DB Schema ที่เกี่ยวข้อง

ระบบทำงานบนตาราง **`orderprocessdetailfront`** โดยอ่านแถวที่ `ProcessStatus = 1` (Checkout แล้วจากครัว)

```
ProcessStatus = 1  →  ออกจากครัวแล้ว รอเสิร์ฟ
ServingStaffID = 0  →  ยังไม่ได้เสิร์ฟ
ServingStaffID > 0  →  เสิร์ฟแล้ว (มี StaffID บันทึกไว้)
ServingDateTime     →  เวลาที่เสิร์ฟ (DATETIME NULL)
```

### คอลัมน์ที่เพิ่มเข้า DB

```sql
ALTER TABLE OrderProcessDetailFront
  ADD ServingStaffID INT NOT NULL DEFAULT 0 AFTER FinishDateTime;
ALTER TABLE OrderProcessDetailFront
  ADD ServingDateTime DATETIME NULL DEFAULT NULL AFTER ServingStaffID;

ALTER TABLE OrderProcessDetail
  ADD ServingStaffID INT NOT NULL DEFAULT 0 AFTER FinishDateTime;
ALTER TABLE OrderProcessDetail
  ADD ServingDateTime DATETIME NULL DEFAULT NULL AFTER ServingStaffID;
```

---

## หน้าจอและการใช้งาน

### Header
- **นาฬิกา** — แสดงเวลาปัจจุบัน Real-time
- **ปุ่มรีเฟรช** — โหลดข้อมูลใหม่ทันที
- **ปุ่มเต็มจอ** — สำหรับติดตั้งบนจอแสดงผลในร้าน

### Summary Bar (แถบสรุป 3 ช่อง)
| ช่อง | ความหมาย |
|------|---------|
| โต๊ะรอเสิร์ฟ | จำนวนโต๊ะที่มีอาหารค้างอยู่ |
| รายการค้าง | จำนวนรายการที่ยังไม่ได้เสิร์ฟ |
| เสิร์ฟแล้ว | จำนวนรายการที่เสิร์ฟแล้ววันนี้ |

### Filter Bar (แถบกรอง)
- **ทั้งหมด** — แสดงทุกโต๊ะ
- **⏳ รอเสิร์ฟ** — แสดงเฉพาะโต๊ะที่ยังค้าง
- **✅ เสิร์ฟแล้ว** — แสดงเฉพาะโต๊ะที่เสิร์ฟครบแล้ว

### การ์ดแต่ละโต๊ะ
```
┌─────────────────────────────────────┐
│  T A5   โต๊ะ A5          🟢 รอเสิร์ฟ │
│  🕐 12:30   🍽️ 3 รายการ  ✅ 1/3      │
├─────────────────────────────────────┤
│  ☑  ผัดกะเพราหมู              ×1   │  ← ติ๊กแล้ว (สีจางลง)
│  ☐  ต้มยำกุ้ง                 ×1   │  ← ยังไม่ติ๊ก
│  ☐  ข้าวสวย                   ×2   │
├─────────────────────────────────────┤
│  ████░░░░░░  1/3  33%              │
│  [ เสิร์ฟครบโต๊ะ A5 ]             │
└─────────────────────────────────────┘
```

### สีของการ์ด
| สี | ความหมาย |
|----|---------|
| ขอบเขียว | รอเสิร์ฟ (ยังไม่ติ๊กเลย) |
| ขอบเหลือง | เสิร์ฟบางส่วน |
| สีจาง | เสิร์ฟครบแล้ว |

---

## วิธีใช้งาน (พนักงานเสิร์ฟ)

### ติ๊กทีละรายการ
1. แตะที่ชื่ออาหารหรือช่อง checkbox
2. ✅ ติ๊กสำเร็จ รายการจะสีจางลง
3. แตะซ้ำเพื่อยกเลิกการติ๊ก

### เสิร์ฟครบโต๊ะ
1. กดปุ่ม **"เสิร์ฟครบโต๊ะ X"** ด้านล่างการ์ด
2. ทุกรายการในโต๊ะจะถูกติ๊กพร้อมกัน
3. การ์ดจะเปลี่ยนเป็น "✅ เสิร์ฟครบแล้ว"

### Auto-refresh
- หน้าจอโหลดข้อมูลใหม่อัตโนมัติ **ทุก 30 วินาที**
- สามารถกดปุ่ม **รีเฟรช** เพื่อโหลดทันที

---

## API Endpoints

Base URL: `api_waiter.php`

| Method | Action | หน้าที่ |
|--------|--------|---------|
| GET | `?action=list_pending` | ดึงรายการที่ออกจากครัววันนี้ พร้อมสถานะเสิร์ฟ |
| POST | `action=serve_item` | ติ๊กเสิร์ฟรายการเดียว |
| POST | `action=unserve_item` | ยกเลิกติ๊กรายการเดียว |
| POST | `action=serve_table` | ติ๊กเสิร์ฟทุกรายการในโต๊ะ |

### ตัวอย่าง Response — list_pending

```json
{
  "success": true,
  "rows": [
    {
      "ProcessID": 12345,
      "TableID": 5,
      "DisplayTableName": "A5",
      "ProductName": "ผัดกะเพราหมู",
      "ProductAmount": 1,
      "FinishDateTime": "2026-05-10 12:30:00",
      "ServingStaffID": 0,
      "ServingDateTime": null,
      "ServeStatus": 0
    }
  ]
}
```

### POST Parameters — serve_item / unserve_item

| Field | Type | ความหมาย |
|-------|------|---------|
| `ProductLevelID` | int | Shop ID |
| `ProcessID` | int | ID หลักของรายการ |
| `SubProcessID` | int | ID ย่อย |
| `PrinterID` | int | เครื่องพิมพ์ |
| `StaffID` | int | พนักงานที่เสิร์ฟ |

### POST Parameters — serve_table

| Field | Type | ความหมาย |
|-------|------|---------|
| `TableID` | int | โต๊ะที่เสิร์ฟครบ |
| `StaffID` | int | พนักงานที่เสิร์ฟ |

---

## หลักการทำงาน (Technical)

### Optimistic UI
ระบบอัปเดต UI ทันทีก่อนรอ API response เพื่อให้รู้สึกเร็ว:
```
แตะรายการ
  → อัปเดต UI ทันที (ติ๊ก/ยกเลิก)
  → ส่ง POST ไปยัง API
  → ถ้า API error → ย้อน UI กลับ + แสดง toast แจ้งเตือน
```

### Group by Table
ข้อมูลที่ได้จาก API เป็น row ต่อรายการอาหาร ระบบจะ group ตาม `TableID`:
```javascript
rows → groupByTable(rows) → แสดงเป็นการ์ดต่อโต๊ะ
```

### ข้อมูลที่แสดงเฉพาะวันนี้
Query กรองด้วย `DATE(FinishDateTime) = CURDATE()` เพื่อแสดงเฉพาะรายการที่ออกจากครัววันนี้

---

## การติดตั้ง

### 1. ตั้งค่า DB ใน settings.local.php

```php
<?php
return [
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'ชื่อ_database',
    'db_user' => 'username',
    'db_pass' => 'password',
];
```

### 2. เพิ่มคอลัมน์ใน DB (ถ้ายังไม่ได้ทำ)

```sql
ALTER TABLE OrderProcessDetailFront
  ADD ServingStaffID INT NOT NULL DEFAULT 0 AFTER FinishDateTime;
ALTER TABLE OrderProcessDetailFront
  ADD ServingDateTime DATETIME NULL DEFAULT NULL AFTER ServingStaffID;

ALTER TABLE OrderProcessDetail
  ADD ServingStaffID INT NOT NULL DEFAULT 0 AFTER FinishDateTime;
ALTER TABLE OrderProcessDetail
  ADD ServingDateTime DATETIME NULL DEFAULT NULL AFTER ServingStaffID;
```

### 3. เปิดใช้งาน

เปิด browser แล้วไปที่:
```
http://[server-ip]/serve_display/waiter_display.php
```

---

## ความสัมพันธ์กับ Checker KDS

| ระบบ | ผู้ใช้ | หน้าที่ | อัปเดต column |
|------|--------|---------|--------------|
| Checker KDS | พ่อครัว | รับออเดอร์ → ทำอาหาร → Checkout | `ProcessStatus`, `FinishDateTime`, `FinishStaffID` |
| **Serve Display** | **พนักงานเสิร์ฟ** | **รับอาหารจากครัว → เสิร์ฟลูกค้า** | **`ServingStaffID`, `ServingDateTime`** |

ทั้งสองระบบอ่านจาก **ตารางเดียวกัน** (`orderprocessdetailfront`) แต่คนละช่วงของ workflow

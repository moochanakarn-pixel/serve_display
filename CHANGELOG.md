# Serve Display — Changelog

## v1.2.0 (2026-05-17)

### ฟีเจอร์ใหม่

| ฟีเจอร์ | รายละเอียด |
|---|---|
| Settings modal (กด logo 3 ครั้ง) | กดโลโก้ซ้ายบน 3 ครั้งติดกัน → เปิดหน้าตั้งค่าโปรแกรม |
| ตั้งค่า DB Connection | แก้ Host / Port / DB name / User / Password ผ่าน UI โดยไม่ต้อง SSH แก้ไฟล์ |
| ตั้งค่า Computer ID / ชื่อสถานี | กำหนด station สำหรับ nonKds filter ผ่าน UI ได้เลย |
| ตั้งค่า Refresh interval | เลือก 10 / 15 / 30 / 60 วินาที บันทึก localStorage มีผลทันทีในรอบถัดไป |
| toggle เสียงแจ้งเตือน | เปิด/ปิด sound alert บันทึก localStorage |
| nonKds auto-serve | รายการที่ PrinterID ไม่ใช่ของ station นี้ → virtual ServeStatus=1 ทันที — ไม่ write DB |
| Auto-serve set header (nonKds) | sub-item ทุกตัวในกลุ่มเป็น nonKds → header auto-tick ตามโดยอัตโนมัติ |
| ซ่อนหัวเซ็ตโล่ง | หัวเซ็ตที่ sub-items ยังอยู่ในครัวหรือไม่มีเลย → ซ่อนใน tab รอเสิร์ฟ |

---

### API ใหม่

| action | method | ทำอะไร |
|---|---|---|
| `get_settings` | GET | อ่านค่าจาก settings.local.php + version (ทำงานได้ก่อน DB connect) |
| `save_settings` | POST | เขียน settings.local.php — password ว่าง = คงค่าเดิม |

---

### บัคที่แก้ไขใน v1.2.0

| # | บัค | การแก้ |
|---|---|---|
| 23 | applyNonKds Pass 2 mark `_nonKds=true` บน set header ของ station ตัวเอง → กดไม่ได้ | Pass 2 set แค่ `ServeStatus=1` ไม่ set `_nonKds` |
| 24 | tapUnserveAll optimistic reset ล้าง ServeStatus ของ nonKds rows | เพิ่ม `if (!r._nonKds)` ก่อน reset |
| 25 | cascade tap header รวม nonKds sub-items ใน targets → เรียก API ผิด | เพิ่ม `&& !s._nonKds` ใน filter |
| 26 | renderChips done tab ใช้ `every` ทั้งที่ done tab ใช้ `some` → chip ไม่ตรง | เปลี่ยน `every` เป็น `some` |

---

## v1.1.0 (2026-05-14)

### ฟีเจอร์ใหม่

| ฟีเจอร์ | รายละเอียด |
|---|---|
| ซ่อนรายการที่เสิร์ฟแล้วในแท็บ รอเสิร์ฟ | รายการที่ติ๊กแล้วจะหายไปจากแท็บ รอเสิร์ฟ ทันที |
| แท็บ เสิร์ฟแล้ว แสดงทุกโต๊ะที่มีรายการเสิร์ฟ | ไม่ต้องรอให้ครบทุกรายการในโต๊ะก่อน — มีบางรายการเสิร์ฟแล้วก็ขึ้น |
| Cascade click บน set header | คลิกที่ set header → ติ๊ก/ยกเลิกติ๊ก sub-item ทุกรายการในกลุ่มพร้อมกัน |
| Auto-serve header | เมื่อ sub-item ทุกตัวในกลุ่มถูกติ๊กครบ → ตัวหัวเซ็ตจะโดนติ๊กตามอัตโนมัติ |
| แสดงหมายเลขคิว (QueueName) | badge 🎫 ชื่อคิวในหัวการ์ดแต่ละโต๊ะ — ตรงกับจอ Checker |
| แสดง ProcessID บน set-divider | `#000042` ใต้ชื่อเซ็ต ใช้อ้างอิงกับจอ KDS ในครัว |

---

### บัคที่แก้ไขใน v1.1.0

| # | บัค | การแก้ |
|---|---|---|
| 16 | sub-item (ProductSetType < 0) ไม่แสดงในจอเสิร์ฟ (1) | COALESCE(FinishDateTime, SubmitOrderDateTime) ใน WHERE filter ครอบ sub-item ที่ไม่มี FinishDateTime |
| 17 | sub-item ไม่แสดงในจอเสิร์ฟ (2) | `placed` Set ใน sortItemsBySet ใช้ ProductLevelID ที่ซ้ำกัน (price tier) แก้เป็น ProcessID |
| 18 | การจัดกลุ่ม sub-item ในแท็บ อยู่ในครัว พัง | cookSql ขาด ProcessID column — เพิ่มเข้าไปใน SELECT |
| 19 | จำนวนรายการในครัว (cooking badge) นับ sub-item ซ้ำ | กรองเฉพาะ ProductSetType >= 0 ก่อนนับ |
| 20 | backdrop modal ทำงานได้แค่ครั้งเดียว | เปลี่ยนจาก `{once:true}` เป็น named function + removeEventListener ใน close() |
| 21 | unserve_item / unserve_table ไม่ตรวจสอบ StaffID | เพิ่ม if ($staff <= 0) return 400 ทั้งสอง action |
| 22 | FinishDateTime ของ sub-item เป็น NULL ทำให้เวลาแสดงผิด | สืบทอด FinishDateTime จาก set header ลงมาใน sortItemsBySet |

---

## v1.0.0-beta (2026-05-12)

หน้าจอสำหรับพนักงานเสิร์ฟ ใช้คู่กับระบบ KDS ในครัว  
สถานะ: **Pilot / ทดลองใช้งาน**

---

### ฟีเจอร์หลัก

| ฟีเจอร์ | รายละเอียด |
|---|---|
| Login พนักงาน | กรอกรหัสพนักงาน บันทึก session ใน localStorage |
| ติ๊กเสิร์ฟทีละรายการ | กดที่ item row — optimistic update พร้อม rollback ถ้า API ล้มเหลว |
| เสิร์ฟครบโต๊ะ | ปุ่ม "เสิร์ฟครบโต๊ะ" ด้านล่างการ์ด |
| ยกเลิกเสิร์ฟ | ยกเลิกทีละรายการหรือทั้งโต๊ะ พร้อม custom confirm modal |
| 3 tabs | รอเสิร์ฟ / เสิร์ฟแล้ว / อยู่ในครัว (read-only) |
| สถานะครัว | badge 🍳 แสดงจำนวนรายการที่ยังทำอยู่ต่อโต๊ะ |
| ค้นหาโต๊ะ | search box กรองแบบ real-time + chip โต๊ะกด jump ได้ |
| เวลา checkout | แสดง HH:MM ของแต่ละรายการ (FinishDateTime) |
| ชื่อผู้เสิร์ฟ | แสดง badge สีเขียวข้างรายการที่เสิร์ฟแล้ว |
| กลุ่มเซต | หัวเซ็ต (ProductSetType=7) เป็น divider คั่นแต่ละรอบสั่ง |
| Responsive grid | 1→2→3→4 คอลัมน์ตามขนาดหน้าจอ |
| PWA | ติดตั้งเป็น shortcut บนมือถือ/แท็บเล็ตได้ |
| Fullscreen | ปุ่มเต็มจอในหัว |
| Sticky bar | filter tab + search ติดด้านบนเสมอตอน scroll |

---

### ไฟล์ในโปรเจ็ค

| ไฟล์ | บทบาท |
|---|---|
| `waiter_display.php` | หน้าจอหลัก (UI + JavaScript) |
| `api_waiter.php` | Backend API — list_pending, serve_item, unserve_item, serve_table, unserve_table, lookup_staff |
| `config.php` | ค่า config ทั่วไป, DB connection, helper functions |
| `auth_check.php` | ตรวจสอบ authentication |
| `settings.local.php` | ค่า DB และ local settings (ไม่ commit เข้า git) |
| `manifest.json` | PWA manifest |
| `manual.php` | คู่มือการใช้งานและการตั้งค่า (ภาษาไทย) |
| `.gitignore` | ป้องกัน settings.local.php ถูก overwrite โดย git pull |

---

### โครงสร้าง DB

ตาราง `orderprocessdetailfront` — คอลัมน์ที่เพิ่มเข้ามา:

```sql
ALTER TABLE orderprocessdetailfront
  ADD COLUMN ServingStaffID  INT      NOT NULL DEFAULT 0,
  ADD COLUMN ServingDateTime DATETIME NULL     DEFAULT NULL;
```

| คอลัมน์ | ความหมาย |
|---|---|
| `ServingStaffID` | StaffID ของพนักงานที่เสิร์ฟ (0 = ยังไม่เสิร์ฟ) |
| `ServingDateTime` | เวลาที่เสิร์ฟ (NULL = ยังไม่เสิร์ฟ) |

ServeStatus กำหนดจาก `ServingDateTime IS NOT NULL`

---

### API Endpoints

`api_waiter.php?action=<action>`

| action | method | ทำอะไร |
|---|---|---|
| `list_pending` | GET | ดึงรายการ ProcessStatus=1 ใน 24 ชั่วโมงย้อนหลัง พร้อม cooking_rows |
| `serve_item` | POST | ติ๊กเสิร์ฟ 1 รายการ (ต้องมี StaffID > 0) |
| `unserve_item` | POST | ยกเลิกเสิร์ฟ 1 รายการ |
| `serve_table` | POST | เสิร์ฟทุกรายการในโต๊ะที่ยังไม่เสิร์ฟ |
| `unserve_table` | POST | ยกเลิกเสิร์ฟทุกรายการในโต๊ะ |
| `lookup_staff` | POST | ค้นหาพนักงานจาก StaffCode |

---

### บัคที่แก้ไปทั้งหมด

| # | บัค | การแก้ |
|---|---|---|
| 1 | คลิกรายการเดียวไปทั้งโต๊ะ | แก้ tapItem ให้อัปเดตเฉพาะแถวที่คลิก + API WHERE ครบ 6 field |
| 2 | rowKey ไม่มี TableID ทำให้ key ชน | เพิ่ม `_${TableID}` ใน rowKey |
| 3 | รายการที่ติ๊กแล้วบางส่วนกดยกเลิกไม่ได้ | ลบ logic lock served items ออก ทุก row กดได้ |
| 4 | unserve_table ไม่มี date filter | เพิ่ม FinishDateTime filter + ServingDateTime IS NOT NULL |
| 5 | StaffID=0 บันทึก DB ได้ | เช็ค STAFF_ID ใน JS ก่อนทุก action + validate server-side |
| 6 | หน้าว่างเปล่า (rows=0) | CURDATE() filter ไม่ครอบ FinishDateTime เมื่อวาน เปลี่ยนเป็น NOW()-24H |
| 7 | cooking query ไม่มี date filter | เพิ่ม SubmitOrderDateTime >= NOW()-24H |
| 8 | unserve_item ไม่มี date filter | เพิ่ม FinishDateTime >= NOW()-24H |
| 9 | serve_item ไม่ป้องกัน double-serve | เพิ่ม AND ServingDateTime IS NULL |
| 10 | debug_query เปิดให้ทุกคนเรียกได้ | ลบออก |
| 11 | StaffID ไม่ validate ฝั่ง server | เพิ่ม if ($staff <= 0) return 400 |
| 12 | confirmUnserve แสดง modal ก่อน login check | เพิ่ม STAFF_ID check ก่อนเปิด modal |
| 13 | loading spinner ไม่ span เต็ม grid | เพิ่ม grid-column:1/-1 |
| 14 | browser confirm() โชว์ domain name | เปลี่ยนเป็น custom modal |
| 15 | หัวเซ็ตกองรวมกัน ลูกแยก | จัดกลุ่มด้วย ParentProcessID + render เป็น divider |

---

### ข้อจำกัดที่รู้อยู่ (Known Limitations)

- รีเฟรชทุก **30 วินาที** — ไม่ใช่ real-time WebSocket
- ยังไม่มีเสียงแจ้งเตือนเมื่อมีออเดอร์ใหม่
- ชื่อ file ยังเป็น `waiter_display.php`

---

### การตั้งค่า (settings.local.php)

```php
<?php
return array(
  'db_host'              => '127.0.0.1',
  'db_port'              => 3306,
  'db_name'              => 'your_db',
  'db_user'              => 'your_user',
  'db_pass'              => 'your_pass',
  'current_computer_id'  => 1,
  'current_computer_name'=> '',
  'finish_staff_id'      => 1,
  'threshold_yellow'     => 10,
  'threshold_red'        => 20,
  'sound_enabled'        => 0,
  'barcode_camera_enabled'=> 1,
  'kds_two_step_checkout' => 0,
);
```

> ไฟล์นี้ไม่ถูก commit เข้า git (อยู่ใน .gitignore) ต้องสร้างบน server เอง

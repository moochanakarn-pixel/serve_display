# Serve Display — System Reference

> เอกสารอ้างอิงสำหรับพัฒนาหน้าจอ **Serve Display** ต่อจากระบบ Checker KDS  
> อัปเดต: 2026-05-10 · เวอร์ชัน KDS อ้างอิง: v1.5.0

---

## สารบัญ

1. [ภาพรวมระบบ](#1-ภาพรวมระบบ)
2. [สถาปัตยกรรม](#2-สถาปัตยกรรม)
3. [ตาราง DB และหน้าที่](#3-ตาราง-db-และหน้าที่)
4. [ความสัมพันธ์ระหว่างตาราง](#4-ความสัมพันธ์ระหว่างตาราง)
5. [ProcessStatus Codes](#5-processstatus-codes)
6. [TransactionStatusID Codes](#6-transactionstatusid-codes)
7. [ProductSetType (Comment Types)](#7-productsettype-comment-types)
8. [API Endpoints ทั้งหมด](#8-api-endpoints-ทั้งหมด)
9. [Data Flow หลัก](#9-data-flow-หลัก)
10. [โครงสร้างข้อมูล Active Row](#10-โครงสร้างข้อมูล-active-row)
11. [ระบบ Comment / Order Special](#11-ระบบ-comment--order-special)
12. [ระบบ Print Queue](#12-ระบบ-print-queue)
13. [Configuration Constants](#13-configuration-constants)
14. [Activity Log](#14-activity-log)
15. [คำแนะนำสำหรับ Serve Display](#15-คำแนะนำสำหรับ-serve-display)

---

## 1. ภาพรวมระบบ

```
POS (Point of Sale)
  └─ เขียนออเดอร์ลงฐานข้อมูล (ordertransactionfront, orderdetailfront, orderprocessdetailfront)
        │
        ▼
Checker KDS (หน้านี้)
  └─ อ่าน orderprocessdetailfront → แสดงการ์ดรายการอาหาร → กด Checkout → อัปเดต ProcessStatus
        │
        ▼
Serve Display (โปรเจกต์ใหม่)
  └─ อ่านข้อมูลจาก DB เดียวกัน → แสดงสถานะให้พนักงานเสิร์ฟ → รู้ว่าโต๊ะไหนเสร็จแล้ว
```

ทั้ง KDS และ Serve Display ใช้ **ฐานข้อมูลเดียวกัน** บน MySQL ที่ POS เป็นผู้เขียน KDS เป็นผู้อ่านและอัปเดต ProcessStatus

---

## 2. สถาปัตยกรรม

| ส่วน | เทคโนโลยี |
|------|-----------|
| Backend | PHP 7.4+ (FastCGI บน IIS หรือ Apache) |
| Database | MySQL (ใช้ร่วมกับ POS) |
| Frontend | Vanilla JavaScript (ไม่มี framework) |
| Web Server | IIS (Windows) / Apache (Linux) |
| การสื่อสาร | JSON REST API (`api_checker.php`) |
| การ polling | `setInterval` ทุก 15 วินาที (`APP_REFRESH_MS`) |

### ไฟล์หลัก

| ไฟล์ | หน้าที่ |
|------|---------|
| `checker.php` | หน้า UI หลัก |
| `api_checker.php` | API endpoints ทั้งหมด |
| `config.php` | ค่าคงที่ระบบ, โหลด settings.local.php |
| `settings.local.php` | ค่าที่ตั้งต่อเครื่อง (DB host, CID, threshold ฯลฯ) |
| `auth_check.php` | ตรวจสอบ session/auth |
| `logs/kds_cid{N}_{DATE}.log` | Activity log แยกต่อเครื่อง (7 วัน rolling) |

---

## 3. ตาราง DB และหน้าที่

### 3.1 `orderprocessdetailfront` — ตารางหลัก (KDS อ่าน/เขียน)

ตารางนี้คือหัวใจของ KDS ทุก **1 row = 1 รายการอาหาร 1 ชิ้น** ที่ต้องทำในครัว

| คอลัมน์ | Type | หน้าที่ |
|---------|------|---------|
| `ProductLevelID` | INT | Shop ID (มักเป็น PK ร่วม) |
| `ProcessID` | INT | ID หลักของ process row (ใช้ระบุ barcode) |
| `SubProcessID` | INT | ID ย่อย (สำหรับกรณี split qty) |
| `PrinterID` | INT | เครื่องพิมพ์ที่รับผิดชอบ row นี้ |
| `TransactionID` | INT | อ้างอิง ordertransactionfront |
| `ComputerID` | INT | เครื่อง POS ที่สั่ง |
| `OrderDetailID` | INT | อ้างอิง orderdetailfront |
| `ProductID` | INT | อ้างอิง products |
| `ProductName` | VARCHAR | ชื่อสินค้า (snapshot ณ เวลาสั่ง) |
| `ProductAmount` | DECIMAL | จำนวนที่ยังค้างอยู่ในคิว |
| `ProductSetType` | INT | 0=ปกติ, 14=comment, 15=comment+ราคา |
| `ParentProcessID` | INT | ถ้า>0 = เป็น child ของ parent process |
| `ProcessStatus` | INT | สถานะ (ดูหัวข้อ 5) |
| `SubmitOrderDateTime` | DATETIME | เวลาที่ POS ส่งออเดอร์ |
| `FinishDateTime` | DATETIME | เวลาที่ KDS กด checkout |
| `FinishStaffID` | INT | พนักงานที่กด checkout |
| `OrderNo` | INT | หมายเลขออเดอร์ (แสดงบนการ์ด) |
| `OrderDate` | DATE | วันที่ออเดอร์ |
| `TableID` | INT | อ้างอิง tableno |
| `DisplayTableName` | VARCHAR | ชื่อโต๊ะ snapshot (อาจมี `->` ถ้าโอนโต๊ะ) |
| `IsMoveOrder` | INT | 1 = โต๊ะถูกโอน |
| `SaleModeID` | INT | อ้างอิง salemode |

**Primary Key (composite):** `ProductLevelID + ProcessID + SubProcessID + PrinterID`

**KDS เขียนคอลัมน์เหล่านี้เท่านั้น:**
- `ProcessStatus` (ACTIVE→IN_PROCESS→FINISHED / VOIDED→RESOLVED)
- `FinishDateTime`
- `FinishStaffID`
- `ProductAmount` (ลดลงเมื่อ checkout บางส่วน / split)

---

### 3.2 `orderdetailfront` — รายละเอียด order (KDS อ่านอย่างเดียว)

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TransactionID` | FK → ordertransactionfront |
| `ComputerID` | เครื่อง POS |
| `OrderDetailID` | PK ย่อย |
| `ProcessID` | อ้างอิงกลับไป orderprocessdetailfront |
| `ProductID` | FK → products |
| `Amount` | จำนวนสั่ง |
| `Comment` | comment free-text ของ line item |
| `ProductSetType` | ประเภทสินค้า |

KDS อ่าน `Comment` เพื่อแสดงหมายเหตุบนการ์ด

---

### 3.3 `ordertransactionfront` — header ของ transaction

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TransactionID` | PK ร่วมกับ ComputerID |
| `ComputerID` | เครื่อง POS |
| `TableID` | โต๊ะ |
| `OpenTime` | เวลาเปิดโต๊ะ |
| `TransactionStatusID` | สถานะ transaction (ดูหัวข้อ 6) |

KDS ใช้ subquery ดึง `TransactionStatusID = 7` เพื่อแสดง badge "รวมโต๊ะ"

---

### 3.4 `tableno` — ข้อมูลโต๊ะ

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TableID` | PK |
| `TableName` | ชื่อโต๊ะ |
| `ZoneID` | FK → tablezone |
| `Deleted` | soft delete (0=active) |

KDS ใช้: `SELECT TableID FROM tableno WHERE ZoneID = ? AND Deleted = 0`

---

### 3.5 `tablezone` — โซนโต๊ะ

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `zoneid` | PK |
| `zonename` | ชื่อโซน |
| `shopid` | FK → productlevel |
| `Deleted` | soft delete |

KDS ใช้กรอง zone เพื่อแสดงเฉพาะโต๊ะในโซนที่เลือก

---

### 3.6 `products` — สินค้า/เมนู

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `ProductID` | PK |
| `ProductName` | ชื่อสินค้า |
| `ProductLevelID` | FK → shop |
| `Deleted` | soft delete |

KDS ใช้: JOIN เพื่อหาชื่อ comment/option จาก `ordercommentlinkfront`, `ordercommentdetail`, `ordercommentwithpricedetail`

---

### 3.7 `staffs` — พนักงาน

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `StaffID` | PK |
| `StaffCode` | รหัสล็อกอิน (ใช้ในหน้า KDS) |
| `StaffFirstName` | ชื่อ |
| `StaffLastName` | นามสกุล |
| `Deleted` | soft delete |

KDS ใช้:
- `lookup_staff_by_code` — ล็อกอินด้วย StaffCode
- `lookup_staff_name` — แสดงชื่อ default finish staff
- บันทึก `FinishStaffID` ตอน checkout

---

### 3.8 `salemode` — ประเภทการขาย

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `SaleModeID` | PK |
| `SaleModeName` | เช่น "Dine-in", "Takeaway", "Delivery" |
| `Deleted` | soft delete |

KDS JOIN เพื่อแสดงชื่อประเภทการขายบนการ์ด

---

### 3.9 `productlevel` — ระดับ Shop

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `ProductLevelID` | PK (= ShopID ใน KDS) |
| `ProductLevelName` | ชื่อ shop |

ใช้ใน settings เพื่อเลือก shop

---

### 3.10 `printers` — เครื่องพิมพ์

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `PrinterID` | PK |
| `PrinterName` | ชื่อแสดงผล |
| `PrinterDeviceName` | ชื่ออุปกรณ์ Windows |
| `Deleted` | soft delete |

---

### 3.11 `checkeraccessprinter` — mapping เครื่อง KDS → เครื่องพิมพ์

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `ComputerID` | FK → เครื่อง KDS |
| `PrinterID` | FK → printers |

**นี่คือตัว filter หลัก** — KDS แต่ละเครื่องจะแสดงเฉพาะรายการที่ส่งไปยังเครื่องพิมพ์ที่ mapping ไว้

---

### 3.12 `ordercommentlinkfront` — link comment → order line

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TransactionID` | FK |
| `ComputerID` | FK |
| `CommentForOrderID` | FK → orderdetailfront.OrderDetailID |
| `ProductID` | FK → products (ชื่อ comment) |
| `Amount` | จำนวน comment |
| `ProductSetType` | 14 = comment ไม่มีราคา |

---

### 3.13 `ordercommentdetail` — comment บน process row

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TransactionID` | FK |
| `ComputerID` | FK |
| `OrderDetailID` | FK |
| `CommentID` | FK → products |
| `Amount` | จำนวน |

---

### 3.14 `ordercommentwithpricedetail` — comment พร้อมราคา

| คอลัมน์ | หน้าที่ |
|---------|---------|
| `TransactionID` | FK |
| `ComputerID` | FK |
| `OrderLinkID` | FK → orderdetailfront.OrderDetailID |
| `OrderDetailID` | FK → orderdetail |
| `ProductID` | FK → products |

ProductSetType = 15

---

### 3.15 `orderdetail` — รายละเอียด order (ฝั่ง non-front)

ใช้ใน UNION ของ `getKdsAllCommentSql()` เพื่อดึง Comment field

---

### 3.16 `kds_printjoborderdetailfront` / `kds_printjoborderdetail` — คิวพิมพ์

ตารางสำหรับ checkout print queue ระบบ KDS สร้าง INSERT ลงตารางนี้ เครื่องพิมพ์จะมา poll แล้วพิมพ์

ตารางใดมีก็ใช้ตารางนั้น (ลอง `kds_printjoborderdetailfront` ก่อน)

---

### 3.17 `kds_outofstock` — สินค้าหมด (ถ้ามี)

KDS เพิ่มสินค้าหมดผ่าน `set_product_out_of_stock` API จะบันทึกที่นี่

---

## 4. ความสัมพันธ์ระหว่างตาราง

```
productlevel (ShopID)
  ├─ tablezone (zoneid, shopid)
  │    └─ tableno (TableID, ZoneID)
  │         └─ ordertransactionfront (TransactionID, TableID)
  │              └─ orderdetailfront (TransactionID, ComputerID, OrderDetailID)
  │                   └─ orderprocessdetailfront (ProcessID, OrderDetailID) ← KDS หลัก
  │
  └─ products (ProductID)
       ├─ ordercommentlinkfront (ProductID = ชื่อ comment)
       ├─ ordercommentdetail (CommentID = ProductID)
       └─ ordercommentwithpricedetail (ProductID)

staffs (StaffID)
  └─ orderprocessdetailfront.FinishStaffID

printers (PrinterID)
  ├─ checkeraccessprinter (ComputerID, PrinterID) ← filter KDS
  └─ orderprocessdetailfront.PrinterID

salemode (SaleModeID)
  └─ orderprocessdetailfront.SaleModeID
```

---

## 5. ProcessStatus Codes

| ค่า | ชื่อ constant | ความหมาย | การ์ดใน KDS |
|-----|--------------|----------|-------------|
| `0` | `PROCESS_STATUS_ACTIVE` | รอดำเนินการ | แสดงปกติ |
| `2` | `PROCESS_STATUS_IN_PROCESS` | กำลังทำ (กด confirm แล้ว) | แสดงปกติ (status ต่างกัน) |
| `1` | `PROCESS_STATUS_FINISHED` | เสร็จแล้ว (checkout) | ย้ายไปแถบ Finished |
| `98` | `PROCESS_STATUS_VOIDED` | ยกเลิก (POS void) | แสดงสีเทา ปุ่ม Resolve |
| `4` | `PROCESS_STATUS_RESOLVED` | จบสถานะยกเลิก | ซ่อน (ไม่แสดง) |

**Flow ปกติ:**
```
ACTIVE (0) → [กด Confirm] → IN_PROCESS (2) → [กด Checkout] → FINISHED (1)
```

**Flow กรณียกเลิก:**
```
VOIDED (98) → [กด Resolve] → RESOLVED (4)
```

**กรณี Two-Step Checkout ปิด (default):**
```
ACTIVE (0) / IN_PROCESS (2) → [กด Checkout] → FINISHED (1)
```

---

## 6. TransactionStatusID Codes

| ค่า | ความหมาย | KDS ใช้งาน |
|-----|----------|-----------|
| `7` | โต๊ะถูกรวม (merge) | แสดง badge "รวมโต๊ะ" บนการ์ด |
| อื่นๆ | สถานะ transaction ต่างๆ | ไม่ได้ใช้ใน KDS โดยตรง |

ตรวจสอบด้วย subquery:
```sql
SELECT otf2.TransactionStatusID
FROM ordertransactionfront otf2
WHERE otf2.TableID = opf.TableID
  AND otf2.ComputerID = opf.ComputerID
  AND otf2.TransactionStatusID = 7
  AND DATE(otf2.OpenTime) = opf.OrderDate
LIMIT 1
```

---

## 7. ProductSetType (Comment Types)

| ค่า | ความหมาย | แสดงใน KDS |
|-----|----------|-----------|
| `0` | รายการปกติ / free-text comment | comment ธรรมดา |
| `14` | Option/comment ไม่มีราคา | label "คอมเมนต์" |
| `15` | Option/comment มีราคา | label "คอมเมนต์เพิ่มราคา" |
| อื่นๆ | ProductSet (ชุด SETA) | แสดงเป็นการ์ดแยก + parent_name |

**กฎการ merge:**
- `ProductSetType IN (14, 15)` + `ParentProcessID > 0` → merge เข้า `comments[]` ของ parent
- `ProductSetType` อื่น + `ParentProcessID > 0` → แสดงเป็นการ์ดแยก โดย inherit ข้อมูล parent

---

## 8. API Endpoints ทั้งหมด

Base URL: `api_checker.php`  
Query params บังคับ: `kds_cid={CID}&kds_sub={sub_folder}`

### GET Endpoints

| Action | URL | Input | Output |
|--------|-----|-------|--------|
| `list` | `?action=list` | - | active_rows, recent_finished_rows, stats, filters |
| `list_active` | `?action=list_active` | - | active_rows, stats, filters |
| `list_finished` | `?action=list_finished` | - | recent_finished_rows, filters |
| `list_zones` | `?action=list_zones` | - | `zones: [{zoneid, zonename}]` |
| `list_tables_in_zone` | `?action=list_tables_in_zone&zoneid={id}` | zoneid | `table_ids: [int]` |
| `list_shops` | `?action=list_shops` | - | `shops: [{shop_id, shop_name}]` |
| `lookup_staff_name` | `?action=lookup_staff_name&staff_id={id}` | staff_id | `staff_name` |
| `lookup_staff_by_code` | `?action=lookup_staff_by_code&staff_code={code}` | staff_code | `{staff_id, staff_code, staff_name}` |
| `get_system_settings` | `?action=get_system_settings` | - | settings object + staff_name + connection_message |
| `list_print_server_printers` | `?action=list_print_server_printers` | - | `printers[]` |
| `list_out_of_stock_products` | `?action=list_out_of_stock_products` | - | รายการสินค้าหมด |

### POST Endpoints

| Action | Input (POST body) | Output |
|--------|------------------|--------|
| `confirm_one` | ProductLevelID, ProcessID, SubProcessID, PrinterID | `{success, process_status: 2}` |
| `checkout_one` | ProductLevelID, ProcessID, SubProcessID, PrinterID, finish_staff_id | `{success, refresh_finished: true}` |
| `checkout_barcode` | barcode, finish_staff_id | `{success, barcode, process_id, matched_row}` |
| `undo_one` | ProductLevelID, ProcessID, SubProcessID, PrinterID | `{success, refresh_finished: true}` |
| `resolve_status` | ProductLevelID, ProcessID, SubProcessID, PrinterID, finish_staff_id | `{success}` |
| `save_system_settings` | settings fields | `{success, staff_name, requires_reload: true}` |
| `test_system_settings_connection` | settings fields | `{success, staff_name}` |
| `set_product_out_of_stock` | product_id, is_out_of_stock | `{success}` |
| `log_activity` | action_type, detail, staff_id | `{success}` |

---

## 9. Data Flow หลัก

### 9.1 POS → KDS (การสั่งออเดอร์)

```
1. POS สร้าง Transaction ใน ordertransactionfront
2. POS เขียน orderdetailfront (รายการที่สั่ง)
3. POS เขียน orderprocessdetailfront (ProcessStatus = 0, ACTIVE)
   → PrinterID = เครื่องพิมพ์ครัว
   → SubmitOrderDateTime = เวลาสั่ง
4. KDS poll ทุก 15 วินาที → list_active API → แสดงการ์ด
```

### 9.2 KDS Checkout Flow

```
พนักงานกด Checkout บนการ์ด
  → POST checkout_one {ProductLevelID, ProcessID, SubProcessID, PrinterID, finish_staff_id}
  → BEGIN TRANSACTION
  → SELECT FOR UPDATE (lock row)
  → applyCheckoutSplit:
       ถ้า ProductAmount = 1: UPDATE ProcessStatus=1, FinishDateTime=now, FinishStaffID
       ถ้า ProductAmount > 1: INSERT row ใหม่ qty=1 + UPDATE qty เดิม -1
  → fetch child rows (ParentProcessID = ProcessID) → checkout child ด้วย
  → COMMIT
  → writeActivityLog('CHECKOUT', ...)
  → return {success: true, refresh_finished: true}
```

### 9.3 การกรองข้อมูลต่อเครื่อง

```
ComputerID (CID) → checkeraccessprinter → PrinterID[]
  → fetchActiveRows: WHERE PrinterID IN (allowed_ids)
```

แต่ละเครื่อง KDS เห็นเฉพาะรายการที่ส่งไปยังเครื่องพิมพ์ที่ mapping ไว้กับเครื่องนั้น

### 9.4 Zone Filter (ฝั่ง JavaScript)

Zone filter ทำงาน **ฝั่ง JS** ไม่ได้กรองใน SQL:
```
active_rows (ทั้งหมดที่ allowed printers เห็น)
  → JS: rows.filter(r => allowedTableIds.includes(r.TableID))
  → allowedTableIds มาจาก list_tables_in_zone API
```

---

## 10. โครงสร้างข้อมูล Active Row

Row ที่ส่งกลับจาก `list_active`:

```json
{
  "ProductLevelID": 1,
  "ProcessID": 12345,
  "SubProcessID": 0,
  "PrinterID": 2,
  "TransactionID": 500,
  "ComputerID": 1,
  "OrderDetailID": 999,
  "ProductID": 101,
  "ProductName": "ผัดกะเพราหมู",
  "ProductAmount": 1.0,
  "ProductSetType": 0,
  "ParentProcessID": 0,
  "SubmitOrderDateTime": "2026-05-10 12:30:00",
  "FinishDateTime": null,
  "OrderNo": 42,
  "OrderDate": "2026-05-10",
  "TableID": 5,
  "DisplayTableName": "A5",
  "ProcessStatus": 0,
  "IsMoveOrder": 0,
  "SaleModeID": 1,
  "SaleModeName": "Dine-in",
  "TransactionStatusID": 0,
  "is_voided": false,
  "is_moved": false,
  "is_combined": false,
  "moved_to": "",
  "parent_name": null,
  "comments": [
    {
      "text": "ไม่เผ็ด",
      "amount": 1.0,
      "type": 14,
      "label": "คอมเมนต์",
      "is_priced": false,
      "is_free_text": false
    }
  ]
}
```

### Flags พิเศษ (คำนวณใน PHP)

| Flag | เงื่อนไข | แสดงใน KDS |
|------|---------|-----------|
| `is_voided` | `ProcessStatus = 98` | สีเทา + ปุ่ม Resolve |
| `is_moved` | `IsMoveOrder=1 AND DisplayTableName มี "->"` | badge โอนโต๊ะ |
| `is_combined` | `TransactionStatusID=7 AND !voided AND !moved` | badge รวมโต๊ะ |
| `moved_to` | string หลัง `->` ใน DisplayTableName | ชื่อโต๊ะปลายทาง |

---

## 11. ระบบ Comment / Order Special

KDS รวบรวม comment จาก 5 แหล่ง (UNION ALL ใน `getKdsAllCommentSql()`):

| แหล่ง | ตาราง | ประเภท |
|-------|-------|--------|
| 1 | `orderdetailfront` JOIN `ordercommentlinkfront` JOIN `products` | Option ไม่มีราคา |
| 2 | `orderdetailfront.Comment` | Free-text comment |
| 3 | `orderprocessdetailfront` JOIN `orderdetail.Comment` | Free-text จาก backend table |
| 4 | `orderprocessdetailfront` JOIN `ordercommentdetail` JOIN `products` | Comment จาก process |
| 5 | `orderprocessdetailfront` JOIN `ordercommentwithpricedetail` JOIN `products` | Comment มีราคา |

Comment deduplication ด้วย key: `type|text|amount`

---

## 12. ระบบ Print Queue

เมื่อ checkout และมีการพิมพ์ KDS INSERT ลง `kds_printjoborderdetailfront` (หรือ `kds_printjoborderdetail`):

| คอลัมน์สำคัญ | ค่า |
|-------------|-----|
| `KDSStatus` | 2 = checkout print |
| `JobOrderStatus` | 0 = รอพิมพ์ |
| `PrintNo` | auto-increment ต่อ process |
| `ProcessMinute` | เวลารอ (นาที) |
| `PrintStaffName` | "Checker #{staff_id}" |

---

## 13. Configuration Constants

| Constant | ค่า default | ความหมาย |
|----------|------------|---------|
| `APP_REFRESH_MS` | 15000 | Poll interval (ms) |
| `FINISHED_REFRESH_EVERY` | 3 | รีเฟรช finished ทุก N รอบ |
| `PROCESS_STATUS_ACTIVE` | 0 | - |
| `PROCESS_STATUS_IN_PROCESS` | 2 | - |
| `PROCESS_STATUS_FINISHED` | 1 | - |
| `PROCESS_STATUS_VOIDED` | 98 | - |
| `PROCESS_STATUS_RESOLVED` | 4 | - |
| `ACTIVE_ROWS_TODAY_ONLY` | false | กรองเฉพาะวันนี้ (active) |
| `FINISHED_ROWS_TODAY_ONLY` | true | กรองเฉพาะวันนี้ (finished) |
| `RECENT_FINISHED_LIMIT` | 0 | 0=ไม่จำกัด |
| `ALERT_THRESHOLD_YELLOW_DEFAULT` | 10 | นาทีก่อนแจ้งเตือนเหลือง |
| `ALERT_THRESHOLD_RED_DEFAULT` | 20 | นาทีก่อนแจ้งเตือนแดง |
| `DEFAULT_FINISH_STAFF_ID` | 3 | StaffID default เมื่อไม่ได้ login |
| `CURRENT_COMPUTER_ID` | 2 | CID ของเครื่องนี้ |
| `BARCODE_MIN_LENGTH` | 1 | ความยาว barcode ขั้นต่ำ |
| `BARCODE_DIGITS_DISPLAY` | 6 | หลักที่แสดงใน barcode |
| `SHOP_ID` | 0 | ProductLevelID ของ shop |

---

## 14. Activity Log

### รูปแบบไฟล์

```
logs/kds_cid{CID}_{YYYY-MM-DD}.log
```

### รูปแบบ log line

```
2026-05-10 12:30:00 | LOGIN          | CID:2 | Staff:5 | Staff:สมชาย ใจดี
2026-05-10 12:45:00 | CHECKOUT       | CID:2 | Staff:5 | Table:A5 Menu:ผัดกะเพรา Process:12345
```

### Action Types

| Action | เหตุการณ์ |
|--------|---------|
| `APP_START` | เปิดแอป |
| `LOGIN` | พนักงาน login |
| `LOGOUT` | พนักงาน logout |
| `ZONE_CHANGE` | เปลี่ยน zone |
| `CHECKOUT` | กด checkout รายการ |
| `UNDO` | ย้อนรายการที่ checkout ไปแล้ว |
| `SETTINGS_SAVE` | บันทึกค่าระบบ |
| `SETTINGS_VIEW` | เปิดหน้าตั้งค่า (ส่งจาก JS) |

### การจัดการ

- แยกไฟล์ต่อวันต่อ CID
- ลบอัตโนมัติเมื่อไฟล์อายุเกิน **7 วัน** (เช็คที่ `filemtime`)
- เขียนด้วย `FILE_APPEND | LOCK_EX` — ปลอดภัยสำหรับหลาย process พร้อมกัน
- ป้องกันการเข้าถึงผ่าน browser ด้วย `logs/.htaccess` และ `logs/web.config`

---

## 15. คำแนะนำสำหรับ Serve Display

### ข้อมูลที่ต้องการ

Serve Display ต้องการรู้ว่า "โต๊ะไหนมีอาหารพร้อมเสิร์ฟแล้ว" ซึ่งหมายถึง:

```sql
-- โต๊ะที่มีรายการ FINISHED ในช่วงเวลาที่กำหนด
SELECT DISTINCT
    opf.TableID,
    opf.DisplayTableName,
    opf.TransactionID,
    opf.ComputerID,
    opf.OrderNo,
    opf.FinishDateTime,
    opf.FinishStaffID
FROM orderprocessdetailfront opf
WHERE opf.ProcessStatus = 1                        -- FINISHED
  AND opf.FinishDateTime >= NOW() - INTERVAL 30 MINUTE  -- ปรับตามต้องการ
ORDER BY opf.FinishDateTime DESC
```

### การตรวจสอบว่าโต๊ะเคลียร์แล้ว

เมื่อพนักงานเสิร์ฟรับงานแล้ว Serve Display อาจ:
1. **Option A:** เพิ่ม column `ServeStatus` ใน `orderprocessdetailfront` (ต้องแก้ DB schema)
2. **Option B:** สร้างตารางใหม่ `kds_serve_log` เก็บ ProcessID ที่รับแล้ว (แนะนำ — ไม่กระทบ POS)
3. **Option C:** ใช้ `ProcessStatus = 4` (RESOLVED) แทน (แต่ปัจจุบัน 4 = resolved voided)

### API ที่ Serve Display ใช้ได้ทันที

- `GET ?action=list_active` — รายการค้างอยู่ในครัว (รู้ว่าอะไรยังไม่ออก)
- `GET ?action=list_finished` — รายการที่ checkout แล้ว (รู้ว่าอะไรออกแล้ว)
- `GET ?action=list_zones` — รายชื่อโซน
- `GET ?action=list_tables_in_zone&zoneid={id}` — โต๊ะในโซน

### Key Identifiers สำหรับ grouping

ใช้ `TransactionID + ComputerID` เพื่อ group รายการของโต๊ะเดียวกัน  
ใช้ `TableID + OrderDate` เพื่อ filter เฉพาะวันนี้

### ตัวอย่าง Query สรุปสถานะโต๊ะ

```sql
-- สรุปโต๊ะที่มีรายการรอเสิร์ฟ (KDS finished แต่ยังไม่ได้เสิร์ฟ)
SELECT
    opf.TableID,
    MAX(opf.DisplayTableName)  AS TableName,
    opf.TransactionID,
    opf.ComputerID,
    COUNT(*)                    AS FinishedItems,
    MAX(opf.FinishDateTime)    AS LastFinished
FROM orderprocessdetailfront opf
WHERE opf.ProcessStatus = 1
  AND DATE(opf.FinishDateTime) = CURDATE()
GROUP BY opf.TableID, opf.TransactionID, opf.ComputerID
ORDER BY LastFinished DESC
```

### ข้อควรระวัง

1. **Zone filter ทำใน JS** — ถ้าจะทำใน SQL ต้อง JOIN `tableno` กับ `tablezone` เพิ่ม
2. **IsMoveOrder + DisplayTableName** — ถ้า `->` อยู่ใน DisplayTableName แสดงว่าโต๊ะโอน ต้องใช้ส่วนหลัง `->` เป็นชื่อโต๊ะจริง
3. **TransactionID = 0** — บางระบบ POS ไม่ได้ตั้ง TransactionID เสมอไป ระวังใช้ ComputerID ร่วมด้วยเสมอ
4. **checkeraccessprinter** — ถ้า Serve Display ต้องการเห็นทุกโต๊ะ อาจไม่ต้อง filter ด้วย printer mapping (หรือสร้าง mapping ใหม่สำหรับ Serve Display โดยเฉพาะ)
5. **Polling vs WebSocket** — ระบบ KDS ใช้ polling 15 วินาที Serve Display ควรใช้ interval สั้นกว่าถ้าต้องการ real-time (5-10 วินาที)

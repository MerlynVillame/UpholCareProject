# Quick Seed Guide - Create Example Data

## 🚀 Two Options Available

### Option 1: Quick Seed (2024-2025) ⚡ **RECOMMENDED**

**Best for:** Quick testing and seeing immediate results

**What it creates:**
- Years: 2024 and 2025 only
- Bookings: ~140 records (5-7 per month)
- Time: ~5 seconds
- Revenue: ~₱500K-600K per year

**Run this:**
```
http://localhost/UphoCare/database/seed_2024_2025_data.php
```

**Perfect if you want to:**
- ✅ Quickly test the year search
- ✅ See monthly graphs immediately
- ✅ Test current/recent data only
- ✅ Fast setup with minimal wait

---

### Option 2: Full Historical Data (2010-2025) 📊

**Best for:** Complete testing with long-term trends

**What it creates:**
- Years: 2010 through 2025 (15 years)
- Bookings: ~600-900 records
- Time: ~30-60 seconds
- Features: Inflation simulation, growth patterns

**Run this:**
```
http://localhost/UphoCare/database/seed_yearly_test_data.php
```

**Perfect if you want to:**
- ✅ Compare year-over-year trends
- ✅ See business growth patterns
- ✅ Test with historical data
- ✅ Complete dataset

---

## 📊 What You'll See After Quick Seed

### For Year 2024:
```
Total Revenue: ~₱550,000
Total Orders: ~72 bookings
Months: All 12 months populated
Graph: Smooth line showing monthly trend
```

### For Year 2025:
```
Total Revenue: ~₱550,000
Total Orders: ~72 bookings
Months: All 12 months populated
Graph: Smooth line showing monthly trend
```

### Monthly Breakdown Example:
| Month | Orders | Revenue | Profit | Margin |
|-------|--------|---------|--------|--------|
| January | 6 | ₱45,000 | ₱31,500 | 70% |
| February | 5 | ₱38,000 | ₱26,600 | 70% |
| March | 7 | ₱52,000 | ₱36,400 | 70% |
| ... | ... | ... | ... | ... |
| December | 6 | ₱47,000 | ₱32,900 | 70% |

---

## 🎯 Step-by-Step Usage

### Quick Setup (5 seconds):

1. **Run Quick Seeder:**
   ```
   http://localhost/UphoCare/database/seed_2024_2025_data.php
   ```

2. **Wait for "Success" message**

3. **Click "View Reports Dashboard"**

4. **Test the year search:**
   - Type `2024` → Click Search
   - Type `2025` → Click Search
   - See monthly data and graphs!

---

## 🔄 Switching Between Options

### Already Seeded Quick Data, Want Full History?

**Option A:** Add more years (keeps existing data)
1. Just run: `seed_yearly_test_data.php`
2. It will add 2010-2023 data
3. Your 2024-2025 data remains

**Option B:** Start fresh with full history
1. Remove existing: `remove_test_data.php`
2. Run full seeder: `seed_yearly_test_data.php`
3. Gets all years 2010-2025

---

## 🗑️ Remove Test Data

When done testing:
```
http://localhost/UphoCare/database/remove_test_data.php
```

Removes ALL test data (both quick and full)

---

## 📈 Expected Results

### Quick Seed (2024-2025):
```
✅ 2024: ~72 bookings, ~₱550K revenue
✅ 2025: ~72 bookings, ~₱550K revenue
✅ Total: ~140 bookings
✅ Time: ~5 seconds
```

### Full Historical (2010-2025):
```
✅ 2010: ~40 bookings, ~₱70K revenue
✅ 2015: ~50 bookings, ~₱110K revenue
✅ 2020: ~70 bookings, ~₱160K revenue
✅ 2025: ~90 bookings, ~₱220K revenue
✅ Total: ~900 bookings
✅ Time: ~60 seconds
```

---

## 💡 Which Should I Use?

### Use Quick Seed If:
- ⚡ You want fast results NOW
- 🎯 Testing current year functionality
- 🔍 Just need to see if graphs work
- ⏱️ Don't want to wait

### Use Full Historical If:
- 📊 Need year-over-year comparison
- 📈 Want to see trends
- 🏢 Testing complete reporting
- 💼 Demonstrating to stakeholders

---

## 🎨 Visual Preview

### Quick Seed Dashboard View:
```
Available Years: 2024, 2025

┌─────────────────────────────────────┐
│ Search by Year                      │
│ [2024] [Search]                     │
└─────────────────────────────────────┘

Line Graph: Shows 12 points (Jan-Dec)
Table: 12 rows (monthly data)
```

### Full Historical Dashboard View:
```
Available Years: 2010, 2011, 2012... 2025

┌─────────────────────────────────────┐
│ Search by Year                      │
│ [2010-2025] [Search]                │
└─────────────────────────────────────┘

Line Graph: Trends visible across years
Compare: Any two years side-by-side
```

---

## ⚡ TL;DR

**Just want to see it work?**
👉 Run: `/database/seed_2024_2025_data.php` (5 seconds)

**Need complete testing?**
👉 Run: `/database/seed_yearly_test_data.php` (60 seconds)

**Done testing?**
👉 Run: `/database/remove_test_data.php` (instant)

---

**Both options create:**
✅ Completed & paid bookings
✅ Monthly distribution
✅ Realistic prices
✅ Easy to remove
✅ Test data markers

**Choose based on your needs!** 🎯


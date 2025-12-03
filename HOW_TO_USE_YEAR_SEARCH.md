# How to Use Year Search in Reports

## ⚠️ Common Mistake

**WRONG:** Using the DataTable search box (bottom right of the table)
**CORRECT:** Using the Year Search box (top of the page)

---

## 📝 Step-by-Step Instructions

### Step 1: Seed Test Data (Required First Time)

1. Open browser
2. Navigate to: `http://localhost/UphoCare/database/seed_yearly_test_data.php`
3. Wait for completion (creates ~600-900 records)
4. Click "View Reports Dashboard"

### Step 2: Access Reports Page

Navigate to: `http://localhost/UphoCare/admin/reports`

### Step 3: Use the Year Search Box

Look at the **TOP of the page**, you'll see:

```
Sales & Revenue Report     [Year Input Box] [Search Button] [Export PDF] [Refresh]
```

**The Year Search Box looks like this:**
- Has placeholder text: "Enter year (e.g., 2025)"
- Has a blue "Search" button next to it
- Located in the header area

### Step 4: Search for a Year

1. Click in the **Year Search input box** (NOT the DataTable search!)
2. Type a year: `2010`, `2015`, `2020`, `2025`, etc.
3. Click **Search** button OR press **Enter**
4. Page will reload with data for that year

---

## 🎯 Visual Guide

### ✅ CORRECT - Use This Search Box:

```
┌─────────────────────────────────────────────────────────────┐
│ Sales & Revenue Report                                       │
│                                                               │
│ [Enter year (e.g., 2025)] [🔍 Search] [Export] [Refresh]   │
│         ↑                      ↑                             │
│    Type year here        Click this button                   │
└─────────────────────────────────────────────────────────────┘
```

### ❌ WRONG - Don't Use This Search Box:

```
┌─────────────────────────────────────────────────────────────┐
│ Monthly Breakdown - Year 2025          [📅 12 Months]       │
│                                                               │
│ Show 10 entries                          Search: [____]      │
│                                                     ↑         │
│ [Month] [Orders] [Revenue] [Expenses]    This filters rows  │
│                                           NOT for year!      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔍 What Happens When You Search

### Before Search:
- URL: `localhost/UphoCare/admin/reports`
- Shows: Current year (2025) data by default

### After Searching for 2011:
- URL: `localhost/UphoCare/admin/reports/2011`
- Shows: Year 2011 data
- Page title shows: "Viewing Year: 2011"
- KPI cards show: "Total Revenue (2011)"
- Graph shows: "Yearly Income Trend - 2011"

---

## 📊 Test These Years

After seeding data, try searching:

| Year | Expected Result |
|------|----------------|
| **2010** | ~40 bookings, low prices, startup phase |
| **2015** | ~50 bookings, moderate prices, growth phase |
| **2020** | ~70 bookings, higher prices, expansion |
| **2025** | ~90 bookings, highest prices, current operations |

---

## 🐛 Troubleshooting

### "No matching records found"

**Problem:** No data for that year

**Solutions:**
1. ✅ Run the seeder first: `/database/seed_yearly_test_data.php`
2. ✅ Check you searched a year between 2010-2025
3. ✅ Make sure you're using the TOP year search, not table search

### "Nothing happens when I search"

**Problem:** Using wrong search box

**Solution:**
- Don't use the "Search:" box in the table
- Use the year input box at the TOP of the page

### "Page shows 2025 data instead of my searched year"

**Problem:** Search didn't redirect properly

**Solutions:**
1. Check the URL - should show `/admin/reports/2011` (not just `/admin/reports`)
2. Try clicking Search button instead of Enter
3. Clear browser cache and try again

---

## 💡 Quick Test

1. **Seed data:** Visit `/database/seed_yearly_test_data.php`
2. **Go to reports:** Visit `/admin/reports`
3. **Look at TOP:** Find year input box near "Export PDF"
4. **Type:** 2011
5. **Click:** Search button
6. **See:** Page reloads with 2011 data

---

## 📍 Location Reference

```
Page Layout:
┌────────────────────────────────────────────────────┐
│ UPHOLCARE (Sidebar)                                │
├────────────────────────────────────────────────────┤
│ ✅ TOP SECTION - Year Search Here                 │
│ Sales & Revenue Report [YEAR BOX] [SEARCH]        │
│                                                    │
│ Viewing Year: 2011 - Monthly sales...             │
│ Available years: 2010, 2011, ...                  │
├────────────────────────────────────────────────────┤
│ [Revenue Card] [Profit Card] [Income] [Orders]    │
├────────────────────────────────────────────────────┤
│ [LINE GRAPH SHOWING MONTHLY TRENDS]               │
├────────────────────────────────────────────────────┤
│ ❌ BOTTOM SECTION - DataTable Search              │
│ Monthly Breakdown - Year 2011                     │
│ Show 10 entries          Search: [____]          │
│ ^DataTable controls^    ^Filters rows, NOT year^ │
└────────────────────────────────────────────────────┘
```

---

**Remember:** 
1. **Seed first** (one time): `/database/seed_yearly_test_data.php`
2. **Search at TOP**: Use year input box in header
3. **Verify URL**: Should show `/admin/reports/2011`

**Need help?** Check `/database/` for the management dashboard!


# Test Data Distribution Guide (2010-2025)

## Overview
This document shows the expected data distribution across 15 years of test data.

---

## 📊 Year-by-Year Breakdown

### Historical Data (2010-2015) - Early Years
**Characteristics:**
- Lower prices (pre-inflation)
- Fewer bookings per month (3-5)
- Simulates business startup phase

| Year | Avg Bookings/Month | Avg Price Range | Total Bookings/Year |
|------|-------------------|-----------------|---------------------|
| 2010 | 3-4 | ₱600 - ₱3,200 | ~40 bookings |
| 2011 | 3-4 | ₱625 - ₱3,350 | ~42 bookings |
| 2012 | 3-5 | ₱650 - ₱3,500 | ~44 bookings |
| 2013 | 3-5 | ₱675 - ₱3,650 | ~46 bookings |
| 2014 | 4-5 | ₱700 - ₱3,800 | ~48 bookings |
| 2015 | 4-5 | ₱730 - ₱3,950 | ~50 bookings |

**Total (2010-2015):** ~270 bookings

---

### Growth Period (2016-2020) - Expansion Phase
**Characteristics:**
- Moderate price increases
- Increasing booking volume (4-6)
- Business expansion phase

| Year | Avg Bookings/Month | Avg Price Range | Total Bookings/Year |
|------|-------------------|-----------------|---------------------|
| 2016 | 4-6 | ₱760 - ₱4,100 | ~56 bookings |
| 2017 | 4-6 | ₱790 - ₱4,250 | ~58 bookings |
| 2018 | 5-6 | ₱825 - ₱4,450 | ~62 bookings |
| 2019 | 5-7 | ₱860 - ₱4,650 | ~66 bookings |
| 2020 | 5-7 | ₱895 - ₱4,850 | ~70 bookings |

**Total (2016-2020):** ~312 bookings

---

### Recent Years (2021-2025) - Mature Phase
**Characteristics:**
- Higher prices (current market rates)
- High booking volume (6-8)
- Established business phase

| Year | Avg Bookings/Month | Avg Price Range | Total Bookings/Year |
|------|-------------------|-----------------|---------------------|
| 2021 | 6-7 | ₱930 - ₱5,050 | ~74 bookings |
| 2022 | 6-7 | ₱970 - ₱5,250 | ~78 bookings |
| 2023 | 6-8 | ₱1,010 - ₱5,450 | ~82 bookings |
| 2024 | 7-8 | ₱1,050 - ₱5,700 | ~86 bookings |
| 2025 | 7-8 | ₱1,095 - ₱5,950 | ~90 bookings |

**Total (2021-2025):** ~410 bookings

---

## 📈 Overall Statistics

### Grand Totals (2010-2025)
- **Total Years:** 15 years
- **Total Bookings:** ~600-900 bookings
- **Average per Year:** ~50-60 bookings
- **Average per Month:** ~4-6 bookings

### Price Evolution
- **2010 Base Price:** ₱800-₱2,500
- **2025 Current Price:** ₱1,280-₱4,000
- **Inflation Rate:** 4% annually
- **Total Increase:** ~60% over 15 years

### Business Growth
- **2010 Volume:** 3-4 bookings/month
- **2025 Volume:** 7-8 bookings/month
- **Growth Rate:** ~5% annually
- **Total Growth:** 100% booking increase

---

## 🎯 Testing Scenarios

### 1. Historical Comparison
Search different years and compare:
```
Search: 2010  → See startup phase (low volume, low prices)
Search: 2015  → See early growth
Search: 2020  → See expansion period
Search: 2025  → See current operations
```

### 2. Trend Analysis
Compare consecutive years:
```
2010 vs 2011 vs 2012  → See steady growth pattern
2015 vs 2016 vs 2017  → See expansion acceleration
2022 vs 2023 vs 2024  → See mature phase stability
```

### 3. Multi-Year Revenue
View total revenue trends:
```
Early Period (2010-2012): Lower revenue, establishing market
Mid Period (2015-2018): Growing revenue, market penetration
Recent Period (2021-2025): Stable high revenue, market leader
```

### 4. Profit Margin Analysis
Check profit margins across years:
```
All years maintain 70% profit margin
(30% estimated operating costs)
```

---

## 💡 What You'll See in Reports

### Line Graph Behavior
- **2010-2015:** Gradual upward trend
- **2016-2020:** Steeper upward trend
- **2021-2025:** High steady growth

### Monthly Table Pattern
- **Early years:** Lower numbers, fewer orders
- **Recent years:** Higher numbers, more orders
- **All years:** Consistent 70% profit margin

### KPI Cards
- **Total Revenue:** Increases significantly in recent years
- **Total Profit:** Proportional to revenue
- **Highest Income:** Usually in December of recent years
- **Total Orders:** More orders in recent years

---

## 🔢 Sample Monthly Distribution (Any Year)

### Monthly Pattern
Each month gets 3-8 bookings (depending on year), distributed as:
- **Week 1:** 1-2 bookings
- **Week 2:** 1-2 bookings
- **Week 3:** 1-2 bookings
- **Week 4:** 0-2 bookings

### Service Distribution (Random)
- Sofa Repair: ~15%
- Mattress Cover: ~12%
- Chair Upholstery: ~15%
- Couch Restoration: ~10%
- Cushion Repair: ~13%
- Dining Chair Set: ~10%
- Ottoman Repair: ~13%
- Recliner Restoration: ~12%

### Payment Type
- All bookings: Completed & Paid
- Status: Completed
- Payment Status: Paid

---

## 📋 Data Quality Features

### Realistic Elements
✅ Price inflation over time
✅ Business growth simulation
✅ Seasonal variations (random)
✅ Service variety
✅ Price variations within ranges
✅ Additional fees (labor, pickup, delivery, gas, travel)

### Test Data Markers
✅ `notes = 'TEST_DATA_DO_NOT_DELETE'`
✅ `booking_number` starts with 'TEST-'
✅ `item_description` starts with '[TEST]'
✅ Separate test customer account

---

## 🎨 Visual Example

### Expected Line Graph Shape
```
Revenue
   ↑
   |                                            ****
   |                                        ****
   |                                    ****
   |                                ****
   |                            ****
   |                        ****
   |                    ****
   |                ****
   |            ****
   |        ****
   |    ****
   |****
   +------------------------------------------------→
   2010  2012  2014  2016  2018  2020  2022  2024  2025
```

### Expected Revenue Distribution
```
Year Range    Revenue      % of Total
2010-2015     ~25%         Early growth
2016-2020     ~30%         Expansion
2021-2025     ~45%         Mature phase
```

---

## ✅ Quality Checks

### After Seeding, Verify:
1. **Year Coverage:** Check data exists for all years 2010-2025
2. **Price Trends:** Prices should increase over time
3. **Volume Trends:** More bookings in recent years
4. **Graph Shape:** Upward trending line
5. **Profit Margins:** Consistent ~70% across all years

### Quick SQL Check:
```sql
-- Check year distribution
SELECT YEAR(updated_at) as year, COUNT(*) as bookings
FROM bookings 
WHERE notes = 'TEST_DATA_DO_NOT_DELETE'
GROUP BY YEAR(updated_at)
ORDER BY year;

-- Check price trends
SELECT YEAR(updated_at) as year, 
       AVG(grand_total) as avg_price,
       MIN(grand_total) as min_price,
       MAX(grand_total) as max_price
FROM bookings 
WHERE notes = 'TEST_DATA_DO_NOT_DELETE'
GROUP BY YEAR(updated_at)
ORDER BY year;
```

---

**Last Updated:** November 2025  
**Version:** 2.0 (15-year historical data)


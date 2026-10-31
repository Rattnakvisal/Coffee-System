# ការទូទាត់សម្រាប់ POS នៅកម្ពុជា

## Cash / សាច់ប្រាក់

- បញ្ចូល USD ឬ KHR ឬទាំងពីរ ក្នុងប្រអប់ Cash received។ ប្រាក់ដែលនៅខ្វះ និងប្រាក់អាប់នឹងគណនាភ្លាមៗ។
- `Exact USD` និង `Exact riel` កំណត់ចំនួនទឹកប្រាក់គ្រប់សរុប។ ប៊ូតុង $5, $10, ៛10,000 និង ៛20,000 ជួយបញ្ចូលរហ័ស។
- ជ្រើស KHR ឬ USD សម្រាប់ប្រាក់អាប់។ តម្លៃបង្ហាញជារូបិយប័ណ្ណផ្សេងគឺជាតម្លៃស្មើប៉ុណ្ណោះ។
- ប្រព័ន្ធប្រៀបធៀបប្រាក់ទទួលដោយ integer arithmetic មុនបង្គត់ ដើម្បីកុំឱ្យប្រាក់មិនគ្រប់ក្លាយជាគ្រប់ពេលប្ដូរទៅ USD។
- USD អនុញ្ញាត 2 ខ្ទង់ទសភាគ។ KHR ប្រើចំនួនគត់។ សរុប KHR បង្គត់ឡើងទៅ 1 រៀល និងប្រាក់អាប់ KHR បង្គត់ចុះទៅ 1 រៀល។ មិនមានការបង្គត់តាមក្រដាសប្រាក់ 100 រៀលដោយស្វ័យប្រវត្តិទេ។

## KHQR / បាគង

1. ជ្រើស KHQR និងរូបិយប័ណ្ណ KHR ឬ USD។
2. ឱ្យអតិថិជន scan KHQR ពិតរបស់ហាង តាមរូបិយប័ណ្ណដែលបានជ្រើស និងបង់តាមចំនួនបង្ហាញ។
3. Cashier ពិនិត្យក្នុង app ធនាគាររបស់ហាងថាបានទទួលប្រាក់ពិត ចំនួនទឹកប្រាក់ត្រូវ និង transaction reference ត្រូវ។
4. បញ្ចូល reference រួចធីក «Payment received / បានទទួលប្រាក់» មុនបញ្ចប់ order។

នេះជាការបញ្ជាក់ដោយ cashier តាមជម្រើសរបស់ម្ចាស់ project។ មិនទាន់មានការភ្ជាប់ bank API ឬពិនិត្យការទូទាត់ស្វ័យប្រវត្តិទេ។ បើប្ដូររូបិយប័ណ្ណ ឬ cart ការបញ្ជាក់នឹងត្រូវធ្វើឡើងវិញ។ ប្រព័ន្ធមិនបង្កើត QR គំរូសម្រាប់ទទួលប្រាក់ទេ។ បើមិនបានកំណត់រូប QR វាប្រាប់ឱ្យប្រើ KHQR stand របស់ហាង។

## កំណត់ក្នុង .env

```dotenv
# អត្រារបស់ហាង មិនមែនអត្រា NBC ដែលទាញយកស្វ័យប្រវត្តិទេ
POS_KHR_PER_USD=4000
POS_KHQR_MERCHANT_NAME="Purr's Coffee"
POS_KHQR_KHR_IMAGE=/images/payments/khqr-khr.png
POS_KHQR_USD_IMAGE=/images/payments/khqr-usd.png
POS_CARD_ENABLED=false
```

ប្រើរូប KHQR ពិតដែលចេញដោយធនាគាររបស់ហាង និងអនុញ្ញាតឱ្យអតិថិជនបញ្ចូលចំនួនប្រាក់។ ដាក់រូបក្នុង `public/images/payments/` រួចកំណត់ path ខាងលើ។ ទុក image setting ទទេ ប្រសិនបើប្រើ QR stand នៅ counter។ កុំប្រើ QR ដែលចាក់ចំនួនប្រាក់ថេរសម្រាប់គ្រប់ order។

បើប្រើ configuration cache ក្រោយប្ដូរ .env សូមរត់ `php artisan config:clear`។ កំណត់ `POS_CARD_ENABLED=true` តែពេលហាងមាន card terminal សម្រាប់ទទួលប្រាក់។ Card ក៏ទាមទារ transaction reference និងការបញ្ជាក់ដោយ cashier ដែរ។

## ទិន្នន័យ order

Sales report និង inventory money-in នៅតែគិតជា USD។ `orders.payment_details` រក្សាទុកអត្រានៅពេលលក់ ប្រាក់ USD/KHR ដែលទទួល រូបិយប័ណ្ណប្រាក់អាប់ ឬ KHQR reference និងពេលបញ្ជាក់។ ទិន្នន័យ order ចាស់នៅដដែល ហើយ column ថ្មីអនុញ្ញាត null។ Order History បង្ហាញ KHQR, រូបិយប័ណ្ណ, reference និងប្រាក់អាប់សម្រាប់ order ថ្មី។

Migration សម្រាប់ environment ផ្សេង៖

```powershell
php artisan migrate --path=database/migrations/2026_10_31_000000_add_payment_details_to_orders_table.php
```

ប្រភពផ្លូវការ៖ [NBC / Bakong — KHQR](https://bakong.nbc.gov.kh/en/) និង [PPCBank — KHQR, KHR និង USD](https://www.ppcbank.com.kh/ppcbank-khqr/)។ KHQR ជាស្តង់ដារ QR រួមសម្រាប់អ្នកផ្ដល់សេវាដែលចូលរួម ដូច្នេះមិនចាំបាច់បែងចែក ABA, ACLEDA និង Wing ជា payment method ផ្សេងគ្នាទេ។

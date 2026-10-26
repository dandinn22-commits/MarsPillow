# העלאת האתר להוסטינגר — שלב אחרי שלב

הזמן המשוער: כ־30–45 דקות. לא צריך לדעת לתכנת.

## 1. לחבר את הדומיין ולהפעיל אבטחה (SSL)
1. בלוח הבקרה של הוסטינגר (hPanel): **Websites → Add website**, ובחר את הדומיין שקנית.
2. **Security → SSL**: הפעל את התעודה החינמית על הדומיין. זה מה שנותן את המנעול בדפדפן, ובלעדיו התשלומים לא יעבדו.

## 2. לבחור גרסת PHP
**Advanced → PHP Configuration** → בחר **PHP 8.2** (או 8.1 ומעלה) → Save.

## 3. ליצור מסד נתונים
1. **Databases → Management**: צור מסד נתונים חדש. רשום לעצמך את שם המסד, שם המשתמש והסיסמה.
2. לחץ על **Enter phpMyAdmin** ליד המסד שיצרת.
3. בחר את המסד מהרשימה משמאל, לחץ **Import**, בחר את הקובץ `install.sql` ולחץ **Go**.
4. אמורות להופיע 3 טבלאות: `orders`, `order_items`, `contact_messages`.

## 4. להעלות את הקבצים
ב־**Files → File Manager** תראה את התיקייה `domains/הדומיין-שלך/` ובתוכה `public_html`.

1. העלה את **התוכן** של התיקייה `public_html` מהפרויקט אל `public_html` בשרת (כולל הקובץ `.htaccess`). אם יש שם קובץ `default.php` של הוסטינגר, מחק אותו.
2. העלה את התיקייה **`private`** כולה אל `domains/הדומיין-שלך/`, **ליד** `public_html` ולא בתוכה:
```
domains/הדומיין-שלך/
├── private/        ← כאן
└── public_html/    ← וכאן תוכן האתר
```
טיפ: הכי מהיר לדחוס לקובץ zip, להעלות, ולחלץ עם לחיצה ימנית → Extract.

## 5. למלא את ההגדרות
1. בתוך `private`, שכפל את `config.example.php` ושנה את שם העותק ל־`config.php`.
2. פתח את `config.php` (לחיצה ימנית → Edit) ומלא:
   - `base_url`: הכתובת המלאה של האתר, למשל `https://www.marspillow.com` (בלי `/` בסוף).
   - `db`: שם המסד, המשתמש והסיסמה מסעיף 3. בשורת `dsn` מחליפים רק את שם המסד.
   - `support_email`, `business_name`, `business_address`, `shipping_days`.
   - בשלב הזה השאר `'mode' => 'test'`.
3. שמור.

## 6. ליצור סיסמה לדף הניהול
1. היכנס ל־`https://הדומיין-שלך/admin/make-hash` (עובד רק במצב בדיקה).
2. בחר סיסמה חזקה (10 תווים ומעלה) ולחץ **Create hash**.
3. העתק את הטקסט שמתקבל והדבק אותו ב־`config.php`, בשדה `admin` → `password_hash`.
4. היכנס ל־`/admin` עם הסיסמה שבחרת.

## 7. להגדיר שליחת מיילים
1. ב־**Emails** צור תיבת דואר כמו `orders@הדומיין-שלך`.
2. ב־`config.php` → `mail`: מלא את `from_email` ואת `admin_email` (הכתובת שאליה יגיעו התראות על הזמנות).
3. ב־`smtp`: שנה `enabled` ל־`true`, ומלא את `user` ואת `pass` של התיבה שיצרת. השרת `smtp.hostinger.com` ופורט 465 כבר מוגדרים.

## 8. לחבר את Stripe (תשלום בכרטיס) במצב בדיקה
1. פתח חשבון ב־stripe.com. ההרשמה דורשת חברה אמריקאית או חשבון בנק נתמך.
2. ודא שהמתג **Test mode** דלוק. ב־**Developers → API keys** העתק את המפתח שמתחיל ב־`sk_test_` אל `stripe` → `test` → `secret_key`.
3. **Developers → Webhooks → Add endpoint**:
   - כתובת: `https://הדומיין-שלך/webhook/stripe`
   - אירועים: `checkout.session.completed` ו־`checkout.session.async_payment_succeeded`
   - אחרי השמירה העתק את ה־**Signing secret** (מתחיל ב־`whsec_`) אל `webhook_secret`.

## 9. לחבר את PayPal במצב בדיקה
1. היכנס ל־developer.paypal.com עם חשבון PayPal Business.
2. **Apps & Credentials** → ודא שבחרת **Sandbox** → **Create App**.
3. העתק את ה־**Client ID** ואת ה־**Secret** אל `paypal` → `test`.

## 10. משימה מתוזמנת (מיילים אוטומטיים)
המשימה הזו שולחת שני סוגי מיילים: בקשת ביקורת 21 ימים אחרי שהזמנה נשלחה, ותזכורת למי שהשאיר מייל ועזב סל קניות.
1. **Advanced → Cron Jobs → Custom**.
2. בשדה הפקודה הדבק (החלף את שם המשתמש והדומיין, ושניהם מופיעים בנתיב של מנהל הקבצים):
```
/usr/bin/php /home/שם-המשתמש/domains/הדומיין-שלך/private/cron.php
```
3. הגדר הרצה **כל 15 דקות** ושמור. אחרי שעה תוכל לראות את התוצאות בקובץ `private/storage/logs/cron.log`.

## 11. לבדוק
עבור על [TEST-PLAN.md](TEST-PLAN.md). כשהכל עובד, המשך ל־[GO-LIVE.md](GO-LIVE.md).

## אם משהו לא עובד
- **מופיע "Setup needed"**: הקובץ `config.php` לא נמצא. ודא שהוא בתוך `private`, ושהתיקייה `private` נמצאת ליד `public_html` ולא בתוכה.
- **שגיאת מסד נתונים**: בדוק את השם, המשתמש והסיסמה ב־`db`.
- **עמודים חוץ מהבית מחזירים 404**: ודא שהקובץ `.htaccess` הועלה. בהוסטינגר קבצים שמתחילים בנקודה עלולים להיות מוסתרים, ואפשר להציג אותם בהגדרות של מנהל הקבצים.
- **לוג שגיאות**: `private/storage/logs/php-error.log`. יש שם גם `stripe.log`, `paypal.log` ו־`orders.log`.

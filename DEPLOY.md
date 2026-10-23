# رفع الموقع على استضافة صحارى (cPanel)

الدومين: `advisory.com.sa` · السيرفر: `sl32.sahara.net.sa` · حساب cPanel: `advisory`

> الأوامر تحت تفترض إن مجلد المنزل على السيرفر هو `/home/advisory`. تأكد من المسار في cPanel (يظهر في الشريط الجانبي تحت "Home Directory") وعدّله لو اختلف.

---

## قبل البدء (مرة واحدة)

### ١. غيّر كلمة سر cPanel
كلمة السر الحالية انكشفت. من cPanel ← **Password & Security** غيّرها.

### ٢. وجّه الدومين للاستضافة
لو الدومين مسجّل عند جهة ثانية (مثل SaudiNIC أو مسجّل آخر)، غيّر الـ Nameservers إلى:
- `ns10.sahara.com.sa`
- `ns11.sahara.com.sa`

التغيير يأخذ من ساعات إلى يوم. قبلها تقدر تشتغل على cPanel عبر `https://sl32.sahara.net.sa:2083`.

### ٣. إصدار PHP والإضافات
cPanel ← **Select PHP Version** (أو **MultiPHP Manager**):
- الإصدار: **8.4.27 أو أحدث** (أو 8.5.12+). الإصدارات الأقدم فيها ثغرات PHP2Shell.
- فعّل الإضافات: `intl`، `pdo_mysql`، `mbstring`، `fileinfo`، `gd`، `zip`، `bcmath`، `openssl`، `curl`.

### ٤. قاعدة البيانات
cPanel ← **MySQL® Databases**:
1. أنشئ قاعدة بيانات، مثلاً `advisory_site` (cPanel يضيف اسم الحساب قبلها فتصير `advisory_advisory_site` غالباً).
2. أنشئ مستخدم بكلمة سر قوية.
3. اربط المستخدم بالقاعدة مع **ALL PRIVILEGES**.

احفظ الاسم الكامل للقاعدة والمستخدم وكلمة السر للخطوة ٧.

### ٥. بريد الإرسال
cPanel ← **Email Accounts**: أنشئ `no-reply@advisory.com.sa`. منه تنرسل رموز الدخول للوحة التحكم وإشعارات الطلبات.

أنشئ كذلك `info@advisory.com.sa` لو ما كان موجود، لأنه يستقبل الطلبات.

---

## الرفع

### ٦. ارفع الحزمة وفك الضغط
1. على جهازك ابنِ الحزمة (أو استخدم الموجودة في `deploy/advisory-site.zip`):
   ```bash
   bash scripts/package-for-cpanel.sh
   ```
2. cPanel ← **File Manager** ← افتح مجلد المنزل `/home/advisory` (مو `public_html`).
3. **Upload** ← ارفع `advisory-site.zip`.
4. أنشئ مجلد اسمه `advisory`، انقل الملف داخله، واضغط **Extract**.

النتيجة: المشروع في `/home/advisory/advisory`، وملف `artisan` موجود مباشرة داخله.

### ٧. ملف الإعدادات `.env`
داخل `/home/advisory/advisory`:
1. انسخ `.env.production.example` باسم `.env` (فعّل **Show Hidden Files** من Settings في File Manager لتشوف الملفات اللي تبدأ بنقطة).
2. افتحه بـ **Edit** وعبّي:
   - `DB_DATABASE` و `DB_USERNAME` و `DB_PASSWORD` من الخطوة ٤.
   - `MAIL_PASSWORD` كلمة سر `no-reply@advisory.com.sa`.
   - `ADMIN_PASSWORD` كلمة سر قوية لحساب لوحة التحكم.

### ٨. أوامر التشغيل الأولى
cPanel ← **Terminal** (لو مو ظاهر، اطلب تفعيله من دعم صحارى أو فعّل SSH):
```bash
cd ~/advisory
php -v                                   # تأكد إن الإصدار 8.4.27+ أو 8.5.12+
php artisan key:generate --force
php artisan migrate --force --seed
php artisan storage:link
php artisan optimize
php artisan filament:optimize
```
بعد ما تدخل لوحة التحكم أول مرة، احذف قيمة `ADMIN_PASSWORD` من `.env`.

> لو `php -v` طلع إصدار أقدم من اللي اخترته في الخطوة ٣، استخدم المسار الكامل، مثل `/opt/cpanel/ea-php84/root/usr/bin/php`.

### ٩. اربط الدومين بمجلد `public`
الموقع لازم يفتح على `advisory/public` فقط، عشان ما تنكشف ملفات مثل `.env`.

**الطريقة الأسهل (Terminal):** استبدل `public_html` برابط للمجلد:
```bash
cd ~
mv public_html public_html_old
ln -s ~/advisory/public public_html
```

**أو من cPanel ← Domains:** لو يسمح بتعديل **Document Root** للدومين، خلّه `advisory/public`.

### ١٠. شهادة SSL
cPanel ← **SSL/TLS Status** ← **Run AutoSSL**. بعد ما يشتغل الدومين تتفعّل `https://advisory.com.sa` تلقائياً.

لتحويل كل الزيارات إلى https، أضف في أول `advisory/public/.htaccess` بعد `RewriteEngine On`:
```apache
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### ١١. المهام المجدولة (Cron)
تُرسل إيميلات الطلبات والتوظيف عبر طابور يشتغل كل دقيقة. cPanel ← **Cron Jobs** ← أضف:
- **Common Settings:** Once Per Minute (`* * * * *`)
- **Command:**
  ```
  cd /home/advisory/advisory && php artisan schedule:run >> /dev/null 2>&1
  ```

---

## بعد الرفع

### ١٢. افحص الموقع
- `https://advisory.com.sa` يفتح بالعربي، و`/en` بالإنجليزي.
- `https://advisory.com.sa/management` ← ادخل بـ `ADMIN_EMAIL` و`ADMIN_PASSWORD`، واختر رمز البريد الإلكتروني. لازم يوصلك الرمز على `admin@advisory.com.sa`، فتأكد إن هذا البريد موجود أو غيّر إيميل الأدمن من لوحة التحكم.
- أرسل طلب تجريبي من صفحة التواصل وتأكد إن الإيميل وصل `info@advisory.com.sa` خلال دقيقة.

### ١٣. عبّي البيانات من لوحة التحكم
الإعدادات ← **عام والتواصل**: الهاتف، واتساب، العنوان الوطني، موقع الخريطة.
الإعدادات ← **SEO**: معرّف Google Analytics، رمز Search Console، رابط Google Business.

### ١٤. Google
- **Search Console:** أضف `https://advisory.com.sa`، تحقق بالرمز، وأرسل `https://advisory.com.sa/sitemap.xml`.
- **Google Business Profile:** أنشئ ملف الشركة في الرياض من business.google.com وحط رابطه في الإعدادات.

---

## التحديثات لاحقاً
1. على جهازك: `bash scripts/package-for-cpanel.sh`
2. ارفع الحزمة الجديدة وفك الضغط فوق `~/advisory` (ملف `.env` ومجلد `storage` ما يتأثرون لأن الحزمة ما فيها `.env` ولا ملفات مرفوعة).
3. في Terminal:
   ```bash
   cd ~/advisory
   php artisan down
   php artisan migrate --force
   php artisan optimize
   php artisan filament:optimize
   php artisan up
   ```

## لو صار خطأ
- صفحة بيضاء أو خطأ 500: افتح `~/advisory/storage/logs/` وشوف آخر ملف.
- خطأ صلاحيات: المجلدات `storage` و`bootstrap/cache` لازم تكون قابلة للكتابة (755).
- لا تفعّل `APP_DEBUG=true` على الموقع الحي إلا دقائق للتشخيص، لأنه يعرض بيانات حساسة.

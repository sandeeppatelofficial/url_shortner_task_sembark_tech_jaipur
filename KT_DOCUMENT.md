# URL Shortener Project - KT Document

Hi Developer, yeh document is project ka complete Knowledge Transfer (KT) hai. Isme saare modules, features, roles aur data flow ki detail explanation hai taaki project easily samajh aa jaye.

## 1. Project Overview
Yeh ek Laravel 11 based URL Shortener application hai jisme multi-tenancy (company-based) aur role-based access control (RBAC) implement kiya gaya hai. Isme users URLs ko short kar sakte hain, short URLs pe clicks track kar sakte hain, aur specific permissions ke sath naye users/members ko invite kar sakte hain.

## 2. Roles & Permissions (User Types)
Project me basically 3 types ke roles define kiye gaye hain:

- **Superadmin**: 
  - Iska koi specific `company_id` nahi hota (it is `null`). 
  - Isko poore system me maujood kisi bhi company ke sabhi URLs dikhte hain. 
  - Yeh naye 'admins' aur nayi companies ko system me invite kar sakta hai.
  - Lekin, yeh khud short URLs create nahi kar sakta.

- **Admin**: 
  - Yeh ek specific Company se juda hota hai (iska `company_id` hota hai). 
  - Yeh apni company ke saare members aur unke banaye hue short URLs dekh sakta hai. 
  - Yeh naye short URLs bana sakta hai aur apni company me kaam karne ke liye naye 'members' ko invite kar sakta hai.

- **Member**: 
  - Yeh bhi ek Company ka part hota hai (same `company_id` as their Admin). 
  - Yeh dashboard par sirf apne khud ke banaye hue short URLs dekh sakta hai. 
  - Yeh naye short URLs create kar sakta hai. 
  - Ise kisi ko invite karne ki permission nahi hoti.

## 3. Key Modules & Features

### A. Authentication & Invitation Module
- **Login (`/login`)**: Simple login page jaha user apna email aur password daal kar login karta hai. Iska logic `LoginController` me handle hota hai.
- **Invitations (`/invitations/create` & `/invitations`)**: Naye users ko system me register karne ka tarika open signup nahi hai, balki invite-only flow hai.
  - Superadmin -> Admin ko invite karta hai.
  - Admin -> Members ko invite karta hai.
  - Jab koi invite create hota hai, toh backend me ek unique 16-32 character ka token banta hai jo `invitations` table me save hota hai.
  - Us token ke base par user ko ek link milta hai (e.g. `/invite/{token}`).
  - Link open karne par invited user apna naam aur password set karke invitation complete (`/invite/{token}` POST request) karta hai. Phir background me uska actual User account ban jata hai aur invite mark as accepted ho jata hai.

### B. URL Shortener Module
- **Create Short URL (`/urls/create` & `/urls`)**: Admin ya Member apne long original URLs ko yaha submit karte hain.
  - Form submit hone ke baad `ShortUrlController` ek unique 6-character random short code generate karta hai.
  - Yeh data `short_urls` table me save ho jata hai. Is table me URL ke sath user ki ID aur uski Company ID bhi link ho jati hai.
- **List Short URLs (`/urls`)**: Yeh dashboard hai jaha URLs ki list aati hai.
  - Query lagate waqt role-based access / visibility lagayi gayi hai:
    - *Superadmin* -> `query()` me koi filter nahi, sab dikhega.
    - *Admin* -> `where('company_id', $user->company_id)`
    - *Member* -> `where('user_id', $user->id)`
- **Public URL Redirect (`/s/{code}`)**: Yeh openly accessible / public route hai jise koi bhi hit kar sakta hai. 
  - Jab bhi browser se `/s/abc123` jaisa chhota link open hota hai toh controller backend me us code se original URL nikalta hai.
  - Us specific URL ka 'clicks' count 1 se increment karta hai (metrics ke liye).
  - Aur finally user ko original URL par HTTP redirect kar deta hai.

## 4. Database Structure (Main Tables)
Data samajhna zaroori hai, ye main tables hain:
- `users`: `id`, `name`, `email`, `password`, `role`, `company_id` (foreign key).
- `companies`: `id`, `name`, `created_at`.
- `short_urls`: `id`, `company_id`, `user_id`, `original_url`, `short_code`, `clicks`.
- `invitations`: `id`, `company_id`, `invited_by`, `email`, `role`, `token`, `accepted_at`.

## 5. Important Technical Details (For Developer)
- **Framework**: Laravel 11. Dhyan rakhna ki Laravel 11 me directory structure thoda clean hai. (Routing/middleware configurations `bootstrap/app.php` me hoti hain, Kernel files default hidden rehti hain).
- **Models & Relationships**: Models (jaise `User.php`, `Company.php`, `ShortUrl.php`, `Invitation.php`) check karlena, unke relations (`belongsTo`, `hasMany`) already setup hain.
- **Test Cases**: Pura business logic test cases se covered hai. `tests/Feature/ShortUrlTest.php` me saare role checks tested hain. Code push ya change karne se pehle root directory me `php artisan test` zaroor chalana chahiye, agar sab 'PASS' hai to matlab logic theek hai.
- **Middlewares**: `routes/web.php` me route groups bane hue hain jo `auth`, `guest` aur custom `role` middlewares use karte hain jisse unauthorized pages block hote hain.

---
### Next Steps
Jab bhi naya code likhna start karo, sabse pehle `routes/web.php` open karo taaki endpoints samajh aayen, aur uske related Controllers me jaake data flow observe karo. Happy Coding!

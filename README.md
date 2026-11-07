# Aptieka | PharmaCare24

Mācību projekts: aptiekas tīmekļa vietne ar zāļu katalogu, pasūtījumu noformēšanu un administrācijas paneli. Projekts izstrādāts ar PHP, MySQL, HTML, CSS un JavaScript.

**Tīmekļa vietne:** https://pharmacare24.free.je/  
**Administrācijas panelis:** https://pharmacare24.free.je/admin/login.php

> **Piezīme:** Šī ir demonstrācijas sistēma, nevis īsta aptieka. Neievadiet īstus pacientu personas datus un neveiciet reālus maksājumus. Maksājumu funkcionalitāte ir atkarīga no Stripe konfigurācijas.

## Vietnes izmantošana

1. Atveriet [PharmaCare24](https://pharmacare24.free.je/).
2. Sadaļā **Zāles** apskatiet katalogu. Zāles var meklēt un pārlūkot, izmantojot saraksta meklēšanas un lapošanas iespējas.
3. Nospiediet **Vairāk informācijas**, lai apskatītu papildu informāciju par vielu no **PubChem API**, ja tā ir pieejama.
4. Pievienojiet izvēlētās zāles grozam, pārbaudiet daudzumu un kopējo cenu.
5. Ja grozā ir recepšu zāles, pirms apmaksas jāveic receptes pārbaude, ievadot pacienta datus. Demonstrācijai izmantojiet tikai testa datus.
6. Turpiniet uz **Stripe Checkout**, ja maksājumu integrācija ir konfigurēta testēšanai.

Vietnē pieejama arī tumšā un gaišā tēma, adaptīvs dizains un kontaktinformācijas sadaļa.

## Administrācijas panelis

**Pieteikšanās:** [pharmacare24.free.je/admin/login.php](https://pharmacare24.free.je/admin/login.php)

1. Atveriet administrācijas paneļa saiti.
2. Ievadiet administratora vai moderatora lietotājvārdu un paroli.
3. Pēc veiksmīgas autorizācijas atvērsies **Informācijas panelis**.
4. Lai beigtu darbu, augšējā joslā nospiediet izrakstīšanās pogu.

Piekļuve ir paredzēta tikai autorizētiem lietotājiem. Drošības dēļ administratora piekļuves dati nav publicēti README. Demonstrācijas piekļuvi var saskaņot ar projekta autoru.

### Administrācijas paneļa iespējas

| Sadaļa | Iespējas |
| --- | --- |
| **Informācijas panelis** | Pārskatīt noliktavas zāļu, aktīvo recepšu un jauno pasūtījumu statistiku, kā arī pēdējos pasūtījumus. |
| **Zāļu noliktava** | Pievienot, rediģēt un dzēst zāles; mainīt cenu, atlikumu, kategoriju, attēlu un receptes nepieciešamību. |
| **Receptes** | Apskatīt, pievienot, rediģēt un dzēst receptes; pārvaldīt pacienta, ārsta, zāļu un derīguma datus. |
| **Pasūtījumi** | Apskatīt pasūtījumus un to detaļas, mainīt statusu, kā arī izveidot pasūtījumu uz vietas aptiekā. |
| **Lietotāji** | Izveidot, rediģēt un dzēst administrācijas lietotājus; piešķirt administratora vai moderatora lomu. Pieejams tikai galvenajam administratoram. |
| **Darbību žurnāls** | Pārskatīt sistēmā reģistrētās darbības. Pieejams tikai galvenajam administratoram. |

Pasūtījumu statusi: **Jauns**, **Apstrādē**, **Gatavs**, **Izsniegts**, **Atcelts**.

### Lietotāju lomas

- **Administrators:** piekļuve visām administrācijas sadaļām, arī lietotāju pārvaldībai un darbību žurnālam.
- **Moderators:** piekļuve informācijas panelim, zāļu noliktavai, receptēm un pasūtījumiem, bet ne lietotāju pārvaldībai vai darbību žurnālam.

## Izmantotās tehnoloģijas

- **PHP** un **MySQL (PDO)** – servera loģika un datu glabāšana.
- **HTML, CSS, JavaScript** – lietotāja saskarne un interaktīvās funkcijas.
- **Fetch API** – pieprasījumi administrācijas API un dinamiska datu atjaunošana.
- **PubChem PUG REST API** – papildu informācija par vielām.
- **Stripe Checkout** – maksājumu integrācija (ja konfigurēta).
- **Composer** – PHP atkarību pārvaldība, tostarp `stripe/stripe-php`.

## Projekta palaišana lokāli

Nepieciešams PHP ar PDO MySQL atbalstu, MySQL, Composer un lokāls PHP tīmekļa serveris (piemēram, Laragon vai XAMPP).

1. Klonējiet repozitoriju:
   ```bash
   git clone https://github.com/aomineJavaWorker/Aptieka.git
   cd Aptieka
   ```
2. Instalējiet atkarības:
   ```bash
   composer install
   ```
3. Izveidojiet MySQL datubāzi un importējiet `database/aptieka.sql`, lai izveidotu tabulas. Ja nepieciešami demonstrācijas ieraksti, pārskatiet un importējiet `database/seed.sql`. **Uzmanību:** šis fails dzēš un aizstāj datus vairākās tabulās.
4. Projekta saknes mapē izveidojiet `.env` failu ar saviem iestatījumiem, piemēram:
   ```dotenv
   DB_HOST=localhost
   DB_NAME=aptieka
   DB_USER=root
   DB_PASSWORD=
   DB_CHARSET=utf8mb4
   STRIPE_SECRET_KEY=sk_test_YOUR_TEST_KEY
   ```
5. Konfigurējiet lokālo tīmekļa serveri tā, lai tas atvērtu projekta `index.php`. Pārliecinieties, ka `.env` nav pieejams caur tīmekļa pārlūku. Stripe testēšanai izmantojiet tikai testa atslēgas.

## Projekta struktūra

```text
admin/           Administrācijas paneļa lapas
api/             PHP API pieprasījumu apstrādei
assets/          CSS, JavaScript un attēli
config/          Datubāzes un Stripe konfigurācija
database/        SQL shēma un demonstrācijas dati
includes/        Autorizācija un kopīgie PHP moduļi
uploads/         Augšupielādētie zāļu attēli
index.php        Publiskā sākumlapa
```

## Drošība un demonstrācijas dati

- `.env` fails ir iekļauts `.gitignore` un nedrīkst tikt publicēts GitHub.
- Nenorādiet README īstas administratora paroles, MySQL paroli vai Stripe slepenās atslēgas.
- Demonstrācijas datiem izmantojiet izdomātus pacientu datus un atsevišķus testa lietotājus.
- Publiskai demonstrācijai neveidojiet koplietojamu administratora kontu ar tiesībām mainīt reālus datus.

---

**Autors:** [aomineJavaWorker](https://github.com/aomineJavaWorker)  
**Projekta veids:** Mācību un portfolio projekts.

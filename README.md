
# PharmaCare24

**PharmaCare24** ir mācību un portfolio projekts — aptiekas tīmekļa vietne ar zāļu katalogu, pasūtījumu noformēšanu un administrācijas paneli.

Projekts izstrādāts, izmantojot **PHP, MySQL, HTML, CSS un JavaScript**. Tajā realizēta produktu pārvaldība, meklēšana, filtrēšana, pasūtījumu apstrāde un Stripe Checkout integrācija.

**Tīmekļa vietne:** https://pharmacare24.free.je/

**Administrācijas panelis:** https://pharmacare24.free.je/admin/login.php

> **Piezīme:** PharmaCare24 ir demonstrācijas sistēma, nevis īsta aptieka. Neievadiet reālus pacientu personas datus un neveiciet īstus maksājumus. Maksājumu funkcionalitāte paredzēta testēšanai.

---

## Vietnes izmantošana

### 1. Sākumlapa

Atveriet [PharmaCare24](https://pharmacare24.free.je/).

Sākumlapā pieejama informācija par projektu, produktu izlase un iespēja pāriet uz zāļu katalogu. Vietne ir pielāgota datoriem un mobilajām ierīcēm.

<img width="760" alt="image" src="https://github.com/user-attachments/assets/90d52b49-6932-4aa7-a793-47ad035bbbe3" />


### 2. Produktu izlase

Sadaļā **Kataloga izlase** redzami atlasīti produkti no demonstrācijas kataloga.

Katram produktam ir attēls, nosaukums, aktīvā viela un cena. Nospiežot uz produkta, tiek atvērta tā detalizētās informācijas lapa.

<img width="760" alt="image" src="https://github.com/user-attachments/assets/0ee0b9d1-e2d3-48db-aa37-d97a5a3df5e4" />


### 3. Zāļu katalogs

Sadaļā **Zāļu katalogs** iespējams apskatīt pieejamos produktus.

Katalogā realizētas šādas funkcijas:

- Meklēšana pēc produkta nosaukuma un informācijas.
- Filtrēšana pēc kategorijas.
- Filtrēšana pēc minimālās un maksimālās cenas.
- Recepšu un bezrecepšu zāļu atlase.
- Filtrēšana pēc pieejamības.
- Kārtošana pēc cenas un nosaukuma.
- Lapošana un attēlojamo produktu skaita izvēle.
- Visu filtru atiestatīšana.

<img width="760" alt="image" src="https://github.com/user-attachments/assets/4ffb284b-1f4f-40f6-b3e6-feeffaa581ed" />


### 4. Produkta detalizētā informācija

Nospiediet **Vairāk informācijas**, lai atvērtu atsevišķu produkta lapu.

Tajā pieejama šāda informācija:

- Produkta attēls un nosaukums.
- Aktīvā viela un ražotājs.
- Zāļu forma un stiprums.
- Cena un noliktavas atlikums.
- Informācija par receptes nepieciešamību.
- Saite uz Latvijas Zāļu reģistru oficiālās informācijas pārbaudei.

No produkta lapas iespējams izvēlēties daudzumu un pievienot produktu grozam.

<img width="760" alt="image" src="https://github.com/user-attachments/assets/916a590c-e053-4e88-adc0-d94746ad6096" />


### 5. Pirkumu grozs

Nospiežot groza ikonu, tiek atvērts pirkumu grozs.

Lietotājs var pievienot produktus, mainīt to daudzumu, izņemt produktus un apskatīt kopējo cenu.

Groza saturs tiek saglabāts pārlūka lokālajā krātuvē (Local Storage).

<img width="460" alt="image" src="https://github.com/user-attachments/assets/c53703a8-af27-4a96-bd63-e105e2413fc2" />


### 6. Recepšu pārbaude un apmaksa

Ja grozā ir recepšu zāles, pirms pasūtījuma noformēšanas nepieciešama receptes pārbaude.

Demonstrācijas sistēmā tiek izmantota recepšu informācija no projekta datubāzes.

<img width="460" alt="image" src="https://github.com/user-attachments/assets/6259ce6b-7baa-4290-aa57-9c9f94c53f9f" />


Pēc nepieciešamo pārbaužu veikšanas lietotājs var turpināt uz **Stripe Checkout**, ja maksājumu integrācija ir konfigurēta testēšanai.

### 7. Papildu iespējas

Sadaļā **Kāpēc izvēlēties PharmaCare24?** izceltas trīs galvenās priekšrocības:

- **Ātra produktu atrašana** — meklēšana, filtri un kārtošana.
- **Pārskatāma informācija** — atsevišķas produktu lapas.
- **Ērti jebkurā ierīcē** — responsīvs dizains un gaišā/tumšā tēma.

<img width="760" alt="image" src="https://github.com/user-attachments/assets/88b8ab80-3b83-49d7-b5b5-aafbb24e5224" />


---

## Administrācijas panelis

**Pieteikšanās:** https://pharmacare24.free.je/admin/login.php

Administrācijas panelis paredzēts autorizētiem lietotājiem ar atbilstošām piekļuves tiesībām.

Pēc pieslēgšanās tiek atvērts informācijas panelis, kurā redzama statistika par produktiem, receptēm un pasūtījumiem.

<img width="760" height="731" alt="image" src="https://github.com/user-attachments/assets/24fc61c1-8af1-43cf-8902-ad314e06970e" />


### Administrācijas paneļa iespējas

| Sadaļa | Funkcionalitāte |
| --- | --- |
| Informācijas panelis | Produktu, recepšu un pasūtījumu statistika. |
| Zāļu noliktava | Produktu pievienošana, rediģēšana un dzēšana. |
| Receptes | Recepšu un to derīguma informācijas pārvaldība. |
| Pasūtījumi | Pasūtījumu apskate, izveide un statusu pārvaldība. |
| Lietotāji | Administratoru un moderatoru pārvaldība. |
| Darbību žurnāls | Sistēmā veikto darbību pārskatīšana. |

**Pasūtījumu statusi:**

`Jauns` → `Apstrādē` → `Gatavs` → `Izsniegts`

Pasūtījumu iespējams arī atcelt.

### Lietotāju lomas

**Administrators** — piekļuve visām administrācijas sadaļām, tostarp lietotāju pārvaldībai un darbību žurnālam.

**Moderators** — piekļuve informācijas panelim, produktu, recepšu un pasūtījumu pārvaldībai.

---

## Izmantotās tehnoloģijas

| Tehnoloģija | Pielietojums |
| --- | --- |
| PHP | Servera loģika un API. |
| MySQL | Produktu, lietotāju, recepšu un pasūtījumu datubāze. |
| PDO | Droša datubāzes vaicājumu izpilde. |
| HTML5 | Tīmekļa lapu struktūra. |
| CSS3 | Responsīvs dizains un gaišā/tumšā tēma. |
| JavaScript | Filtrēšana, kārtošana, grozs un interaktīvās funkcijas. |
| Fetch API | Datu apmaiņa ar servera API. |
| Stripe Checkout | Testa maksājumu integrācija. |
| Composer | PHP atkarību pārvaldība. |

---

## Projekta palaišana lokāli

Nepieciešams:

- PHP ar PDO MySQL atbalstu.
- MySQL datubāzes serveris.
- Composer.
- Lokālais tīmekļa serveris, piemēram, Laragon vai XAMPP.

### 1. Klonējiet repozitoriju

```bash
git clone https://github.com/aomineJavaWorker/PharmaCare24.git
cd PharmaCare24
```

### 2. Instalējiet atkarības

```bash
composer install
```

### 3. Sagatavojiet datubāzi

Izveidojiet MySQL datubāzi un importējiet:

- `database/aptieka.sql` — datubāzes struktūra.
- `database/seed.sql` — demonstrācijas dati.

**Uzmanību:** pirms `seed.sql` importa pārbaudiet tā saturu, jo tas var aizstāt esošos datus.

### 4. Izveidojiet .env failu

Projekta saknes mapē izveidojiet `.env`:

```dotenv
DB_HOST=localhost
DB_NAME=aptieka
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
STRIPE_SECRET_KEY=sk_test_YOUR_TEST_KEY
```

Norādiet savus datubāzes piekļuves datus un Stripe testa atslēgu, ja nepieciešama maksājumu testēšana.

### 5. Palaidiet projektu

Palaidiet Apache un MySQL, izmantojot Laragon vai XAMPP, un atveriet projekta `index.php` pārlūkā.

---

## Projekta struktūra

```text
PharmaCare24/
├── admin/                  Administrācijas panelis
├── api/                    PHP API
├── assets/
│   ├── css/                Dizaina stili
│   ├── js/                 JavaScript funkcionalitāte
│   └── img/                Vietnes attēli
├── config/                 Datubāzes un Stripe konfigurācija
├── database/               SQL shēma un demonstrācijas dati
├── includes/               Kopīgie PHP moduļi
├── uploads/medicines/      Produktu attēli
├── index.php               Sākumlapa un katalogs
├── medicine.php            Produkta detalizētā lapa
├── payment_success.php     Veiksmīgas apmaksas rezultāts
├── payment_cancel.php      Atceltas apmaksas rezultāts
└── README.md               Projekta dokumentācija
```

---

## Drošība un demonstrācijas dati

- Datubāzes paroles un Stripe slepenās atslēgas tiek glabātas `.env` failā.
- `.env` ir iekļauts `.gitignore`.
- Datubāzes pieprasījumos tiek izmantots PDO.
- Administrācijas panelī realizēta autentifikācija un lietotāju lomas.
- Demonstrācijas sistēmā jāizmanto tikai izdomāti pacientu dati.
- Reāli maksājumi un īstu recepšu datu ievadīšana nav paredzēta.

---

**Autors:** [aomineJavaWorker](https://github.com/aomineJavaWorker)

**Projekta veids:** Mācību un portfolio projekts.

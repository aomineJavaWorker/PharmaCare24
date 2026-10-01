SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


/* =========================================================
   NOTĪRĀM APTIEKAS DATU TABULAS
   ========================================================= */

TRUNCATE TABLE audit_logs;

TRUNCATE TABLE order_items;
TRUNCATE TABLE prescription_items;

TRUNCATE TABLE orders;
TRUNCATE TABLE prescriptions;

TRUNCATE TABLE medicines;
TRUNCATE TABLE categories;

TRUNCATE TABLE users;


/* =========================================================
   LIETOTĀJI
   ========================================================= */

INSERT INTO users (
    id,
    username,
    password_hash,
    role
)
VALUES
(
    1,
    'kristovskis',
    '$2y$10$qtBvhQ2qHtG54HJiudaIdeSHXEn64KdanD41hksC7RrTEXF2DDz42',
    'admin'
),
(
    2,
    'moder',
    '$2y$10$L6AVXaaFZyWDDQYHKRpouuwUmYwlWj1T0eCmJhKEuPBjF4V2JvRoC',
    'moderator'
);


/* =========================================================
   KATEGORIJAS
   ========================================================= */

INSERT INTO categories (
    id,
    name
)
VALUES
(1, 'Pretsāpju līdzekļi'),
(2, 'Vitamīni un minerālvielas'),
(3, 'Alerģijas līdzekļi'),
(4, 'Saaukstēšanās līdzekļi'),
(5, 'Kuņģa-zarnu trakta līdzekļi');


/* =========================================================
   ZĀLES
   ========================================================= */

INSERT INTO medicines (
    id,
    category_id,
    name,
    active_substance,
    api_search_name,
    manufacturer,
    dosage_form,
    strength,
    price,
    stock_quantity,
    prescription_required,
    image
)
VALUES
(
    1, 1,
    'Paracetamol',
    'Paracetamols',
    'paracetamol',
    'Olainfarm',
    'Tabletes',
    '500 mg',
    2.49,
    120,
    0,
    'uploads/medicines/Paracetamol.png'
),
(
    2, 1,
    'Ibuprofen',
    'Ibuprofēns',
    'ibuprofen',
    'Grindeks',
    'Tabletes',
    '400 mg',
    3.19,
    83,
    0,
    'uploads/medicines/Ibuprofen.png'
),
(
    3, 2,
    'Vitamin D3',
    'Holekalciferols',
    'cholecalciferol',
    'Walmark',
    'Kapsulas',
    '2000 SV',
    6.99,
    60,
    0,
    'uploads/medicines/Vitamin_D3.png'
),
(
    4, 3,
    'Cetirizins',
    'Cetirizīns',
    'cetirizine',
    'Teva',
    'Tabletes',
    '10 mg',
    5.10,
    41,
    0,
    'uploads/medicines/Cetirizins.png'
),
(
    5, 4,
    'Otrivin',
    'Ksilometazolīns',
    'xylometazoline',
    'Haleon',
    'Deguna aerosols',
    '0.1%',
    4.89,
    35,
    0,
    'uploads/medicines/Otrivin.png'
),
(
    6, 4,
    'Strepsils',
    'Amilmetakrezols',
    'amylmetacresol',
    'Reckitt',
    'Pastilas',
    '-',
    5.49,
    55,
    0,
    'uploads/medicines/Strepsils.png'
),
(
    7, 5,
    'Smecta',
    'Diosmektīts',
    'diosmectite',
    'Ipsen',
    'Pulveris',
    '3 g',
    7.20,
    25,
    0,
    'uploads/medicines/Smecta.png'
),
(
    8, 1,
    'Voltaren',
    'Diklofenaks',
    'diclofenac',
    'Haleon',
    'Gēls',
    '2%',
    8.90,
    18,
    0,
    'uploads/medicines/Voltaren.png'
),
(
    9, 2,
    'Magne B6',
    'Magnijs un B6 vitamīns',
    'pyridoxine',
    'Sanofi',
    'Tabletes',
    '-',
    9.40,
    45,
    0,
    'uploads/medicines/Magne_B6.png'
),
(
    10, 3,
    'Suprastin',
    'Hloropiramīns',
    'chloropyramine',
    'Egis',
    'Tabletes',
    '25 mg',
    4.20,
    18,
    1,
    'uploads/medicines/Suprastin.png'
),
(
    11, 4,
    'Septolete',
    'Benzidamīns',
    'benzydamine',
    'KRKA',
    'Pastilas',
    '-',
    5.99,
    37,
    0,
    'uploads/medicines/Septolete.png'
),
(
    12, 1,
    'No-Spa',
    'Drotaverīns',
    'drotaverine',
    'Sanofi',
    'Tabletes',
    '40 mg',
    4.70,
    47,
    0,
    'uploads/medicines/No-Spa.png'
),
(
    13, 1,
    'Aspirin',
    'Acetilsalicilskābe',
    'acetylsalicylic acid',
    'Bayer',
    'Tabletes',
    '500 mg',
    3.50,
    69,
    1,
    'uploads/medicines/Aspirin.png'
),
(
    14, 4,
    'ACC',
    'Acetilcisteīns',
    'acetylcysteine',
    'Sandoz',
    'Putojošās tabletes',
    '600 mg',
    7.80,
    30,
    0,
    'uploads/medicines/ACC.png'
),
(
    15, 2,
    'Vitamin C',
    'Askorbīnskābe',
    'ascorbic acid',
    'Walmark',
    'Tabletes',
    '500 mg',
    4.99,
    89,
    0,
    'uploads/medicines/Vitamin_C.png'
);


/* =========================================================
   RECEPTES
   ========================================================= */

INSERT INTO prescriptions (
    id,
    prescription_number,
    patient_name,
    personal_code,
    doctor_name,
    issued_at,
    valid_until,
    status
)
VALUES
(
    1,
    'R-2026-0001',
    'Testa Pacients 1',
    '999999-00001',
    'Dr. Bērziņš',
    '2026-09-01',
    '2026-10-01',
    'Aktīva'
),
(
    2,
    'R-2026-0002',
    'Testa Pacients 2',
    '999999-00002',
    'Dr. Ozola',
    '2026-09-02',
    '2026-10-02',
    'Aktīva'
),
(
    3,
    'R-2026-0003',
    'Testa Pacients 3',
    '999999-00003',
    'Dr. Kalniņš',
    '2026-09-03',
    '2026-10-03',
    'Izmantota'
),
(
    4,
    'R-2026-0004',
    'Testa Pacients 4',
    '999999-00004',
    'Dr. Liepa',
    '2026-09-04',
    '2026-10-04',
    'Aktīva'
),
(
    5,
    'R-2026-0005',
    'Testa Pacients 5',
    '999999-00005',
    'Dr. Jansons',
    '2026-09-05',
    '2026-10-05',
    'Atcelta'
),
(
    6,
    'R-2026-0006',
    'Testa Pacients 6',
    '999999-00006',
    'Dr. Bērziņš',
    '2026-09-06',
    '2026-10-06',
    'Aktīva'
),
(
    7,
    'R-2026-0007',
    'Testa Pacients 7',
    '999999-00007',
    'Dr. Ozola',
    '2026-09-07',
    '2026-10-07',
    'Aktīva'
),
(
    8,
    'R-2026-0008',
    'Testa Pacients 8',
    '999999-00008',
    'Dr. Kalniņš',
    '2026-09-08',
    '2026-10-08',
    'Izmantota'
),
(
    9,
    'R-2026-0009',
    'Testa Pacients 9',
    '999999-00009',
    'Dr. Liepa',
    '2026-09-09',
    '2026-10-09',
    'Aktīva'
),
(
    10,
    'R-2026-0010',
    'Testa Pacients 10',
    '999999-00010',
    'Dr. Jansons',
    '2026-09-10',
    '2026-10-10',
    'Aktīva'
),
(
    11,
    'R-2026-0011',
    'Testa Pacients 11',
    '999999-00011',
    'Dr. Bērziņš',
    '2026-09-11',
    '2026-10-11',
    'Aktīva'
),
(
    12,
    'R-2026-0012',
    'Testa Pacients 12',
    '999999-00012',
    'Dr. Ozola',
    '2026-09-12',
    '2026-10-12',
    'Aktīva'
),
(
    13,
    'R-2026-0013',
    'Testa Pacients 13',
    '999999-00013',
    'Dr. Kalniņš',
    '2026-09-13',
    '2026-10-13',
    'Izmantota'
),
(
    14,
    'R-2026-0014',
    'Testa Pacients 14',
    '999999-00014',
    'Dr. Liepa',
    '2026-09-14',
    '2026-10-14',
    'Aktīva'
),
(
    15,
    'R-2026-0015',
    'Testa Pacients 15',
    '999999-00015',
    'Dr. Jansons',
    '2026-09-15',
    '2026-10-15',
    'Aktīva'
);


/* =========================================================
   RECEPŠU ZĀLES
   ========================================================= */

INSERT INTO prescription_items (
    prescription_id,
    medicine_id,
    quantity
)
VALUES
(1, 10, 1),
(2, 4, 1),
(3, 10, 2),
(4, 4, 1),
(5, 10, 1),
(6, 4, 2),
(7, 10, 1),
(8, 4, 1),
(9, 10, 1),
(10, 4, 2),
(11, 10, 1),
(12, 4, 1),
(13, 10, 1),
(14, 4, 2),
(15, 10, 1);


/* =========================================================
   PASŪTĪJUMI
   ========================================================= */

INSERT INTO orders (
    id,
    customer_name,
    customer_email,
    status,
    payment_status,
    total
)
VALUES
(
    1,
    'Jānis Bērziņš',
    'janis.berzins@example.com',
    'Jauns',
    'Neapmaksāts',
    5.68
),
(
    2,
    'Anna Ozola',
    'anna.ozola@example.com',
    'Apstrādē',
    'Apmaksāts',
    6.99
),
(
    3,
    'Mārtiņš Kalniņš',
    'martins.kalnins@example.com',
    'Gatavs',
    'Apmaksāts',
    10.20
),
(
    4,
    'Laura Liepa',
    'laura.liepa@example.com',
    'Izsniegts',
    'Apmaksāts',
    4.89
),
(
    5,
    'Roberts Jansons',
    'roberts.jansons@example.com',
    'Jauns',
    'Neapmaksāts',
    12.69
),
(
    6,
    'Elīna Krūmiņa',
    'elina.krumina@example.com',
    'Apstrādē',
    'Apmaksāts',
    8.90
),
(
    7,
    'Artūrs Vītols',
    'arturs.vitols@example.com',
    'Gatavs',
    'Apmaksāts',
    9.40
),
(
    8,
    'Ieva Priede',
    'ieva.priede@example.com',
    'Izsniegts',
    'Apmaksāts',
    5.99
),
(
    9,
    'Kristaps Zariņš',
    'kristaps.zarins@example.com',
    'Jauns',
    'Neapmaksāts',
    7.80
),
(
    10,
    'Līga Siliņa',
    'liga.silina@example.com',
    'Atcelts',
    'Neapmaksāts',
    4.99
),
(
    11,
    'Rihards Lapiņš',
    'rihards.lapins@example.com',
    'Jauns',
    'Neapmaksāts',
    9.40
),
(
    12,
    'Kate Gailīte',
    'kate.gailite@example.com',
    'Apstrādē',
    'Apmaksāts',
    11.48
),
(
    13,
    'Edgars Eglītis',
    'edgars.eglitis@example.com',
    'Gatavs',
    'Apmaksāts',
    7.00
),
(
    14,
    'Marta Balode',
    'marta.balode@example.com',
    'Izsniegts',
    'Apmaksāts',
    13.98
),
(
    15,
    'Andris Pētersons',
    'andris.petersons@example.com',
    'Jauns',
    'Neapmaksāts',
    15.60
);


/* =========================================================
   PASŪTĪJUMU ZĀLES
   ========================================================= */

INSERT INTO order_items (
    order_id,
    medicine_id,
    prescription_id,
    quantity,
    unit_price
)
VALUES
(1, 1, NULL, 1, 2.49),
(1, 2, NULL, 1, 3.19),

(2, 3, NULL, 1, 6.99),

(3, 4, NULL, 2, 5.10),

(4, 5, NULL, 1, 4.89),

(5, 6, NULL, 1, 5.49),
(5, 7, NULL, 1, 7.20),

(6, 8, NULL, 1, 8.90),

(7, 9, NULL, 1, 9.40),

(8, 11, NULL, 1, 5.99),

(9, 14, NULL, 1, 7.80),

(10, 15, NULL, 1, 4.99),

(11, 12, NULL, 2, 4.70),

(12, 1, NULL, 1, 2.49),
(12, 13, NULL, 1, 3.50),

(13, 13, NULL, 2, 3.50),

(14, 3, NULL, 2, 6.99),

(15, 14, NULL, 2, 7.80);


SET FOREIGN_KEY_CHECKS = 1;
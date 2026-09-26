USE aptieka;

INSERT INTO categories (name) VALUES
('Pretsāpju līdzekļi'),
('Vitamīni un minerālvielas'),
('Alerģijas līdzekļi'),
('Saaukstēšanās līdzekļi'),
('Kuņģa-zarnu trakta līdzekļi');


INSERT INTO medicines
(
    category_id,
    name,
    active_substance,
    manufacturer,
    dosage_form,
    strength,
    price,
    stock_quantity,
    prescription_required
)
VALUES
(1, 'Paracetamol', 'Paracetamols', 'Olainfarm', 'Tabletes', '500 mg', 2.49, 120, 0),
(1, 'Ibuprofen', 'Ibuprofēns', 'Grindeks', 'Tabletes', '400 mg', 3.19, 85, 0),
(2, 'Vitamin D3', 'Holekalciferols', 'Walmark', 'Kapsulas', '2000 SV', 6.99, 60, 0),
(3, 'Cetirizin', 'Cetirizīns', 'Teva', 'Tabletes', '10 mg', 5.10, 42, 0),
(4, 'Otrivin', 'Ksilometazolīns', 'Haleon', 'Deguna aerosols', '0.1%', 4.89, 35, 0),
(4, 'Strepsils', 'Amilmetakrezols', 'Reckitt', 'Pastilas', '-', 5.49, 55, 0),
(5, 'Smecta', 'Diosmektīts', 'Ipsen', 'Pulveris', '3 g', 7.20, 25, 0),
(1, 'Voltaren', 'Diklofenaks', 'Haleon', 'Gēls', '2%', 8.90, 18, 0),
(2, 'Magne B6', 'Magnijs un B6 vitamīns', 'Sanofi', 'Tabletes', '-', 9.40, 44, 0),
(3, 'Suprastin', 'Hloropiramīns', 'Egis', 'Tabletes', '25 mg', 4.20, 20, 1),
(4, 'Septolete', 'Benzidamīns', 'KRKA', 'Pastilas', '-', 5.99, 38, 0),
(1, 'No-Spa', 'Drotaverīns', 'Sanofi', 'Tabletes', '40 mg', 4.70, 47, 0),
(1, 'Aspirin', 'Acetilsalicilskābe', 'Bayer', 'Tabletes', '500 mg', 3.50, 70, 0),
(4, 'ACC', 'Acetilcisteīns', 'Sandoz', 'Putojošās tabletes', '600 mg', 7.80, 30, 0),
(2, 'Vitamin C', 'Askorbīnskābe', 'Walmark', 'Tabletes', '500 mg', 4.99, 90, 0);

INSERT INTO prescriptions
(
    prescription_number,
    patient_name,
    doctor_name,
    issued_at,
    valid_until,
    status
)
VALUES
('R-2026-0001', 'Testa Pacients 1', 'Dr. Bērziņš', '2026-09-01', '2026-10-01', 'Aktīva'),
('R-2026-0002', 'Testa Pacients 2', 'Dr. Ozola', '2026-09-02', '2026-10-02', 'Aktīva'),
('R-2026-0003', 'Testa Pacients 3', 'Dr. Kalniņš', '2026-09-03', '2026-10-03', 'Izmantota'),
('R-2026-0004', 'Testa Pacients 4', 'Dr. Liepa', '2026-09-04', '2026-10-04', 'Aktīva'),
('R-2026-0005', 'Testa Pacients 5', 'Dr. Jansons', '2026-09-05', '2026-10-05', 'Atcelta'),
('R-2026-0006', 'Testa Pacients 6', 'Dr. Bērziņš', '2026-09-06', '2026-10-06', 'Aktīva'),
('R-2026-0007', 'Testa Pacients 7', 'Dr. Ozola', '2026-09-07', '2026-10-07', 'Aktīva'),
('R-2026-0008', 'Testa Pacients 8', 'Dr. Kalniņš', '2026-09-08', '2026-10-08', 'Izmantota'),
('R-2026-0009', 'Testa Pacients 9', 'Dr. Liepa', '2026-09-09', '2026-10-09', 'Aktīva'),
('R-2026-0010', 'Testa Pacients 10', 'Dr. Jansons', '2026-09-10', '2026-10-10', 'Aktīva'),
('R-2026-0011', 'Testa Pacients 11', 'Dr. Bērziņš', '2026-09-11', '2026-10-11', 'Aktīva'),
('R-2026-0012', 'Testa Pacients 12', 'Dr. Ozola', '2026-09-12', '2026-10-12', 'Aktīva'),
('R-2026-0013', 'Testa Pacients 13', 'Dr. Kalniņš', '2026-09-13', '2026-10-13', 'Izmantota'),
('R-2026-0014', 'Testa Pacients 14', 'Dr. Liepa', '2026-09-14', '2026-10-14', 'Aktīva'),
('R-2026-0015', 'Testa Pacients 15', 'Dr. Jansons', '2026-09-15', '2026-10-15', 'Aktīva');

INSERT INTO prescription_items
(prescription_id, medicine_id, quantity)
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

INSERT INTO orders
(
    customer_name,
    customer_email,
    status,
    total
)
VALUES
('Klients 1', 'klients1@example.com', 'Jauns', 5.68),
('Klients 2', 'klients2@example.com', 'Apstrādē', 6.99),
('Klients 3', 'klients3@example.com', 'Gatavs', 10.20),
('Klients 4', 'klients4@example.com', 'Izsniegts', 4.89),
('Klients 5', 'klients5@example.com', 'Jauns', 12.69),
('Klients 6', 'klients6@example.com', 'Apstrādē', 8.90),
('Klients 7', 'klients7@example.com', 'Gatavs', 9.40),
('Klients 8', 'klients8@example.com', 'Izsniegts', 5.99),
('Klients 9', 'klients9@example.com', 'Jauns', 7.80),
('Klients 10', 'klients10@example.com', 'Atcelts', 4.99),
('Klients 11', 'klients11@example.com', 'Jauns', 9.38),
('Klients 12', 'klients12@example.com', 'Apstrādē', 11.48),
('Klients 13', 'klients13@example.com', 'Gatavs', 7.00),
('Klients 14', 'klients14@example.com', 'Izsniegts', 13.98),
('Klients 15', 'klients15@example.com', 'Jauns', 15.60);

INSERT INTO order_items
(order_id, medicine_id, quantity, unit_price)
VALUES
(1, 1, 1, 2.49),
(1, 2, 1, 3.19),
(2, 3, 1, 6.99),
(3, 4, 2, 5.10),
(4, 5, 1, 4.89),
(5, 6, 1, 5.49),
(5, 7, 1, 7.20),
(6, 8, 1, 8.90),
(7, 9, 1, 9.40),
(8, 11, 1, 5.99),
(9, 14, 1, 7.80),
(10, 15, 1, 4.99),
(11, 12, 2, 4.69),
(12, 1, 1, 2.49),
(12, 13, 1, 3.50),
(12, 6, 1, 5.49),
(13, 13, 2, 3.50),
(14, 3, 2, 6.99),
(15, 14, 2, 7.80);
<?php
// Подключение к базе данных
$host = '127.0.0.1';
$db   = 'bookshop';
$user = 'bookshop_user';
$pass = 'secret';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Создание таблиц
$sql = "
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    status ENUM('В наличии', 'Нет на складе', 'Снято с продажи') NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS book_author (
    book_id INT,
    author_id INT,
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (author_id) REFERENCES authors(id)
);
";

$pdo->exec($sql);

// Заполнение тестовыми данными
$categories = ['Фантастика', 'Детективы', 'Романы'];
foreach ($categories as $category) {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$category]);
}

$authors = ['Айзек Азимов', 'Артур Конан Дойл', 'Лев Толстой'];
foreach ($authors as $author) {
    $stmt = $pdo->prepare("INSERT INTO authors (name) VALUES (?)");
    $stmt->execute([$author]);
}

$books = [
    ['name' => 'Основание', 'price' => 500, 'category_id' => 1, 'status' => 'В наличии'],
    ['name' => 'Шерлок Холмс', 'price' => 450, 'category_id' => 2, 'status' => 'В наличии'],
    ['name' => 'Война и мир', 'price' => 600, 'category_id' => 3, 'status' => 'Нет на складе']
];

foreach ($books as $book) {
    $stmt = $pdo->prepare("INSERT INTO books (name, price, category_id, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$book['name'], $book['price'], $book['category_id'], $book['status']]);
}

$book_authors = [
    ['book_id' => '1', 'author_id' => '1'],
    ['book_id' => '1', 'author_id' => '2'],
    ['book_id' => '2', 'author_id' => '2'],
    ['book_id' => '3', 'author_id' => '3']

];

foreach ($book_authors as $book_author) {
    $stmt = $pdo->prepare("INSERT INTO book_author (book_id, author_id) VALUES (?, ?)");
    $stmt->execute([$book_author['book_id'], $book_author['author_id']]);
}

echo "Тестовые данные успешно загружены!";

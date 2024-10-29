<?php

$host = 'localhost';
$db = 'product';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO products (name, category, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $category, $price);
    $stmt->execute();
    $stmt->close();
}


$searchTerm = '';
$filterCategory = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}
if (isset($_GET['category'])) {
    $filterCategory = $_GET['category'];
}


$sql = "SELECT * FROM products WHERE name LIKE ? ";
$params = ["%$searchTerm%"];
if ($filterCategory) {
    $sql .= "AND category = ?";
    $params[] = $filterCategory;
}
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Управление каталогом товаров</h1>
    <div class="container-form">

        <form method="POST">
            <h2>Добавить товар</h2>
            <input type="text" name="name" placeholder="Название товара" required>
            <input type="text" name="category" placeholder="Категория" required>
            <input type="number" name="price" placeholder="Цена" step="0.01" required>
            <button type="submit" name="add_product">Добавить</button>
        </form>
        <div class="form2">
            <h2>Поиск товаров</h2>
            <form method="GET">
                <input type="text" name="search" placeholder="Поиск по названию"
                    value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="category">
                    <option value="">Все категории</option>
                    <option value="Канц-товары" <?php if ($filterCategory == 'Техника') echo 'selected'; ?>>Компьютерные
                        игры
                    </option>
                    <option value="Игрушки" <?php if ($filterCategory == 'Игрушки') echo 'selected'; ?>>Игрушки</option>
                    <option value="Техника" <?php if ($filterCategory == 'Техника') echo 'selected'; ?>>Техника</option>
                    <option value="Книги" <?php if ($filterCategory == 'Книги') echo 'selected'; ?>>Книги</option>
                    <option value="Манхва" <?php if ($filterCategory == 'Манхва') echo 'selected'; ?>>Манхва</option>
                </select>
                <button type="submit">Поиск</button>
            </form>
        </div>

    </div>

    <h2>Каталог товаров</h2>
    <div class="catalog">
        <?php while ($product = $result->fetch_assoc()): ?>
        <div class="product">
            <div class="photo">
                photo
            </div>
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p>Категория: <?php echo htmlspecialchars($product['category']); ?></p>
            <p>Цена: <?php echo htmlspecialchars($product['price']); ?>₽</p>
        </div>
        <?php endwhile; ?>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>

</html>
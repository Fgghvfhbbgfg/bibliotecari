<?php

// Подключение к базе данных
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'library_db';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Добавление книги
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $genre_id = $_POST['genre_id'];

    $sql = "INSERT INTO books (title, author, description, genre_id) VALUES ('$title', '$author', '$description', '$genre_id')";
    if ($conn->query($sql) === TRUE) {
        echo "Книга успешно добавлена";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
}

// Поиск книг
if (isset($_GET['search'])) {
    $search_term = $_GET['search_term'];
    $sql = "SELECT * FROM books WHERE title LIKE '%$search_term%' OR author LIKE '%$search_term%'";
    $result = $conn->query($sql);
}

// Удаление книги
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    $sql = "DELETE FROM books WHERE id=$book_id";
    if ($conn->query($sql) === TRUE) {
        echo "Книга успешно удалена";
    } else {
        echo "Ошибка при удалении книги: " . $conn->error;
    }
}

// Получение списка жанров
$genres = [];
$sql = "SELECT * FROM genres";
$result_genres = $conn->query($sql);
if ($result_genres->num_rows > 0) {
    while ($row = $result_genres->fetch_assoc()) {
        $genres[$row['id']] = $row['name'];
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Библиотека</title>
</head>
<body>
    <h1>Библиотека</h1>

    <!-- Форма добавления книги -->
    <h2>Добавить книгу</h2>
    <form method="post" action="">
        <input type="text" name="title" placeholder="Название" required><br>
        <input type="text" name="author" placeholder="Автор" required><br>
        <textarea name="description" placeholder="Описание"></textarea><br>
        <select name="genre_id">
            <?php foreach ($genres as $id => $name) : ?>
                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select><br>
        <input type="submit" name="add_book" value="Добавить">
    </form>

    <!-- Форма поиска книг -->
    <h2>Поиск книг</h2>
    <form method="get" action="">
        <input type="text" name="search_term" placeholder="Поиск по названию или автору">
        <input type="submit" name="search" value="Найти">
    </form>

    <!-- Вывод результатов поиска -->
    <?php if (isset($result) && $result->num_rows > 0) : ?>
        <h2>Результаты поиска:</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li>
                    <?php echo $row['title']; ?> - <?php echo $row['author']; ?>
                    <a href="?delete=<?php echo $row['id']; ?>">Удалить</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

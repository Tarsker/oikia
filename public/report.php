<?php
require_once '../config/config.php';
require_once '../core/db.php';
require_once '../core/functions.php';
require_once 'fpdf/fpdf.php'; // Incluye la biblioteca FPDF

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
check_session();
$user = get_logged_in_user();

$properties = execute_query("SELECT id, type, description, address FROM properties", []);
$grouped_expenses = [];

if (isset($_POST['export_pdf'])) {
    $property_id = $_POST['property_id'] ?? null;
    $month = $_POST['month'] ?? null;

    $stmt = get_expenses($property_id, $month);

    // Iniciar PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Reporte de Gastos', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Propiedad', 1);
    $pdf->Cell(40, 10, 'Nombre del Gasto', 1);
    $pdf->Cell(20, 10, 'Valor', 1);
    $pdf->Cell(40, 10, 'Persona', 1);
    $pdf->Cell(20, 10, 'Mes', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $total_value = 0;
    while ($row = $stmt->fetch_assoc()) {
        $property = $row['type'] . ' - ' . $row['description'] . ' - ' . $row['address'];
        $pdf->Cell(40, 10, $property, 1);
        $pdf->Cell(40, 10, $row['name'], 1);
        $pdf->Cell(20, 10, number_format($row['value'], 2), 1);
        $pdf->Cell(40, 10, $row['person'], 1);
        $pdf->Cell(20, 10, $row['month'], 1);
        $pdf->Ln();
        $total_value += $row['value'];
    }

    // Agregar total al final del PDF
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Total: ' . number_format($total_value, 2), 0, 1, 'R');

    $pdf->Output('D', 'reporte_gastos.pdf');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['export_pdf'])) {
    $property_id = $_POST['property_id'] ?? null;
    $month = $_POST['month'] ?? null;

    $stmt = get_expenses($property_id, $month);

    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $property = $row['type'] . ' - ' . $row['description'];
            $grouped_expenses[$property][] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gastos</title>
    <link rel="stylesheet" href="/oikia/assets/css/styles.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="month"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #4cae4c;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #337ab7;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .back-button:hover {
            background: #286090;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Reporte de Gastos</h2>
        <form action="report.php" method="post">
            <label for="property_id">Propiedad:</label>
            <select id="property_id" name="property_id">
                <option value="">Todas las Propiedades</option>
                <?php while ($row = $properties->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_POST['property_id']) && $_POST['property_id'] == $row['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['type'] . ' - ' . $row['description'] . ' - ' . $row['address']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="month">Mes:</label>
            <input type="month" id="month" name="month" value="<?= isset($_POST['month']) ? htmlspecialchars($_POST['month']) : '' ?>">
            <button type="submit">Generar Reporte</button>
            <button type="submit" name="export_pdf">Exportar a PDF</button>
        </form>
        <?php if ($grouped_expenses): ?>
            <?php foreach ($grouped_expenses as $property => $expenses): ?>
                <h3><?= htmlspecialchars($property) ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre del Gasto</th>
                            <th>Valor</th>
                            <th>Persona</th>
                            <th>Mes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= htmlspecialchars($expense['name']) ?></td>
                                <td><?= format_currency($expense['value']) ?></td>
                                <td><?= htmlspecialchars($expense['person']) ?></td>
                                <td><?= htmlspecialchars($expense['month']) ?></td>
                            </tr>
                            <?php $total += $expense['value']; ?>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="total">Total</td>
                            <td class="total"><?= format_currency($total) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back-button">Regresar al Menú</a>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.onkeydown = function(e) {
            if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I") || (e.ctrlKey && e.key === "U")) {
                e.preventDefault();
                return false;
            }
        };
    </script>
</body>
</html>
